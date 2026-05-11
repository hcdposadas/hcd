<?php

namespace App\Controller;

use App\Form\EditarGiro;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\ProyectoBAE;
use App\Entity\GirosDestino;
use App\Entity\Comision;
use App\Entity\TipoProyecto;
use App\Entity\Dictamen;
use App\Entity\Expediente;
use App\Entity\AreaAdministrativa;
use App\Entity\GiroAdministrativo;
use App\Entity\Giro;
use App\Entity\Sesion;
use App\Form\CrearDictamenType;
use App\Form\CrearDictamenComisionType;
use App\Form\FirmaDictamenType;
use Knp\Snappy\Pdf;
use Symfony\Component\HttpFoundation\Response;
use App\Form\FirmaBAEType;
use App\Form\PedidoType;
use App\Form\DictamenType;
use App\Form\InformeDigestoType;
use App\Form\AsignacionType;
use Symfony\Component\HttpFoundation\BinaryFileResponse;


class ComisionController extends AbstractController
{
    /**
     * @Route("/comision", name="comision")
     */
    public function index(PaginatorInterface $paginator, Request $request)
    {
        // Temporal: aumentar límite de memoria para debugging
        ini_set('memory_limit', '512M');

        $em = $this->getDoctrine()->getManager();
        $em->getConfiguration()->setSQLLogger(null); // Desactivar SQL logger para ahorrar memoria

        // Obtener ID del usuario actual sin cargar toda la entidad
        $userId = $this->getUser()->getId();

        // Obtener comisiones del usuario usando una consulta DQL optimizada
        $comisionesData = $em->createQuery('
            SELECT c.id, c.peso, c.nombre
            FROM App\Entity\Comision c
            JOIN App\Entity\CargoPersona cp WITH cp.comision = c
            JOIN cp.persona p
            JOIN App\Entity\Usuario u WITH u.persona = p
            WHERE u.id = :userId
        ')
            ->setParameter('userId', $userId)
            ->getResult();

        if (!$comisionesData) {
            throw $this->createNotFoundException('No se encontró comisión para el usuario');
        }

        $comisionIds = array_values(array_unique(array_map(function ($comision) {
            return $comision['id'];
        }, $comisionesData)));

        $comisionesHabilitadas = [];
        $comisionesObras = [];
        $habilitado = false;
        $esObras = false;
        foreach ($comisionesData as $comisionData) {
            if (!$comisionData['peso'] || $userId == $comisionData['peso']) {
                $comisionesHabilitadas[] = $comisionData['id'];
                $habilitado = true;
            }

            if (strpos(strtolower($comisionData['nombre']), 'obras') !== false) {
                $comisionesObras[] = $comisionData['id'];
                $esObras = true;
            }
        }

        // Construir la consulta base con QueryBuilder - Solo el último giro por expediente y comisión
        $qb = $em->getRepository(Giro::class)->createQueryBuilder('gd');
        $qb->select('gd, cd, pb, e, ed, pl, pld')  // Solo cargar las entidades necesarias
            ->join('gd.comisionDestino', 'cd')
            ->leftJoin('gd.proyectoBae', 'pb')
            ->leftJoin('pb.expediente', 'e')
            ->leftJoin('gd.expediente', 'ed')
            ->leftJoin('e.periodoLegislativo', 'pl')
            ->leftJoin('ed.periodoLegislativo', 'pld')
            ->where('gd.comisionDestino IN (:comisionIds)')
            ->andWhere('(pb.id IS NOT NULL OR ed.id IS NOT NULL)')
            ->andWhere('NOT EXISTS (
               SELECT g2.id
               FROM App\Entity\Giro g2
               LEFT JOIN g2.proyectoBae pb2
               LEFT JOIN pb2.expediente e2
               LEFT JOIN g2.expediente ed2
               WHERE g2.comisionDestino = gd.comisionDestino
               AND g2.id > gd.id
               AND COALESCE(e2.id, ed2.id) = COALESCE(e.id, ed.id)
           )')
            ->setParameter('comisionIds', $comisionIds)
            ->orderBy('gd.id', 'DESC');

        // Filtros
        $numero = $request->query->get('numero');
        $letra = $request->query->get('letra');
        $anio = $request->query->get('anio');
        $fecha = $request->query->get('fecha');
        $estadoGiro = $request->query->get('estado_giro', 'todos');

        // Determinar los filtros basados en el estado seleccionado
        $soloCabecera = false;
        $enTratamiento = false;
        $finalizados = false;

        switch ($estadoGiro) {
            case 'solo_cabecera':
                $soloCabecera = true;
                break;
            case 'en_tratamiento':
                $soloCabecera = true;
                $enTratamiento = true;
                break;
            case 'finalizados':
                $soloCabecera = true;
                $finalizados = true;
                break;
        }

        // Debug
        error_log('Filtros: numero=' . $numero . ', letra=' . $letra . ', anio=' . $anio .
            ', fecha=' . $fecha . ', estadoGiro=' . $estadoGiro);

        if ($numero) {
            $qb->andWhere('(e.expediente = :numero OR ed.expediente = :numero)')
                ->setParameter('numero', $numero);
        }

        if ($letra) {
            $qb->andWhere('(e.letra = :letra OR ed.letra = :letra)')
                ->setParameter('letra', $letra);
        }

        if ($anio) {
            $qb->andWhere('(pl.anio = :anio OR pld.anio = :anio OR (pl.anio IS NULL AND e.anio = :anio) OR (pld.anio IS NULL AND ed.anio = :anio))')
                ->setParameter('anio', $anio);
        }

        if ($fecha) {
            $qb->andWhere('(DATE(e.fecha) = :fecha OR DATE(ed.fecha) = :fecha)')
                ->setParameter('fecha', $fecha);
        }

        if ($soloCabecera) {
            $qb->andWhere('gd.cabecera = true');
        }

        // Los filtros en_tratamiento y finalizados se aplicarán después

        // Si está activo en_tratamiento o finalizados, necesitamos obtener todos los resultados primero
        if ($enTratamiento || $finalizados) {
            // Obtener todos los giros sin paginar para poder filtrar
            $allGiros = $qb->getQuery()->getResult();

            // Filtrar por último giro administrativo
            $girosToShow = [];
            foreach ($allGiros as $giro) {
                $expediente = $giro->getProyectoBae() ? $giro->getProyectoBae()->getExpediente() : $giro->getExpediente();
                if ($expediente) {
                    $expedienteId = $expediente->getId();
                    $ultimoGiro = $em->getRepository(GiroAdministrativo::class)
                        ->createQueryBuilder('ga')
                        ->select('ad.nombre')
                        ->join('ga.areaDestino', 'ad')
                        ->where('ga.expediente = :expedienteId')
                        ->setParameter('expedienteId', $expedienteId)
                        ->orderBy('ga.id', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    $ultimoGiroNombre = $ultimoGiro ? $ultimoGiro['nombre'] : null;

                    // Verificar si el último giro fue a archivo o finalizado
                    $esArchivoOFinalizado = false;
                    if ($ultimoGiroNombre) {
                        $nombreLower = strtolower($ultimoGiroNombre);
                        if (strpos($nombreLower, 'archivo') !== false || strpos($nombreLower, 'finalizado') !== false) {
                            $esArchivoOFinalizado = true;
                        }
                    }

                    // Aplicar el filtro correspondiente
                    if ($enTratamiento && !$esArchivoOFinalizado) {
                        // En tratamiento: mostrar solo los que NO están en archivo o finalizado
                        $girosToShow[] = $giro;
                    } elseif ($finalizados && $esArchivoOFinalizado) {
                        // Finalizados: mostrar solo los que SÍ están en archivo o finalizado
                        $girosToShow[] = $giro;
                    }
                }
            }

            // Paginar los resultados filtrados
            $giros = $paginator->paginate(
                $girosToShow,
                $request->query->get('page', 1),
                10
            );
        } else {
            // Si no hay filtros especiales, paginar normalmente
            $query = $qb->getQuery();
            $giros = $paginator->paginate(
                $query,
                $request->query->get('page', 1),
                10
            );
        }

        // Obtener el último giro administrativo para cada expediente paginado
        $ultimosGiros = [];

        // Si no se procesaron filtros especiales arriba, obtener los últimos giros ahora
        if (!$enTratamiento && !$finalizados) {
            foreach ($giros as $giro) {
                $expediente = $giro->getProyectoBae() ? $giro->getProyectoBae()->getExpediente() : $giro->getExpediente();
                if ($expediente) {
                    $expedienteId = $expediente->getId();
                    $ultimoGiro = $em->getRepository(GiroAdministrativo::class)
                        ->createQueryBuilder('ga')
                        ->select('ad.nombre')
                        ->join('ga.areaDestino', 'ad')
                        ->where('ga.expediente = :expedienteId')
                        ->setParameter('expedienteId', $expedienteId)
                        ->orderBy('ga.id', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    $ultimoGiroNombre = $ultimoGiro ? $ultimoGiro['nombre'] : null;
                    $ultimosGiros[$expedienteId] = $ultimoGiroNombre;
                }
            }
        } else {
            // Si se procesaron filtros especiales, obtener los últimos giros de los resultados paginados
            foreach ($giros as $giro) {
                $expediente = $giro->getProyectoBae() ? $giro->getProyectoBae()->getExpediente() : $giro->getExpediente();
                if ($expediente) {
                    $expedienteId = $expediente->getId();
                    $ultimoGiro = $em->getRepository(GiroAdministrativo::class)
                        ->createQueryBuilder('ga')
                        ->select('ad.nombre')
                        ->join('ga.areaDestino', 'ad')
                        ->where('ga.expediente = :expedienteId')
                        ->setParameter('expedienteId', $expedienteId)
                        ->orderBy('ga.id', 'DESC')
                        ->setMaxResults(1)
                        ->getQuery()
                        ->getOneOrNullResult();

                    $ultimoGiroNombre = $ultimoGiro ? $ultimoGiro['nombre'] : null;
                    $ultimosGiros[$expedienteId] = $ultimoGiroNombre;
                }
            }
        }

        return $this->render('comision/index.html.twig', [
            'controller_name' => 'ComisionController',
            'giros' => $giros,
            'habilitado' => $habilitado,
            'numero' => $numero,
            'letra' => $letra,
            'anio' => $anio,
            'fecha' => $fecha,
            'estado_giro' => $estadoGiro,
            'solo_cabecera' => $soloCabecera,
            'en_tratamiento' => $enTratamiento,
            'finalizados' => $finalizados,
            'es_obras' => $esObras,
            'comisiones_habilitadas' => array_values(array_unique($comisionesHabilitadas)),
            'comisiones_obras' => array_values(array_unique($comisionesObras)),
            'ultimosGiros' => $ultimosGiros
        ]);

        // Nunca se ejecutará; pero si decidimos usar JsonResponse u otra cosa podemos
        // liberar la relación pesada de Persona antes de que Symfony serialice el token.
        // Mantengo el código comentado por si se requiere en otros métodos.
        /*
        $user = $this->getUser();
        if ($user) {
            // No queremos que se serialice toda la gráfica de Persona -> CargoPersona -> …
            $user->setPersona(null);
        }
        */
    }

    /**
     * @Route("/comision/girar-finalizado/{id}", name="comision_girar_finalizado")
     */
    public function girarFinalizado($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Verificar que el usuario pertenece a la comisión de obras
        $comision = $this->getUser()->getPersona()->getCargoPersona()->first()->getComision();
        if (!$comision || strpos(strtolower($comision->getNombre()), 'obras') === false) {
            $this->addFlash('error', 'No tiene permisos para realizar esta acción.');
            return $this->redirectToRoute('comision_index');
        }

        // Obtener el giro
        $giro = $em->getRepository(Giro::class)->find($id);
        if (!$giro || !$giro->getCabecera()) {
            $this->addFlash('error', 'Giro no encontrado o no es cabecera.');
            return $this->redirectToRoute('comision_index');
        }

        $expediente = $giro->getProyectoBae()->getExpediente();

        // Buscar el área "Finalizado"
        $areaFinalizado = $em->getRepository(AreaAdministrativa::class)
            ->createQueryBuilder('a')
            ->where('LOWER(a.nombre) LIKE :nombre')
            ->setParameter('nombre', '%finalizado%')
            ->getQuery()
            ->getOneOrNullResult();

        if (!$areaFinalizado) {
            $this->addFlash('error', 'No se encontró el área "Finalizado".');
            return $this->redirectToRoute('comision_index');
        }

        // Buscar un área administrativa genérica o la primera disponible
        $areaOrigen = $em->getRepository(AreaAdministrativa::class)
            ->createQueryBuilder('a')
            ->where('LOWER(a.nombre) LIKE :comisiones OR LOWER(a.nombre) LIKE :hcd OR LOWER(a.nombre) LIKE :concejo')
            ->setParameter('comisiones', '%comision%')
            ->setParameter('hcd', '%hcd%')
            ->setParameter('concejo', '%concejo%')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$areaOrigen) {
            // Si no se encuentra ninguna, usar la primera área administrativa disponible
            $areaOrigen = $em->getRepository(AreaAdministrativa::class)->findOneBy([]);
        }

        // Crear el giro administrativo
        $giroAdministrativo = new GiroAdministrativo();
        $giroAdministrativo->setFechaGiro(new \DateTime());
        if ($areaOrigen) {
            $giroAdministrativo->setAreaOrigen($areaOrigen);
        }
        $giroAdministrativo->setAreaDestino($areaFinalizado);
        $giroAdministrativo->setExpediente($expediente);
        $giroAdministrativo->setTexto('Expediente finalizado desde la comisión de ' . $comision->getNombre());
        $giroAdministrativo->setEstado('FINALIZADO');

        $em->persist($giroAdministrativo);
        $em->flush();

        $this->addFlash('success', 'El expediente ha sido girado a Finalizado exitosamente.');

        return $this->redirectToRoute('comision_index');
    }

    public function misDictamenes(PaginatorInterface $paginator, Request $request)
    {

        $esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();


        $comision = $esPresidenteComision->getComision();

        if (!$comision) {

        }


        $em = $this->getDoctrine()->getManager();
        $dictamenes = $em->getRepository(Dictamen::class)
            ->findByPresidenteComision($esPresidenteComision);

        $dictamenes = $paginator->paginate(
            $dictamenes,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/dictamenes.html.twig', [
            'controller_name' => 'ComisionController',
            'dictamenes' => $dictamenes
        ]);
    }


    public function newdictamen(Request $request, $id)
    {
        if ($this->isGranted('ROLE_CONCEJAL')) {
            $this->addFlash('warning', 'No tiene permisos para crear un Dictamen.');

            return $this->redirectToRoute('comision_index');
        }

        $esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();

        $expediente = $this->getDoctrine()->getRepository(Expediente::class)->find($id);


        $dictamen = new Dictamen();

        $form = $this->createForm(CrearDictamenComisionType::class, $dictamen);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $dictamen->setExpediente($expediente);

            $dictamen->setPresidenteComision($esPresidenteComision);

            $adjuntos = $form->get("expedientesAdjunto")->getData();

            foreach ($adjuntos as $adjunto) {

                $expediente->addExpedientesAdjunto($adjunto);
            }

            $proveidos = $form->get('proveidos')->getData();

            foreach ($proveidos as $proveido) {
                $proveido->setExpediente($expediente);


                $em->persist($proveido);


            }


            $em->persist($dictamen);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->redirectToRoute('dictamen_index');
        }

        return $this->render('dictamen/crear.html.twig',
            [
                'form' => $form->createView()
            ]);
    }


