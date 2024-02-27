<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Comunicacion;
use App\Entity\RecibidoComunicado;
use App\Form\ComunicacionType;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;




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

        $comunicaciones = $paginator->paginate(
            $comunicaciones,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );


		return $this->render(
			'comunicacion/enviadas.html.twig',
			array(
				'comunicados' => $comunicaciones,
				
			)
		);

    }

    public function recibidas(PaginatorInterface $paginator, Request $request){

        $em = $this->getDoctrine()->getManager();

		$area = $this->getUser()->getPersona()->getCargoPersona()->first()->getAreaAdministrativa();

        $recibidos = $em->getRepository(RecibidoComunicado::class)->findBy(['area'=>$area],['id'=>'DESC']);

        $recibidos = $paginator->paginate(
            $recibidos,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );


		return $this->render(
			'comunicacion/recibidas.html.twig',
			array(
				'recibidos' => $recibidos,
				
			)
		);

    }


	public function imprimirComunicadoRecibido(RecibidoComunicado $id)
    {
        $comunicado=$id->getComunicacion()->getArchivo();

		$em = $this->getDoctrine()->getManager();


        $pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/comunicados/' . $comunicado;

        // Crear una BinaryFileResponse para el archivo PDF
        $response = new BinaryFileResponse($pdfPath);

        // Configurar la cabecera para forzar la descarga del archivo
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');

        $id->setEstado("ABIERTO");

        $em->flush();


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
