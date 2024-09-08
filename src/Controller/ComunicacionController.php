<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comunicacion;
use App\Entity\RecibidoComunicado;
use App\Entity\AreaAdministrativa;
use App\Form\ComunicacionType;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Form\Filter\ComunicadoFilterType;





class ComunicacionController extends AbstractController
{
    /**
     * @Route("/comunicacion", name="comunicacion")
     */
    public function index()
    {
        return $this->render('comunicacion/index.html.twig', [
            'controller_name' => 'ComunicacionController',
        ]);
    }


    public function newComunicado(Request $request)
	{

		$em = $this->getDoctrine()->getManager();

		$comunicacion = new Comunicacion();
		$form       = $this->createForm(
			ComunicacionType::class,$comunicacion
		);

		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {


			

            $date = new \DateTime();
            $comunicacion->setFecha($date);
            $comunicacion->setAnio($date->format('Y'));
            $area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();
            $comunicacion->setAreaOrigen($area);
            $numero=$em->getRepository(Comunicacion::class)->countComunicacionesByTipo($comunicacion->getTipo(), $area->getId());
            $comunicacion->setNumero($numero+1);
            if ($form->get('masivo')->getData()=="TODOS") {

                $areas = $em->getRepository(AreaAdministrativa::class)->createQueryBuilder('a')
                ->where('a.activo = true AND a.id != :id')
                ->setParameter('id', 67)                
                ->getQuery()->getResult();
                foreach ($areas as $destino) {
                    $comunicacion->addAreaDestino($destino);
                }
            }
            if ($form->get('masivo')->getData()=="CONCEJALES") {
                $areas = $em->getRepository(AreaAdministrativa::class)->createQueryBuilder('a')
                ->where('a.activo = true AND a.nombre like :patron')
                ->setParameter('patron', '%Concejal%')
                ->getQuery()->getResult();
                foreach ($areas as $destino) {
                    $comunicacion->addAreaDestino($destino);
                }           
            }
            if ($form->get('masivo')->getData()=="AREAS") {
                $areas = $em->getRepository(AreaAdministrativa::class)->createQueryBuilder('a')
                ->where('a.activo = true AND a.nombre not like :patron AND a.id != :id')
                ->setParameter('patron', '%Concejal%')
                ->setParameter('id', 67)
                ->getQuery()->getResult();
                foreach ($areas as $destino) {
                    $comunicacion->addAreaDestino($destino);
                }

            }
            if ($form->get('masivo')->getData()=="COMISIONES") {
                $ids = [63,60,57,62,55,58,64,61,56,52,59];
                $areas = $em->getRepository(AreaAdministrativa::class)->createQueryBuilder('a')
                ->where('a.id IN (:ids)')
                ->setParameter('ids', $ids)
                ->getQuery()->getResult();
                foreach ($areas as $destino) {
                    $comunicacion->addAreaDestino($destino);
                }           
            }
            $em->persist($comunicacion);

            foreach ($comunicacion->getAreaDestino() as $destino){
                $recibido = new RecibidoComunicado;
                $recibido->setComunicacion($comunicacion);
                $recibido->setArea($destino);
                $recibido->setEstado('RECIBIDO');
                $em->persist($recibido);

            }

            $em->flush();

			$this->get('session')->getFlashBag()->add(
				'success',
				'Comunicado Generado con exito'
			);
            return $this->redirectToRoute('comunicaciones_enviadas');
		}

		return $this->render(
			'comunicacion/new.html.twig',
			array(

				'form'       => $form->createView(),
			)
		);
	}


    public function enviadas(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $comunicaciones = $em->getRepository(Comunicacion::class)->findBy(['areaOrigen'=>$area],['id'=>'DESC']);


        $form = $this->createForm(ComunicadoFilterType::class,			null,
		[
			'method' => 'GET'
		]);
	
		$form->handleRequest($request);

		if ($form->get( 'buscar' )->isClicked()) {
			$data=$form->getData();

			$comunicaciones = $em->getRepository(Comunicacion::class)->getQbBuscar($data->getAreaDestino(),$area,$data->getFecha(),$data->getEstado());
		}

        $comunicaciones = $paginator->paginate(
            $comunicaciones,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );


		return $this->render(
			'comunicacion/enviadas.html.twig',
			array(
				'comunicados' => $comunicaciones,
                'filter_type' => $form->createView()

			)
		);

    }

    public function recibidas(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $recibidos = $em->getRepository(RecibidoComunicado::class)->findBy(['area'=>$area],['id'=>'DESC']);

        $form = $this->createForm(ComunicadoFilterType::class,			null,
		[
			'method' => 'GET'
		]);
	
		$form->handleRequest($request);

		if ($form->isSubmitted() && $form->isValid()) {
			$data=$form->getData();

			$recibidos = $em->getRepository(Comunicacion::class)->getQbBuscar($area,$data->getAreaOrigen(),$data->getFecha(),$data->getEstado());
		}

        $recibidos = $paginator->paginate(
            $recibidos,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );


		return $this->render(
			'comunicacion/recibidas.html.twig',
			array(
				'recibidos' => $recibidos,
				'filter_type'=>$form->createView(),
			)
		);

    }


	public function imprimirComunicadoRecibido(RecibidoComunicado $id)
    {
        $comunicado=$id->getComunicacion()->getArchivo();

		$em = $this->getDoctrine()->getManager();

        $area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $recibidos = $em->getRepository(RecibidoComunicado::class)->findBy(['area'=>$area,'id'=>$id]);
        $response = null;
        if($recibidos[0]->getArea() == $area){

        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/comunicados/' . $comunicado;

        // Crear una BinaryFileResponse para el archivo PDF
        $response = new BinaryFileResponse($pdfPath);

        // Configurar la cabecera para forzar la descarga del archivo
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

        $id->setEstado("ABIERTO");

        $em->flush();

        }

        return $response;
    }


	public function imprimirComunicado(Comunicacion $id)
    {
        
        $comunicado=$id->getArchivo();


        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/comunicados/' . $comunicado;

        // Crear una BinaryFileResponse para el archivo PDF
        $response = new BinaryFileResponse($pdfPath);

        // Configurar la cabecera para forzar la descarga del archivo
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');


        return $response;
    }

}