    public function editdictamen(Request $request, $id)
    {

        //$esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();

        $esPresidenteComision = true;

        $expediente = $this->getDoctrine()->getRepository(Expediente::class)->find($id);


        $dictamen = new Dictamen();

        $form = $this->createForm(CrearDictamenComisionType::class, $dictamen);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $dictamen->setExpediente($expediente);

            $em->persist($dictamen);
            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->redirectToRoute('dictamen_index');
        }

        return $this->render('dictamen/crear.html.twig',
            [
                'form' => $form->createView()
            ]);
    }


    public function cargarGiroSecretaria(ProyectoBae $id)
    {

        $giros = $comision->getGiros()->slice(0, 100);

        var_dump($giros);
        die();


        return $this->render('comision/index.html.twig', [
            'controller_name' => 'ComisionController',
        ]);
    }

    public function showDictamen(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $dictamen = $em->getRepository(Dictamen::class)->find($id);

        $form = $this->createForm(FirmaDictamenType::class, $dictamen);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Dictamen creado correctamente'
            );

            return $this->render('comision/showDictamen.html.twig',
                [
                    'form' => $form->createView(),
                    'dictamen' => $dictamen
                ]);
        }
        return $this->render('comision/showDictamen.html.twig',
            [
                'form' => $form->createView(),
                'dictamen' => $dictamen
            ]);
    }

    public function showProyectoComision(Request $request, Giro $giro)
    {
        $giro->setVisto(true);
        $expediente = $giro->getProyectoBae() ? $giro->getProyectoBae()->getExpediente() : $giro->getExpediente();

        if (!$expediente) {
            throw $this->createNotFoundException('No se encontró expediente para el giro');
        }


        return $this->render(
            'comision/showProyecto.html.twig',
            [
                'expediente' => $expediente,
            ]
        );
    }

    public function indexProyectosBae(PaginatorInterface $paginator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();


        $proyectosBae = $em->getRepository(ProyectoBae::class)->findBy(
            ['tratamientoSobretabla' => false],
            ['fechaCreacion' => 'DESC'],
            10
        );

        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/proyectosBae.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae
        ]);
    }

    public function indexGiros(PaginatorInterface $paginator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();
        $numero = trim((string) $request->query->get('numero', ''));

        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from(ProyectoBae::class, 'p')
            ->join('p.expediente', 'e')
            ->where(
                $qb->expr()->orX(
                    $qb->expr()->isNull('p.tratamientoSobretabla'),
                    $qb->expr()->eq('p.tratamientoSobretabla', ':false')
                )

            )
            ->setParameter('false', false)
            ->orderBy('p.id', 'DESC');

        if ($numero !== '') {
            $qb->andWhere('e.expediente = :numero')
                ->setParameter('numero', $numero);
        }

        $proyectosBae = $qb->getQuery()->getResult();

        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );
        return $this->render('comision/indexGiros.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae,
            'numero' => $numero,
        ]);
    }

    public function indexPedidoInforme(PaginatorInterface $paginator, Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $tipoProyecto = $em->getRepository(TipoProyecto::class)->findOneBySlug('ordenanza');

        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from(ProyectoBae::class, 'p')
            ->join('p.expediente', 'e')
            ->where($qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->isNull('p.tratamientoSobretabla'),
                    $qb->expr()->eq('p.tratamientoSobretabla', ':false')
                ),
                $qb->expr()->orX(
                    $qb->expr()->isNull('p.esInformeDem'),
                    $qb->expr()->eq('p.esInformeDem', ':false')
                )
            ))
            ->setParameter('false', false)
            ->andWhere('e.tipoProyecto = :tipoProyecto')
            ->setParameter('tipoProyecto', $tipoProyecto->getId())
            ->orderBy('p.id', 'DESC');

        $proyectosBae = $qb->getQuery()->getResult();

        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );
        return $this->render('comision/indexPedidos.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae
        ]);

    }

    public function indexInformesDigesto(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $tipoProyecto = $em->getRepository(TipoProyecto::class)->findOneBySlug('ordenanza');

        $qb = $em->createQueryBuilder();

        $qb->select('p')
            ->from(ProyectoBae::class, 'p')
            ->join('p.expediente', 'e')
            ->where($qb->expr()->andX(
                $qb->expr()->orX(
                    $qb->expr()->isNull('p.tratamientoSobretabla'),
                    $qb->expr()->eq('p.tratamientoSobretabla', ':false')
                ),
                $qb->expr()->orX(
                    $qb->expr()->isNull('p.esInformeDem'),
                    $qb->expr()->eq('p.esInformeDem', ':false')
                )
            ))
            ->setParameter('false', false)
            ->andWhere('e.tipoProyecto = :tipoProyecto')
            ->setParameter('tipoProyecto', $tipoProyecto->getId())
            ->orderBy('p.id', 'DESC');

        $proyectosBae = $qb->getQuery()->getResult();

        $proyectosBae = $paginator->paginate(
            $proyectosBae,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/indexInformes.html.twig', [
            'controller_name' => 'ComisionController',
            'proyectos' => $proyectosBae
        ]);
    }

    public function showPedido(Request $request, ProyectoBAE $proyectoBae): Response
    {
        $em = $this->getDoctrine()->getManager();

        $expediente = $proyectoBae->getExpediente();

        $form = $this->createForm(PedidoType::class, $proyectoBae);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Pedido firmado correctamente'
            );

            return $this->render(
                'comision/pedido.html.twig',
                [
                    'form' => $form->createView(),
                    'proyectobae' => $proyectoBae,
                    'expediente' => $expediente,
                ]
            );
        }

        return $this->render(
            'comision/pedido.html.twig',
            [
                'form' => $form->createView(),
                'proyectobae' => $proyectoBae,
                'expediente' => $expediente,
            ]
        );
    }

    public function showInforme(Request $request, ProyectoBAE $proyectoBae): Response
    {
        $em = $this->getDoctrine()->getManager();

        $expediente = $proyectoBae->getExpediente();

        $form = $this->createForm(InformeDigestoType::class, $proyectoBae);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Informe subido correctamente'
            );

            return $this->render(
                'comision/digesto.html.twig',
                [
                    'form' => $form->createView(),
                    'proyectobae' => $proyectoBae,
                    'expediente' => $expediente,
                ]
            );
        }

        return $this->render(
            'comision/digesto.html.twig',
            [
                'form' => $form->createView(),
                'proyectobae' => $proyectoBae,
                'expediente' => $expediente,
            ]
        );
    }

    public function showAsignacion(Request $request, Dictamen $dictamen): Response
    {
        $em = $this->getDoctrine()->getManager();


        $form = $this->createForm(AsignacionType::class, $dictamen);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Asiganacion guardada correctamente'
            );

            return $this->render('comision/asignacion.html.twig',
                [
                    'form' => $form->createView(),
                    'dictamen' => $dictamen
                ]);
        }
        return $this->render('comision/asignacion.html.twig',
            [
                'form' => $form->createView(),
                'dictamen' => $dictamen
            ]);
    }

    public function showProyectoBae(Request $request, ProyectoBAE $proyectoBae): Response
    {
        $em = $this->getDoctrine()->getManager();

        $expediente = $proyectoBae->getExpediente();

        $form = $this->createForm(FirmaBAEType::class, $proyectoBae);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash(
                'success',
                'Giro firmado correctamente'
            );

            return $this->render(
                'comision/firmar.html.twig',
                [
                    'form' => $form->createView(),
                    'proyectobae' => $proyectoBae,
                    'expediente' => $expediente,
                ]
            );
        }

        return $this->render(
            'comision/firmar.html.twig',
            [
                'form' => $form->createView(),
                'proyectobae' => $proyectoBae,
                'expediente' => $expediente,
            ]
        );
    }

    public function imprimirGiros(Pdf $knpSnappyPdf, Request $request, ProyectoBae $id)
    {
        $giros = $id->getGirosOrdenados();
        $expediente = $id->getExpediente();
        $sesion = $id->getBoletinAsuntoEntrado()->getSesion();

        $titulo = "Giro " . $expediente->getExpediente() . "-" . $expediente->getLetra() . "-" . $expediente->getPeriodoLegislativo()->getAnio();
        $fecha = $sesion->getFecha();


        $html = $this->renderView(
            'comision/giroComision.pdf.twig',
            [
                'expediente' => $expediente,
                'sesion' => $sesion,
                'title' => $titulo,
                'giros' => $giros,
                'fecha' => $fecha,

            ]
        );

        return new Response(
            $knpSnappyPdf->getOutputFromHtml(
                $html,
                array(
                    'page-size' => 'Legal',
                    'margin-left' => "2cm",
                    'margin-right' => "3cm",
                    'margin-top' => "3cm",
                    //                    'margin-bottom' => "1cm"
                )
            ),
            200,
            array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $titulo . '.pdf"'
            )
        );
    }

    public function indexDictamenesOrd(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $tipoProyecto = $em->getRepository(TipoProyecto::class)->findOneBySlug('ordenanza');

        $dictamenes = $em->getRepository(Dictamen::class)
            ->createQueryBuilder('d')
            ->where('d.tipoProyecto = :tipoProyecto')
            ->orderBy('d.id', 'DESC')
            ->setParameter('tipoProyecto', $tipoProyecto->getId())
            ->getQuery()
            ->getResult();

        $dictamenes = $paginator->paginate(
            $dictamenes,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/indexAsignaciones.html.twig', [
            'controller_name' => 'ComisionController',
            'dictamenes' => $dictamenes
        ]);
    }


    public function expedientesAsesores(PaginatorInterface $paginator, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $areaAdministrativa = $em->getRepository(AreaAdministrativa::class)->find(37);
        $areaAdministrativa2 = $em->getRepository(AreaAdministrativa::class)->find(44);


        $qb = $em->createQueryBuilder();
        $qb->select('g')
            ->from(GiroAdministrativo::class, 'g')
            ->where($qb->expr()->orX(
                $qb->expr()->eq('g.areaOrigen', ':areaAdministrativa'),
                $qb->expr()->eq('g.areaDestino', ':areaAdministrativa')
            ));
        $qb->orWhere($qb->expr()->orX(
            $qb->expr()->eq('g.areaOrigen', ':areaAdministrativa2'),
            $qb->expr()->eq('g.areaDestino', ':areaAdministrativa2')
        ));
        $qb->setParameter('areaAdministrativa', $areaAdministrativa);
        $qb->setParameter('areaAdministrativa2', $areaAdministrativa2);
        $qb->orderBy('g.id', 'DESC');
        $giros = $qb->getQuery()->getResult();

        $giros = $paginator->paginate(
            $giros,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('comision/showAsesor.html.twig', [
            'giros' => $giros
        ]);

    }

    public function showAsesor(GiroAdministrativo $id)
    {
        $giro = $id;
        $expediente = $giro->getExpediente();


        $em = $this->getDoctrine()->getManager();
        $rechazar = true;
        if ($giro->getEstado() == 'pendiente' or $giro->getEstado() == null) {
            $giro->setEstado('abierto');
        }
        $em->flush();
        return $this->render(
            'comision/showGiroAsesor.html.twig',
            ['giro' => $giro,
                'expediente' => $expediente,
            ]
        );
    }

    public function editGiro(Expediente $expediente, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $girosBae = $em->getRepository(ProyectoBAE::class)->findByExpedienteTimeline($expediente);
        $girosTimeline = [];

        foreach ($girosBae as $giroBae) {
            foreach ($giroBae->getGiros() as $itemGiro) {
                $girosTimeline[] = [
                    'giro' => $itemGiro,
                    'tipo' => 'comision',
                    'sesion' => $giroBae->getBoletinAsuntoEntrado() ? $giroBae->getBoletinAsuntoEntrado()->getSesion() : null,
                    'origen' => 'bae',
                ];
            }
        }

        foreach ($expediente->getGiros() as $giro) {
            $girosTimeline[] = [
                'giro' => $giro,
                'tipo' => 'comision',
                'sesion' => null,
                'origen' => 'expediente',
            ];
        }

        foreach ($expediente->getGiroAdministrativos() as $giroAdministrativo) {
            $girosTimeline[] = [
                'giro' => $giroAdministrativo,
                'tipo' => 'administrativo',
                'sesion' => null,
                'origen' => 'administrativo',
            ];
        }

        usort($girosTimeline, function ($a, $b) {
            $fechaA = $a['giro']->getFechaGiro() ?: $a['giro']->getFechaCreacion();
            $fechaB = $b['giro']->getFechaGiro() ?: $b['giro']->getFechaCreacion();

            if ($fechaA == $fechaB) {
                return 0;
            }

            return $fechaA < $fechaB ? -1 : 1;
        });

        $form = $this->createForm(EditarGiro::class, $expediente);

        $girosAComisionOriginal = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ( $expediente->getGiros() as $giro ) {
            $girosAComisionOriginal->add( $giro );
        }

        $form->handleRequest( $request );

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Sesion|null $sesion */
            $sesion = $form->get('sesion')->getData();

            foreach ( $girosAComisionOriginal as $giro ) {
                if ( false === $expediente->getGiros()->contains( $giro ) ) {
                    $giro->setExpediente( null );
                    $em->remove( $giro );
                }
            }

            if ($sesion) {
                $bae = $sesion->getBae()->last();

                if (!$bae) {
                    $this->addFlash('warning', 'La sesión seleccionada no tiene BAE asociado.');

                    return $this->redirectToRoute('comision_edit_giros', [
                        'id' => $expediente->getId(),
                    ]);
                }

                $proyectoBae = $em->getRepository(ProyectoBAE::class)->findOneBy([
                    'expediente' => $expediente,
                    'boletinAsuntoEntrado' => $bae,
                ]);

                if (!$proyectoBae) {
                    $proyectoBae = new ProyectoBAE();
                }

                $proyectoBae->setExpediente($expediente);
                $proyectoBae->setBoletinAsuntoEntrado($bae);
                $proyectoBae->setIncorporadoEnSesion(true);
                $proyectoBae->setExtracto($expediente->getExtracto());

                foreach ($proyectoBae->getGiros() as $giroExistente) {
                    $giroExistente->setProyectoBae(null);
                    $em->remove($giroExistente);
                }

                foreach ($expediente->getGiros()->toArray() as $giro) {
                    $expediente->removeGiro($giro);
                    $giro->setExpediente(null);
                    $proyectoBae->addGiro($giro);
                    $em->persist($giro);
                }

                $em->persist($proyectoBae);
            } else {
                foreach ($expediente->getGiros() as $giro) {
                    $giro->setProyectoBae(null);
                    $giro->setExpediente($expediente);
                    if (!$giro->getId()) {
                        $giro->setCreadoPor($this->getUser());
                    }
                    $em->persist($giro);
                }
            }

            $em->flush();

            $this->addFlash('success', 'Los giros del expediente fueron actualizados.');

            return $this->redirectToRoute('comision_edit_giros', [
                'id' => $expediente->getId(),
            ]);
        }

        return $this->render(
            'comision/editGiroProyecto.html.twig',
            [
                'expediente' => $expediente,
                'edit_form' => $form->createView(),
                'giros' => $expediente->getGirosOrdenados(),
                'giros_timeline' => $girosTimeline,
            ]
        );
    }

}
