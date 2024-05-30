<?php

namespace App\Controller;

use App\Entity\Parametro;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use App\Entity\Dictamen;
use App\Entity\TextoDefinitivo;
use App\Entity\TipoProyecto;
use App\Form\Filter\TextoDefinitivoFilterType;
use App\Form\TextoDefinitivoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/texto-definitivo")
 */
class TextoDefinitivoController extends AbstractController {

	/**
	 * @Route("/", name="texto_definitivo_index")
	 */
	public function index( PaginatorInterface $paginator, Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( TextoDefinitivoFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$textoDefinitivos = $em->getRepository( TextoDefinitivo::class )->getQbAll( $filterType->getData() );
		} else {

			$textoDefinitivos = $em->getRepository( TextoDefinitivo::class )->getQbAll();
		}

		$textoDefinitivos = $paginator->paginate(
			$textoDefinitivos,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'textodefinitivo/index.html.twig',
			array(
				'textoDefinitivos' => $textoDefinitivos,
				'filter_type'      => $filterType->createView()
			) );
	}

	/**
	 * @Route("/new", name="texto_definitivo_new")
	 */
	public function new( Request $request ) {
		if ( ! $this->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			return $this->redirectToRoute( 'app_homepage' );
		}
		$textoDefinitivo = new Textodefinitivo();
		$form            = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$form->remove( 'tipoDocumento' );
		$form->remove( 'numeroDocumento' );
		$form->remove( 'fechaDocumento' );
		$form->remove( 'tipoTextoDefinitivo' );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$expediente = $form->get( "dictamen" )->get( 'expediente' )->getData();
			$dictamen=$form->get("dictamen")->getData();
			$textoDefinitivo->getDictamen()->setExpediente( $expediente );
			$textoDefinitivo->setTexto("");
			$em = $this->getDoctrine()->getManager();
			$em->persist( $textoDefinitivo );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Texto definitivo creado correctamente'
			);

