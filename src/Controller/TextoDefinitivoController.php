<?php

namespace App\Controller;

use App\Entity\Parametro;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use App\Entity\Dictamen;
use App\Entity\TextoDefinitivo;
use App\Entity\TipoProyecto;
use App\Form\Filter\TextoDefinitivoFilterType;
use App\Form\TextoDefinitivoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
	public function index(PaginatorInterface $paginator, Request $request ) {
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
		$textoDefinitivo = new Textodefinitivo();
		$form            = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$expediente = $form->get( "dictamen" )->get( 'expediente' )->getData();
			$textoDefinitivo->getDictamen()->setExpediente( $expediente );
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

		$editForm = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$editForm->remove('dictamen');
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();
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
		$textoDefinitivo = new Textodefinitivo();
		$em              = $this->getDoctrine()->getManager();

		$tieneTextoDefinitivo = $em->getRepository( TextoDefinitivo::class )->findOneBy( [ 'dictamen' => $dictamen ] );

		if ( $tieneTextoDefinitivo ) {
			return $this->redirectToRoute( 'texto_definitivo_show', [ 'id' => $tieneTextoDefinitivo->getId() ] );
		}

		$textoDefinitivo->setDictamen( $dictamen );
		$form = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$form->remove( 'dictamen' );

		if ( $dictamen->getTipoProyecto()->getId() !== TipoProyecto::TIPO_ORDENANZA ) {
			$form->remove( 'rama' );
		}

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			if ( ! $textoDefinitivo->getTexto() ) {
				$this->get( 'session' )->getFlashBag()->add(
					'warning',
					'El texto no puede estar vacío'
				);

				return $this->redirectToRoute( 'texto_definitivo_asignar', [ 'dictamen' => $dictamen->getId() ] );
			}


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
	public function imprimir(Pdf $knpSnappyPdf, TextoDefinitivo $textoDefinitivo ) {

		$expediente = $textoDefinitivo->getDictamen()->getExpediente();

		$title   = $textoDefinitivo->getDictamen();
		$leyenda = $this->getDoctrine()->getRepository( Parametro::class )->findOneBySlug( 'texto-definitivo-leyenda' );
		$rama    = null;

		if ( $textoDefinitivo->getDictamen()->getTipoProyecto()->getId() == TipoProyecto::TIPO_ORDENANZA ) {
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

//        return new Response($html);

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
}
