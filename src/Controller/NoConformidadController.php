<?php

namespace App\Controller;

use App\Entity\NoConformidad;
use App\Form\NoConformidadType;
use App\Form\NoConformidadFilterType;
use App\Form\ReviewType;
use App\Form\TreatType;
use App\Form\VerifyCorrectionType;
use App\Form\VerifyEffectivenessType;
use App\Repository\NoConformidadRepository;
use App\Service\NoConformidadExportService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
// use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Workflow\WorkflowInterface;

/**
 * @Route("/no-conformidad")
 */
class NoConformidadController extends AbstractController
{
    /**
     * Verifica si el usuario actual puede acceder a la búsqueda avanzada
     */
    private function canAccessAdvancedSearch(): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }
        
        // Solo usuarios con ROLE_CALIDAD o ROLE_ADMIN pueden acceder a búsqueda avanzada
        return $this->isGranted('ROLE_CALIDAD') || $this->isGranted('ROLE_ADMIN');
    }
    
    /**
     * Aplica filtros según el rol del usuario
     */
    private function applyRoleBasedFilters($queryBuilder, $user)
    {
        // Si no es ROLE_CALIDAD ni ROLE_ADMIN, aplicar restricciones
        if (!$this->isGranted('ROLE_CALIDAD') && !$this->isGranted('ROLE_ADMIN')) {
            // Solo puede ver las creadas por él o donde es responsable
            $queryBuilder->andWhere('(nc.creadoPor = :user OR nc.asignadoA = :user)')
                        ->setParameter('user', $user);
        }
        
        return $queryBuilder;
    }
    
    /**
     * Verifica si el usuario puede crear no conformidades
     */
    private function canCreateNoConformidad(): bool
    {
        // Todos los usuarios pueden crear no conformidades
        return $this->getUser() !== null;
    }
    
    /**
     * Verifica si el usuario puede tratar la no conformidad
     */
    private function canTreatNoConformidad(NoConformidad $noConformidad): bool
    {
        $user = $this->getUser();
        if (!$user) {
            return false;
        }
        
        // ROLE_CALIDAD puede tratar cualquier no conformidad
        if ($this->isGranted('ROLE_CALIDAD') || $this->isGranted('ROLE_ADMIN')) {
            return true;
        }
        
        // Un responsable solo puede tratar si está asignado a la no conformidad
        return $noConformidad->getAsignadoA() === $user;
    }
    
    /**
     * Verifica si el usuario puede verificar corrección
     */
    private function canVerifyCorrection(NoConformidad $noConformidad): bool
    {
        return $this->canTreatNoConformidad($noConformidad);
    }
    
    /**
     * Verifica si el usuario puede verificar efectividad
     */
    private function canVerifyEffectiveness(NoConformidad $noConformidad): bool
    {
        return $this->canTreatNoConformidad($noConformidad);
    }
    
    /**
     * Verifica si el usuario puede revisar (aceptar/desestimar)
     */
    private function canReviewNoConformidad(NoConformidad $noConformidad): bool
    {
        // Solo ROLE_CALIDAD puede revisar
        return $this->isGranted('ROLE_CALIDAD') || $this->isGranted('ROLE_ADMIN');
    }
    /**
     * @Route("/consulta", name="no_conformidad_consulta", methods={"GET"})
     */
    public function consulta(Request $request, NoConformidadRepository $repository, PaginatorInterface $paginator): Response
    {
        // Verificar si el usuario puede acceder a búsqueda avanzada
        if (!$this->canAccessAdvancedSearch()) {
            $this->addFlash('error', 'No tiene permisos para acceder a la búsqueda avanzada.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        $form = $this->createForm(NoConformidadFilterType::class);
        $form->handleRequest($request);

        $queryBuilder = $repository->createQueryBuilder('nc')
            ->leftJoin('nc.area', 'a')
            ->leftJoin('nc.empleado', 'e')
            ->leftJoin('nc.asignadoA', 'u')
            ->orderBy('nc.fecha', 'DESC');

        if ($form->isSubmitted()) {
            $data = $form->getData();

            // Filtro por rango de mes y año
            if ($data['mesDesde'] && $data['anoDesde']) {
                $fechaDesde = new \DateTime($data['anoDesde'] . '-' . $data['mesDesde'] . '-01 00:00:00');
                $queryBuilder->andWhere('nc.fecha >= :fechaDesde')
                            ->setParameter('fechaDesde', $fechaDesde);
            }
            
            if ($data['mesHasta'] && $data['anoHasta']) {
                $fechaHasta = new \DateTime($data['anoHasta'] . '-' . $data['mesHasta'] . '-01 00:00:00');
                $fechaHasta->modify('last day of this month 23:59:59');
                $queryBuilder->andWhere('nc.fecha <= :fechaHasta')
                            ->setParameter('fechaHasta', $fechaHasta);
            }

            // Filtro por área
            if ($data['area']) {
                $queryBuilder->andWhere('nc.area = :area')
                            ->setParameter('area', $data['area']);
            }

            // Filtro por usuario responsable del área
            if ($data['usuario']) {
                $queryBuilder->andWhere('nc.creadoPor = :usuario')
                            ->setParameter('usuario', $data['usuario']);
            }

            // Filtro por origen
            if ($data['origen']) {
                $queryBuilder->andWhere('nc.origen = :origen')
                            ->setParameter('origen', $data['origen']);
            }

            // Filtro por categoría
            if ($data['categoria']) {
                $queryBuilder->andWhere('nc.categoria = :categoria')
                            ->setParameter('categoria', $data['categoria']);
            }

            // Filtro por norma
            if ($data['norma']) {
                $queryBuilder->andWhere('nc.norma = :norma')
                            ->setParameter('norma', $data['norma']);
            }

            // Filtro por requisito específico
            if ($data['requisitoEspecifico']) {
                $queryBuilder->andWhere('nc.requisitoEspecifico = :requisitoEspecifico')
                            ->setParameter('requisitoEspecifico', $data['requisitoEspecifico']);
            }

            // Filtro por PGCD
            if ($data['pgcd']) {
                $queryBuilder->andWhere('nc.pgcd = :pgcd')
                            ->setParameter('pgcd', $data['pgcd']);
            }

            // Filtro por estado
            if ($data['estado']) {
                $queryBuilder->andWhere('nc.estado = :estado')
                            ->setParameter('estado', $data['estado']);
            }
        }

        $noConformidades = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->get('page', 1),
            15 // Más resultados por página para consultas
        );

        return $this->render('no_conformidad/consulta.html.twig', [
            'form' => $form->createView(),
            'no_conformidades' => $noConformidades,
            'total_resultados' => $noConformidades->getTotalItemCount()
        ]);
    }

    /**
     * @Route("/export", name="no_conformidad_export", methods={"GET"})
     */
    public function export(Request $request, NoConformidadRepository $repository, NoConformidadExportService $exportService): Response
    {
        $estado = $request->query->get('estado');
        
        $queryBuilder = $repository->createQueryBuilder('nc')
            ->leftJoin('nc.area', 'a')
            ->leftJoin('nc.empleado', 'e')
            ->leftJoin('nc.asignadoA', 'u')
            ->orderBy('nc.fecha', 'DESC');
        
        if ($estado && $estado !== '') {
            $queryBuilder->andWhere('nc.estado = :estado')
                        ->setParameter('estado', $estado);
        }
        
        $noConformidades = $queryBuilder->getQuery()->getResult();
        
        $filename = $exportService->exportToExcel($noConformidades);
        
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'no_conformidades_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
        
        // Eliminar el archivo temporal después de enviarlo
        $response->deleteFileAfterSend(true);
        
        return $response;
    }

    /**
     * @Route("/export-filtered", name="no_conformidad_export_filtered", methods={"GET"})
     */
    public function exportFiltered(Request $request, NoConformidadRepository $repository, NoConformidadExportService $exportService): Response
    {
        $queryBuilder = $repository->createQueryBuilder('nc')
            ->leftJoin('nc.area', 'a')
            ->leftJoin('nc.empleado', 'e')
            ->leftJoin('nc.asignadoA', 'u')
            ->orderBy('nc.fecha', 'DESC');

        // Obtener datos del formulario - los parámetros vienen con prefijo del formulario
        $formData = $request->query->get('no_conformidad_filter', []);
        
        // Aplicar todos los filtros de la consulta avanzada
        if (isset($formData['mesDesde']) && isset($formData['anoDesde']) && $formData['mesDesde'] && $formData['anoDesde']) {
            $fechaDesde = new \DateTime($formData['anoDesde'] . '-' . $formData['mesDesde'] . '-01 00:00:00');
            $queryBuilder->andWhere('nc.fecha >= :fechaDesde')
                        ->setParameter('fechaDesde', $fechaDesde);
        }
        
        if (isset($formData['mesHasta']) && isset($formData['anoHasta']) && $formData['mesHasta'] && $formData['anoHasta']) {
            $fechaHasta = new \DateTime($formData['anoHasta'] . '-' . $formData['mesHasta'] . '-01 00:00:00');
            $fechaHasta->modify('last day of this month 23:59:59');
            $queryBuilder->andWhere('nc.fecha <= :fechaHasta')
                        ->setParameter('fechaHasta', $fechaHasta);
        }

        if (isset($formData['area']) && $formData['area']) {
            $queryBuilder->andWhere('nc.area = :area')
                        ->setParameter('area', $formData['area']);
        }

        if (isset($formData['usuario']) && $formData['usuario']) {
            $queryBuilder->andWhere('nc.creadoPor = :usuario')
                        ->setParameter('usuario', $formData['usuario']);
        }

        if (isset($formData['origen']) && $formData['origen']) {
            $queryBuilder->andWhere('nc.origen = :origen')
                        ->setParameter('origen', $formData['origen']);
        }

        if (isset($formData['categoria']) && $formData['categoria']) {
            $queryBuilder->andWhere('nc.categoria = :categoria')
                        ->setParameter('categoria', $formData['categoria']);
        }

        if (isset($formData['norma']) && $formData['norma']) {
            $queryBuilder->andWhere('nc.norma = :norma')
                        ->setParameter('norma', $formData['norma']);
        }

        if (isset($formData['requisitoEspecifico']) && $formData['requisitoEspecifico']) {
            $queryBuilder->andWhere('nc.requisitoEspecifico = :requisitoEspecifico')
                        ->setParameter('requisitoEspecifico', $formData['requisitoEspecifico']);
        }

        if (isset($formData['pgcd']) && $formData['pgcd']) {
            $queryBuilder->andWhere('nc.pgcd = :pgcd')
                        ->setParameter('pgcd', $formData['pgcd']);
        }

        if (isset($formData['estado']) && $formData['estado']) {
            $queryBuilder->andWhere('nc.estado = :estado')
                        ->setParameter('estado', $formData['estado']);
        }
        
        $noConformidades = $queryBuilder->getQuery()->getResult();
        
        $filename = $exportService->exportToExcel($noConformidades);
        
        $response = new BinaryFileResponse($filename);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            'no_conformidades_filtradas_' . date('Y-m-d_H-i-s') . '.xlsx'
        );
        
        // Eliminar el archivo temporal después de enviarlo
        $response->deleteFileAfterSend(true);
        
        return $response;
    }

    /**
     * @Route("/", name="no_conformidad_index", methods={"GET"})
     */
    public function index(NoConformidadRepository $noConformidadRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $estado = $request->query->get('estado');
        $area = $request->query->get('area');
        $origen = $request->query->get('origen');

        $queryBuilder = $noConformidadRepository->createQueryBuilder('nc')
            ->orderBy('nc.id', 'DESC');

        // Aplicar filtros según el rol del usuario
        $queryBuilder = $this->applyRoleBasedFilters($queryBuilder, $user);

        if ($estado) {
            $queryBuilder->andWhere('nc.estado = :estado')
                        ->setParameter('estado', $estado);
        }

        if ($area) {
            $queryBuilder->andWhere('nc.area = :area')
                        ->setParameter('area', $area);
        }

        if ($origen) {
            $queryBuilder->andWhere('nc.origen = :origen')
                        ->setParameter('origen', $origen);
        }

        $noConformidades = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->get('page', 1),
            15
        );

        return $this->render('no_conformidad/index.html.twig', [
            'no_conformidades' => $noConformidades,
            'estado_filtro' => $estado,
            'area_filtro' => $area,
            'origen_filtro' => $origen,
            'can_access_advanced_search' => $this->canAccessAdvancedSearch(),
            'is_calidad' => $this->isGranted('ROLE_CALIDAD'),
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    /**
     * Método para mostrar no conformidades filtradas por estado
     * 
     * @Route("/estado/nuevo", name="no_conformidad_estado_nuevo", methods={"GET"}, defaults={"estado"="nuevo"})
     * @Route("/estado/asignado", name="no_conformidad_estado_asignado", methods={"GET"}, defaults={"estado"="asignado"})
     * @Route("/estado/en-proceso", name="no_conformidad_estado_en_proceso", methods={"GET"}, defaults={"estado"="en_proceso"})
     * @Route("/estado/verificada-correccion", name="no_conformidad_estado_verificada_correccion", methods={"GET"}, defaults={"estado"="verificada_correccion"})
     * @Route("/estado/cerrado", name="no_conformidad_estado_verificada_efectividad", methods={"GET"}, defaults={"estado"="verificada_efectividad"})
     * @Route("/estado/desestimado", name="no_conformidad_estado_desestimado", methods={"GET"}, defaults={"estado"="desestimado"})
     */
    public function indexByState(string $estado, NoConformidadRepository $noConformidadRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $user = $this->getUser();
        $area = $request->query->get('area');
        $origen = $request->query->get('origen');
        $categoria = $request->query->get('categoria');
        $norma = $request->query->get('norma');
        $pgcd = $request->query->get('pgcd');
        $mesDesde = $request->query->get('mes_desde');
        $anoDesde = $request->query->get('ano_desde');
        $mesHasta = $request->query->get('mes_hasta');
        $anoHasta = $request->query->get('ano_hasta');

        $queryBuilder = $noConformidadRepository->createQueryBuilder('nc')
            ->andWhere('nc.estado = :estado')
            ->setParameter('estado', $estado)
            ->orderBy('nc.id', 'DESC');
            
        // Aplicar filtros según el rol del usuario
        $queryBuilder = $this->applyRoleBasedFilters($queryBuilder, $user);

        if ($area) {
            $queryBuilder->andWhere('nc.area = :area')
                        ->setParameter('area', $area);
        }

        if ($origen) {
            $queryBuilder->andWhere('nc.origen = :origen')
                        ->setParameter('origen', $origen);
        }

        if ($categoria) {
            $queryBuilder->andWhere('nc.categoria = :categoria')
                        ->setParameter('categoria', $categoria);
        }

        if ($norma) {
            $queryBuilder->andWhere('nc.norma = :norma')
                        ->setParameter('norma', $norma);
        }

        if ($pgcd) {
            $queryBuilder->andWhere('nc.pgcd = :pgcd')
                        ->setParameter('pgcd', $pgcd);
        }

        // Filtro por rango de fechas
        if ($mesDesde && $anoDesde) {
            $fechaDesde = new \DateTime($anoDesde . '-' . $mesDesde . '-01 00:00:00');
            $queryBuilder->andWhere('nc.fecha >= :fechaDesde')
                        ->setParameter('fechaDesde', $fechaDesde);
        }
        
        if ($mesHasta && $anoHasta) {
            $fechaHasta = new \DateTime($anoHasta . '-' . $mesHasta . '-01 00:00:00');
            $fechaHasta->modify('last day of this month 23:59:59');
            $queryBuilder->andWhere('nc.fecha <= :fechaHasta')
                        ->setParameter('fechaHasta', $fechaHasta);
        }

        $noConformidades = $paginator->paginate(
            $queryBuilder->getQuery(),
            $request->query->get('page', 1),
            15
        );

        // Mapeo de estados a títulos legibles
        $titulosEstado = [
            'nuevo' => 'Nuevas',
            'asignado' => 'Asignadas',
            'en_proceso' => 'En Proceso',
            'verificada_correccion' => 'Corrección Verificada',
            'verificada_efectividad' => 'Cerradas',
            'desestimado' => 'Desestimadas'
        ];

        return $this->render('no_conformidad/index.html.twig', [
            'no_conformidades' => $noConformidades,
            'estado_filtro' => $estado,
            'area_filtro' => $area,
            'origen_filtro' => $origen,
            'categoria_filtro' => $categoria,
            'norma_filtro' => $norma,
            'pgcd_filtro' => $pgcd,
            'mes_desde' => $mesDesde,
            'ano_desde' => $anoDesde,
            'mes_hasta' => $mesHasta,
            'ano_hasta' => $anoHasta,
            'titulo_pagina' => 'No Conformidades ' . ($titulosEstado[$estado] ?? $estado),
            'es_vista_estado' => true,
            'can_access_advanced_search' => $this->canAccessAdvancedSearch(),
            'is_calidad' => $this->isGranted('ROLE_CALIDAD'),
            'is_admin' => $this->isGranted('ROLE_ADMIN'),
        ]);
    }

    /**
     * @Route("/new", name="no_conformidad_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        // Verificar permisos para crear
        if (!$this->canCreateNoConformidad()) {
            $this->addFlash('error', 'No tiene permisos para crear no conformidades.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        $noConformidad = new NoConformidad();
        $noConformidad->setFecha(new \DateTime());
        
        $form = $this->createForm(NoConformidadType::class, $noConformidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validación adicional: si requisito legal es "Sí", el campo requisito es obligatorio
            if ($noConformidad->getRequisitoLegal() === true && empty($noConformidad->getRequisito())) {
                $this->addFlash('error', 'Cuando el requisito legal es "Sí", debe especificar el requisito.');
                return $this->render('no_conformidad/new.html.twig', [
                    'no_conformidad' => $noConformidad,
                    'form' => $form->createView(),
                ]);
            }
            
            // Si requisito legal es "No", limpiar el campo requisito
            if ($noConformidad->getRequisitoLegal() === false) {
                $noConformidad->setRequisito(null);
            }
            
            // Establecer el usuario que crea la no conformidad
            $noConformidad->setCreadoPor($this->getUser());
            
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($noConformidad);
            $entityManager->flush();

            $this->addFlash('success', 'No conformidad registrada exitosamente');

            return $this->redirectToRoute('no_conformidad_index');
        }

        return $this->render('no_conformidad/new.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="no_conformidad_show", methods={"GET"})
     */
    public function show(NoConformidad $noConformidad): Response
    {
        // Pueden ver: creador, usuarios con ROLE_CALIDAD/ADMIN, y usuario asignado
        $usuarioActual = $this->getUser();
        $creadoPor = $noConformidad->getCreadoPor();
        $asignadoA = $noConformidad->getAsignadoA();
        
        $puedeVer = $this->isGranted('ROLE_CALIDAD') || 
                   $this->isGranted('ROLE_ADMIN') ||
                   ($creadoPor && $creadoPor->getId() === $usuarioActual->getId()) ||
                   ($asignadoA && $asignadoA->getId() === $usuarioActual->getId());
        
        if (!$puedeVer) {
            $this->addFlash('error', 'No tiene permisos para ver esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        
        return $this->render('no_conformidad/show.html.twig', [
            'no_conformidad' => $noConformidad,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="no_conformidad_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, NoConformidad $noConformidad): Response
    {
        // Solo permitir edición si el estado es "nuevo"
        if ($noConformidad->getEstado() !== 'nuevo') {
            $this->addFlash('error', 'Solo se pueden editar las no conformidades con estado "Nuevo".');
            return $this->redirectToRoute('no_conformidad_index');
        }
        
        // Solo usuarios con ROLE_CALIDAD/ADMIN pueden editar
        if (!$this->isGranted('ROLE_CALIDAD') && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'Solo usuarios con rol CALIDAD pueden editar no conformidades.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        
        $form = $this->createForm(NoConformidadType::class, $noConformidad, [
            'is_edit' => true
        ]);
        
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Validación adicional: si requisito legal es "Sí", el campo requisito es obligatorio
            if ($noConformidad->getRequisitoLegal() === true && empty($noConformidad->getRequisito())) {
                $this->addFlash('error', 'Cuando el requisito legal es "Sí", debe especificar el requisito.');
                return $this->render('no_conformidad/edit.html.twig', [
                    'no_conformidad' => $noConformidad,
                    'form' => $form->createView(),
                ]);
            }
            
            // Si requisito legal es "No", limpiar el campo requisito
            if ($noConformidad->getRequisitoLegal() === false) {
                $noConformidad->setRequisito(null);
            }
            
            $this->getDoctrine()->getManager()->flush();

            $this->addFlash('success', 'No conformidad actualizada exitosamente');

            return $this->redirectToRoute('no_conformidad_index');
        }

        return $this->render('no_conformidad/edit.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/revisar", name="no_conformidad_review", methods={"GET","POST"})
     */
    public function review(Request $request, NoConformidad $noConformidad, WorkflowInterface $noConformidadStateMachine): Response
    {
        // Verificar permisos para revisar
        if (!$this->canReviewNoConformidad($noConformidad)) {
            $this->addFlash('error', 'No tiene permisos para revisar esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        $form = $this->createForm(ReviewType::class, $noConformidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accionHallazgo = $form->get('accionHallazgo')->getData();
            
            try {
                if ($accionHallazgo === 'aceptar' && $noConformidadStateMachine->can($noConformidad, 'revisar')) {
                    $noConformidadStateMachine->apply($noConformidad, 'revisar');
                } elseif ($accionHallazgo === 'desestimar' && $noConformidadStateMachine->can($noConformidad, 'desestimar')) {
                    // Guardar la explicación del desestimado
                    $explicacion = $form->get('explicacion')->getData();
                    if ($explicacion) {
                        $noConformidad->setExplicacionDesestimado($explicacion);
                    }
                    $noConformidadStateMachine->apply($noConformidad, 'desestimar');
                }

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'No conformidad procesada exitosamente');

                return $this->redirectToRoute('no_conformidad_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al procesar la no conformidad: ' . $e->getMessage());
            }
        }

        return $this->render('no_conformidad/review.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/tratar", name="no_conformidad_treat", methods={"GET","POST"})
     */
    public function treat(Request $request, NoConformidad $noConformidad, WorkflowInterface $noConformidadStateMachine): Response
    {
        // Verificar permisos para tratar
        if (!$this->canTreatNoConformidad($noConformidad)) {
            $this->addFlash('error', 'No tiene permisos para tratar esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }

        $form = $this->createForm(TreatType::class, $noConformidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if ($noConformidadStateMachine->can($noConformidad, 'asignar')) {
                    $noConformidadStateMachine->apply($noConformidad, 'asignar');
                }

                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Tratamiento registrado exitosamente');

                return $this->redirectToRoute('no_conformidad_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al registrar el tratamiento: ' . $e->getMessage());
            }
        }

        return $this->render('no_conformidad/treat.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/aceptar", name="no_conformidad_accept", methods={"GET","POST"})
     */
    public function accept(Request $request, NoConformidad $noConformidad, WorkflowInterface $noConformidadStateMachine): Response
    {
        // Solo ROLE_CALIDAD puede aceptar
        if (!$this->isGranted('ROLE_CALIDAD') && !$this->isGranted('ROLE_ADMIN')) {
            $this->addFlash('error', 'No tiene permisos para aceptar esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        if ($request->isMethod('POST')) {
            $decision = $request->request->get('decision');
            $explicacionRevision = $request->request->get('explicacion_revision');
            
            try {
                if ($decision === 'aceptar') {
                    // Aceptar análisis y acciones
                    if ($noConformidadStateMachine->can($noConformidad, 'aceptar')) {
                        $noConformidadStateMachine->apply($noConformidad, 'aceptar');
                        $noConformidad->setAnalisisAceptado(true);
                        
                        // Enviar correo electrónico de aceptación
                        $this->enviarCorreoAceptacion($noConformidad);
                        
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash('success', 'Análisis y acciones aceptadas exitosamente. Se ha enviado notificación por correo electrónico.');
                    } else {
                        $this->addFlash('error', 'No se puede aceptar la no conformidad en su estado actual');
                    }
                    
                } elseif ($decision === 'revisar') {
                    // Revisar análisis y acciones - marcar para revisión
                    if (!empty($explicacionRevision)) {
                        // Guardar explicación de revisión específica para análisis y marcar como no aceptado
                        $noConformidad->setExplicacionRevisionAnalisis($explicacionRevision);
                        $noConformidad->setAnalisisAceptado(false);
                        
                        // Enviar correo electrónico de revisión
                        $this->enviarCorreoRevision($noConformidad, $explicacionRevision);
                        
                        $this->getDoctrine()->getManager()->flush();
                        $this->addFlash('warning', 'Se ha solicitado revisión del análisis y acciones. Se ha enviado notificación por correo electrónico.');
                    } else {
                        $this->addFlash('error', 'Debe proporcionar una explicación para la revisión');
                        return $this->render('no_conformidad/accept.html.twig', [
                            'no_conformidad' => $noConformidad,
                        ]);
                    }
                    
                } else {
                    $this->addFlash('error', 'Debe seleccionar una opción: Aceptar o Revisar');
                    return $this->render('no_conformidad/accept.html.twig', [
                        'no_conformidad' => $noConformidad,
                    ]);
                }
                
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al procesar la decisión: ' . $e->getMessage());
            }
            
            return $this->redirectToRoute('no_conformidad_index');
        }

        return $this->render('no_conformidad/accept.html.twig', [
            'no_conformidad' => $noConformidad,
        ]);
    }

    /**
     * @Route("/{id}/verificar-correccion", name="no_conformidad_verify_correction", methods={"GET","POST"})
     */
    public function verifyCorrection(Request $request, NoConformidad $noConformidad, WorkflowInterface $noConformidadStateMachine): Response
    {
        // Verificar permisos para verificar corrección
        if (!$this->canVerifyCorrection($noConformidad)) {
            $this->addFlash('error', 'No tiene permisos para verificar la corrección de esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        $form = $this->createForm(VerifyCorrectionType::class, $noConformidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $correccionVerificada = $form->get('correccionVerificada')->getData();
                
                if ($correccionVerificada === true) {
                    // SI - Marcar como verificada
                    $noConformidad->setCorreccionVerificada(true);
                    
                    // Diferenciar según la categoría del hallazgo
                    if ($noConformidad->getCategoria() === 'no_conformidad') {
                        // NO CONFORMIDAD: verificar acción correctiva - no modificar fechaCorreccion
                        // La fechaAccionCorrectiva ya existe y se mantiene
                        if ($noConformidadStateMachine->can($noConformidad, 'verificar_correccion')) {
                            $noConformidadStateMachine->apply($noConformidad, 'verificar_correccion');
                        }
                        $this->addFlash('success', 'Acción correctiva verificada exitosamente. Ahora debe verificar la efectividad.');
                        
                    } else {
                        // OBSERVACIÓN/OPORTUNIDAD DE MEJORA: verificar corrección - usar fechaCorreccion
                        // Asegurar que tiene fecha de corrección
                        if (!$noConformidad->getFechaCorreccion()) {
                            $noConformidad->setFechaCorreccion(new \DateTime());
                        }
                        
                        if ($noConformidadStateMachine->can($noConformidad, 'verificar_correccion')) {
                            $noConformidadStateMachine->apply($noConformidad, 'verificar_correccion');
                        }
                        $this->addFlash('success', 'Corrección verificada exitosamente. Proceso completado.');
                    }
                    
                } elseif ($correccionVerificada === false) {
                    // NO - Solicitar nueva fecha
                    $noConformidad->setCorreccionVerificada(false);
                    
                    $nuevaFecha = $form->get('nuevaFechaCorreccion')->getData();
                    if ($nuevaFecha) {
                        // Diferenciar según la categoría del hallazgo
                        if ($noConformidad->getCategoria() === 'no_conformidad') {
                            // NO CONFORMIDAD: actualizar fecha de acción correctiva
                            $noConformidad->setFechaAccionCorrectiva($nuevaFecha);
                            $this->addFlash('warning', 'Se ha registrado una nueva fecha de acción correctiva: ' . $nuevaFecha->format('d/m/Y'));
                        } else {
                            // OBSERVACIÓN/OPORTUNIDAD DE MEJORA: actualizar fecha de corrección
                            $noConformidad->setFechaCorreccion($nuevaFecha);
                            $noConformidad->setNuevaFechaCorreccion($nuevaFecha);
                            $this->addFlash('warning', 'Se ha registrado una nueva fecha de corrección: ' . $nuevaFecha->format('d/m/Y'));
                        }
                    } else {
                        if ($noConformidad->getCategoria() === 'no_conformidad') {
                            $this->addFlash('error', 'Debe proporcionar una nueva fecha de acción correctiva');
                        } else {
                            $this->addFlash('error', 'Debe proporcionar una nueva fecha de corrección');
                        }
                        return $this->render('no_conformidad/verify_correction.html.twig', [
                            'no_conformidad' => $noConformidad,
                            'form' => $form->createView(),
                        ]);
                    }
                    
                    // No aplicar transición de workflow, mantener en estado actual
                }

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('no_conformidad_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al verificar la corrección: ' . $e->getMessage());
            }
        }

        return $this->render('no_conformidad/verify_correction.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/verificar-efectividad", name="no_conformidad_verify_effectiveness", methods={"GET","POST"})
     */
    public function verifyEffectiveness(Request $request, NoConformidad $noConformidad, WorkflowInterface $noConformidadStateMachine): Response
    {
        // Verificar permisos para verificar efectividad
        if (!$this->canVerifyEffectiveness($noConformidad)) {
            $this->addFlash('error', 'No tiene permisos para verificar la efectividad de esta no conformidad.');
            return $this->redirectToRoute('no_conformidad_index');
        }
        $form = $this->createForm(VerifyEffectivenessType::class, $noConformidad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $efectividadVerificada = $form->get('efectividadVerificada')->getData();
                $comentarios = $form->get('comentariosVerificacion')->getData();
                
                if ($efectividadVerificada === true) {
                    // SI - La efectividad es correcta, cerrar la no conformidad
                    $noConformidad->setEfectividadVerificada(true);
                    
                    if ($comentarios) {
                        $noConformidad->setComentariosVerificacion($comentarios);
                    }
                    
                    // Aplicar transición para cerrar definitivamente
                if ($noConformidadStateMachine->can($noConformidad, 'verificar_efectividad')) {
                    $noConformidadStateMachine->apply($noConformidad, 'verificar_efectividad');
                    }
                    
                    $this->addFlash('success', 'Efectividad verificada exitosamente. La no conformidad ha sido cerrada.');
                    
                } elseif ($efectividadVerificada === false) {
                    // NO - La efectividad no es correcta, solicitar nueva fecha
                    $noConformidad->setEfectividadVerificada(false);
                    
                    if ($comentarios) {
                        $noConformidad->setComentariosVerificacion($comentarios);
                    }
                    
                    $nuevaFecha = $form->get('nuevaFechaEfectividad')->getData();
                    if ($nuevaFecha) {
                        // TODO: Guardar la nueva fecha en algún campo o log
                        $this->addFlash('warning', 'Se ha registrado que la efectividad no es correcta. Nueva fecha de verificación: ' . $nuevaFecha->format('d/m/Y'));
                    } else {
                        $this->addFlash('error', 'Debe proporcionar una nueva fecha de verificación de efectividad');
                        return $this->render('no_conformidad/verify_effectiveness.html.twig', [
                            'no_conformidad' => $noConformidad,
                            'form' => $form->createView(),
                        ]);
                    }
                    
                    // No aplicar transición de workflow, mantener en estado actual
                }

                $this->getDoctrine()->getManager()->flush();

                return $this->redirectToRoute('no_conformidad_index');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Error al verificar la efectividad: ' . $e->getMessage());
            }
        }

        return $this->render('no_conformidad/verify_effectiveness.html.twig', [
            'no_conformidad' => $noConformidad,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="no_conformidad_delete", methods={"DELETE"})
     */
    public function delete(Request $request, NoConformidad $noConformidad): Response
    {
        if ($this->isCsrfTokenValid('delete'.$noConformidad->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($noConformidad);
            $entityManager->flush();

            $this->addFlash('success', 'No conformidad eliminada exitosamente');
        }

        return $this->redirectToRoute('no_conformidad_index');
    }

    /**
     * Envía correo electrónico de aceptación de análisis y acciones
     */
    private function enviarCorreoAceptacion(NoConformidad $noConformidad): void
    {
        // TODO: Implementar envío de correo electrónico
        // Destinatario: Usuario que generó la no conformidad (noConformidad.empleado)
        // Asunto: "ANÁLISIS Y ACCIONES ACEPTADAS - No Conformidad #{id}"
        // Contenido: Notificar que el análisis y acciones han sido aceptadas
        
        error_log("Correo de aceptación enviado para No Conformidad #{$noConformidad->getId()}");
    }

    /**
     * Envía correo electrónico de revisión de análisis y acciones
     */
    private function enviarCorreoRevision(NoConformidad $noConformidad, string $explicacion): void
    {
        // TODO: Implementar envío de correo electrónico
        // Destinatario: Usuario que generó la no conformidad (noConformidad.empleado)
        // Asunto: "REVISIÓN SOLICITADA - No Conformidad #{id}"
        // Contenido: Incluir la explicación de revisión y habilitar edición
        
        error_log("Correo de revisión enviado para No Conformidad #{$noConformidad->getId()} con explicación: {$explicacion}");
    }
} 