			return $this->redirectToRoute( 'texto_definitivo_show', array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/new.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'form'            => $form->createView(),
			) );
	}

	/**
	 * @Route("/{id}/show", name="texto_definitivo_show")
	 */
	public function show( TextoDefinitivo $textoDefinitivo ) {


		return $this->render( 'textodefinitivo/show.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
			) );
	}

	/**
	 * @Route("/{id}/edit", name="texto_definitivo_edit")
	 */
	public function edit( Request $request, TextoDefinitivo $textoDefinitivo ) {

		if ( ! $this->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			return $this->redirectToRoute( 'app_homepage' );
		}

		$em = $this->getDoctrine()->getManager();

		$editForm = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
//		$editForm->get('dictamen')->get('expediente')->setData($textoDefinitivo->getDictamen()->getExpediente());
		//$editForm->get( 'dictamen' )->remove( 'expediente' );
		$editForm->remove( 'tipoDocumento' );
		$editForm->remove( 'numeroDocumento' );
		$editForm->remove( 'fechaDocumento' );
		$editForm->remove( 'tipoTextoDefinitivo' );
		$editForm->remove( 'firmantes' );
		$editForm->handleRequest( $request );

		$anexosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $textoDefinitivo->getAnexos() as $anexo ) {
			$anexosOriginales->add( $anexo );
		}

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			foreach ( $anexosOriginales as $anexo ) {
				if ( false === $textoDefinitivo->getAnexos()->contains( $anexo ) ) {
					$anexo->setTextoDefinitivo( null );
					$em->remove( $anexo );
				}
			}

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Texto definitivo actualizado correctamente'
			);

			return $this->redirectToRoute( 'texto_definitivo_edit', array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/edit.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'edit_form'       => $editForm->createView()
			) );
	}

	/**
	 * @Route("/{id}/delete", name="texto_definitivo_delete")
	 */
	public function delete( Request $request, TextoDefinitivo $textoDefinitivo ) {
		$form = $this->createDeleteForm( $textoDefinitivo );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $textoDefinitivo );
			$em->flush();
		}

		return $this->redirectToRoute( 'texto_definitivo_index' );
	}

	/**
	 * Creates a form to delete a textoDefinitivo entity.
	 *
	 * @param TextoDefinitivo $textoDefinitivo The textoDefinitivo entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( TextoDefinitivo $textoDefinitivo ) {
		return $this->createFormBuilder()
		            ->set( $this->generateUrl( 'texto_definitivo_delete',
			            array( 'id' => $textoDefinitivo->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	/**
	 * @Route("/asignar/{dictamen}", name="texto_definitivo_asignar")
	 */
	public function asignar( Request $request, Dictamen $dictamen ) {
		if ( ! $this->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			return $this->redirectToRoute( 'app_homepage' );
		}
		$textoDefinitivo = new Textodefinitivo();
		$em              = $this->getDoctrine()->getManager();

		$tieneTextoDefinitivo = $em->getRepository( TextoDefinitivo::class )->findOneBy( [ 'dictamen' => $dictamen ] );

		if ( $tieneTextoDefinitivo ) {
			return $this->redirectToRoute( 'texto_definitivo_show', [ 'id' => $tieneTextoDefinitivo->getId() ] );
		}

		$textoDefinitivo->setDictamen( $dictamen );
		$form = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$form->remove( 'dictamen' );
		$form->remove('tipoDocumento');
		$form->remove('numeroDocumento');
		$form->remove('fechaDocumento');


		if ( $dictamen->getTipoProyecto()->getId() !== TipoProyecto::TIPO_ORDENANZA ) {
			$form->remove( 'rama' );
		}

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$textoDefinitivo->setTexto("");


			$em->persist( $textoDefinitivo );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Texto definitivo asignado correctamente'
			);

			return $this->redirectToRoute( 'texto_definitivo_show', [ 'id' => $textoDefinitivo->getId() ] );
		}

		return $this->render( 'textodefinitivo/asignar.html.twig',
			[
				'textoDefinitivo' => $textoDefinitivo,
				'form'            => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/imprimir", name="texto_definitivo_imprimir")
	 */
	public function imprimir( Pdf $knpSnappyPdf, TextoDefinitivo $textoDefinitivo ) {

//		$expediente = $textoDefinitivo->getDictamen()->getExpediente();

		$title   = $textoDefinitivo->getDictamen();
		$leyenda = $this->getDoctrine()->getRepository( Parametro::class )->findOneBySlug( 'texto-definitivo-leyenda' );
		$rama    = null;

		if ( $textoDefinitivo->getDictamen() &&
		     ( $textoDefinitivo->getDictamen()->getTipoProyecto()->getId() == TipoProyecto::TIPO_ORDENANZA ||
		       ( $textoDefinitivo->getTipoTextoDefinitivo() && $textoDefinitivo->getTipoTextoDefinitivo()->getId() == TipoProyecto::TIPO_ORDENANZA ) ) ) {
			$rama    = $textoDefinitivo->getRama()->getNumeroRomano();
			$leyenda = $this->getDoctrine()->getRepository( Parametro::class )->findOneBySlug( 'texto-definitivo-leyenda-ordenanza' );
		}

		$header = $this->renderView( 'textodefinitivo/encabezado_texto_definitivo.pdf.twig',
			[
				"textoDefinitivo" => $textoDefinitivo,
			] );


		$html = $this->renderView( 'textodefinitivo/texto-definitivo.pdf.twig',
			[
				'textoDefinitivo' => $textoDefinitivo,
				'rama'            => $rama,
				'leyenda'         => $leyenda,
				'title'           => $title,
			]
		);

		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		if($textoDefinitivo->getArchivo()){
			$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/definitivo/' . $textoDefinitivo->getArchivo();

			// Crear una BinaryFileResponse para el archivo PDF
			$response = new BinaryFileResponse($pdfPath);
	
			// Configurar la cabecera para forzar la descarga del archivo
			$response->headers->set('Content-Type', 'application/pdf');
			$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');
			return $response;
		}else{
			return new Response(
				$knpSnappyPdf->getOutputFromHtml( $html,
					[
						'page-size'      => 'Legal',
	//					'page-width'     => '220mm',
	//					'page-height'     => '340mm',
	//					'margin-left'    => "3cm",
	//					'margin-right'   => "3cm",
						'margin-top'     => "5cm",
						'margin-bottom'  => "2cm",
						'header-html'    => $header,
						'header-spacing' => 5,
						'footer-spacing' => 5,
						'footer-html'    => $footer,
	//                    'margin-bottom' => "1cm"
					]
				)
				, 200, [
					'Content-Type'        => 'application/pdf',
					'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
				]
			);
	
		}
//        return new Response($html);

		
	}

		/**
	 * @Route("/{id}/imprimirpase", name="texto_definitivo_pase")
	 */
	public function imprimirPase(TextoDefinitivo $textoDefinitivo ){
		$pdfPath = $this->getParameter('kernel.project_dir') . '/public/uploads/expedientes/pasedem/' . $textoDefinitivo->getPase();

		// Crear una BinaryFileResponse para el archivo PDF
		$response = new BinaryFileResponse($pdfPath);

		// Configurar la cabecera para forzar la descarga del archivo
		$response->headers->set('Content-Type', 'application/pdf');
		$response->headers->set('Content-Disposition', 'inline; filename="custom_pdf_name.pdf"');
		return $response;
	}

	/**
	 * @Route("/nuevo-sin-expediente", name="texto_definitivo_nuevo_sin_expediente")
	 */
	public function nuevoSinExpediente( Request $request ) {
		if ( ! $this->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			return $this->redirectToRoute( 'app_homepage' );
		}
		$textoDefinitivo = new Textodefinitivo();
		$form            = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );

		$form->remove( 'dictamen' );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$em = $this->getDoctrine()->getManager();
			$em->persist( $textoDefinitivo );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Texto definitivo creado correctamente'
			);

			return $this->redirectToRoute( 'texto_definitivo_show', array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/nuevo_sin_expediente.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'form'            => $form->createView(),
			) );
	}

	/**
	 * @Route("/{id}/editar-sin-expediente", name="texto_definitivo_editar_sin_expediente")
	 */
	public function editarSinExpediente( Request $request, TextoDefinitivo $textoDefinitivo ) {

		if ( ! $this->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			return $this->redirectToRoute( 'app_homepage' );
		}

		$em = $this->getDoctrine()->getManager();

		$editForm = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$editForm->remove( 'dictamen' );

		$editForm->handleRequest( $request );

		$anexosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $textoDefinitivo->getAnexos() as $anexo ) {
			$anexosOriginales->add( $anexo );
		}

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			foreach ( $anexosOriginales as $anexo ) {
				if ( false === $textoDefinitivo->getAnexos()->contains( $anexo ) ) {
					$anexo->setTextoDefinitivo( null );
					$em->remove( $anexo );
				}
			}

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Texto definitivo actualizado correctamente'
			);

			return $this->redirectToRoute( 'texto_definitivo_editar_sin_expediente',
				array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/editar_sin_expediente.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'edit_form'       => $editForm->createView()
			) );
	}
	/**
	 * @Route("/{id}/imprimir", name="texto_imprimir")
	 */
	public function imprimirArchivoTexto(KnpSnappyPdf $knpSnappyPdf, TextoDefinitivo $textoDefinitivo) {
		$dictamen = $textoDefinitivo->getDictamen();
		$expediente=$dictamen->getExpediente();

		//CARATULA

		$title      = 'Carátula';

		$html = $this->renderView(
			'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

		$pdfMerge = new PDFMerger;
		$filesystem = new Filesystem();
		$tmp=sys_get_temp_dir();
		$date = new \DateTime();
		$time=$date->getTimeStamp();

		$filesystem->remove('filePDF.pdf');

		$nombre=$tmp.'/Caratula'.$time.'.pdf';
		$knpSnappyPdf->generateFromHtml(
				$html
				,$nombre, array(
					'page-size'      => 'Legal',
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-spacing' => 4,
					'footer-spacing' => 5,				
				)
			);
				
		$pdfMerge->addPDF($nombre); 

		//Proyecto
		$pdfMerge->addPDF('uploads/expedientes/internos/'.$expediente->getExpedienteInterno(), 'all');

		//giro
		$em = $this->getDoctrine()->getManager();

		$proyectoBaeRepository = $em->getRepository(ProyectoBAE::class);

		$firstProyectoBae = $proyectoBaeRepository->findOneBy(
			['expediente' => $expediente->getId()],
			['id' => 'DESC']
		);
		$pdfMerge->addPDF('uploads/expedientes/comision/giro/'.$firstProyectoBae->getFirmado());

		if($firstProyectoBae){
			//
			if($firstProyectoBae->getFirmado()){
				//GIRO FIRMADO
				$pdfMerge->addPDF('uploads/expedientes/comision/giro/'.$firstProyectoBae->getFirmado());
			}

			if($firstProyectoBae->getPedido()){
				//PEDIDO FIRMADO
				$pdfMerge->addPDF('uploads/expedientes/comision/pedidos/'.$firstProyectoBae->getPedido());
			} 
			if($firstProyectoBae->getDigesto()){
				//DIGESTO FIRMADO
				$pdfMerge->addPDF('uploads/expedientes/comision/digesto/'.$firstProyectoBae->getDigesto());
			}
		}

		$pdfMerge->addPDF('uploads/expedientes/comision/dictamen/'.$dictamen->getDictamen());

		if($dictamen->getRama()){
			$pdfMerge->addPDF('uploads/expedientes/comision/ramas/'.$dictamen->getRama());
		}

		

		$pdfMerge->addPDF('/uploads/expedientes/pasedem/'.$dictamen->getRama());

	}
}
