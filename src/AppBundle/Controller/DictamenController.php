<?php

namespace AppBundle\Controller;

use AppBundle\Form\AltaDictamenType;
use AppBundle\Form\AsignarDictamenAExpteType;
use AppBundle\Form\CargarDictamenType;
use AppBundle\Form\CrearDictamenType;
use AppBundle\Form\Filter\DictamenFilterType;
use Doctrine\Common\Collections\ArrayCollection;
use MesaEntradaBundle\Entity\Dictamen;
use MesaEntradaBundle\Entity\LogExpediente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DictamenController extends Controller {
	public function indexAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( DictamenFilterType::class,
			null,
			[
				'method' => 'GET'
			] );


		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$dictamenes = $em->getRepository( 'MesaEntradaBundle:Dictamen' )->getQbAll();
		} else {

			$dictamenes = $em->getRepository( 'MesaEntradaBundle:Dictamen' )->getQbAll();
		}


		$paginator  = $this->get( 'knp_paginator' );
		$dictamenes = $paginator->paginate(
			$dictamenes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'dictamen/index.html.twig',
			[
				'filter_type' => $filterType->createView(),
				'dictamenes'  => $dictamenes
			] );
	}

	public function crearAction( Request $request ) {

		$esPresidenteComision = $this->getUser()->getPersona()->esPresidenteComision();

		if ( ! $esPresidenteComision ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'Ud. no es Presidente de una Comisión'
			);

			return $this->redirectToRoute( 'dictamen_index' );
		}

		$em = $this->getDoctrine()->getManager();

		$dictamen = new Dictamen();

		$form = $this->createForm( CrearDictamenType::class, $dictamen );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$dictamen->setPresidenteComision( $esPresidenteComision );

			$em->persist( $dictamen );
			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen creado correctamente'
			);

			return $this->redirectToRoute( 'dictamen_index' );
		}

		return $this->render( 'dictamen/crear.html.twig',
			[
				'form' => $form->createView()
			] );
	}

	public function altaDictamenAnteriorAction( Request $request ) {

		$dictamen = new Dictamen();

		$form = $this->createForm( AltaDictamenType::class, $dictamen );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();

			$dictamen->getExpediente()->setBorrador( false );
			$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBySlug( 'externo' );
			$dictamen->getExpediente()->setTipoExpediente( $tipoExpediente );

			$em->persist( $dictamen );
			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen creado correctamente'
			);

			return $this->redirectToRoute( 'dictamen_index' );
		}

		return $this->render( 'dictamen/alta.html.twig',
			[
				'form' => $form->createView()
			] );
	}

	public function editarDictamenAnteriorAction( Request $request, $id ) {

		$em = $this->getDoctrine()->getManager();

		$dictamen   = $em->getRepository( 'MesaEntradaBundle:Dictamen' )->find( $id );
		$expediente = $dictamen->getExpediente();

		$firmantesOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $dictamen->getFirmantes() as $firmanteDictamen ) {
			$firmantesOriginales->add( $firmanteDictamen );
		}

		// Log

		$campos = [ 'extractoDictamen' ];

		$valoresOriginales = [];
		foreach ( $campos as $campo ) {
			$getter                      = 'get' . ucfirst( $campo );
			$valoresOriginales[ $campo ] = [
				'valor'  => $expediente->{$getter}(),
				'getter' => $getter,
			];
		}

		$form = $this->createForm( AltaDictamenType::class, $dictamen );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			foreach ( $firmantesOriginales as $firmanteDictamen ) {
				if ( false === $dictamen->getFirmantes()->contains( $firmanteDictamen ) ) {

					$firmanteDictamen->setDictamen( null );

					$em->persist( $firmanteDictamen );

					$em->remove( $firmanteDictamen );
				}
			}

			// Log
			$log = new LogExpediente();
			$log->setExpediente( $expediente );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $expediente->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $expediente->{$campo['getter']}() );
				}
			}

			$em = $this->getDoctrine()->getManager();
			if ( count( $log->getCambios() ) > 0 ) {
				$em->persist( $log );
			}

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen modificado correctamente'
			);

			return $this->redirectToRoute( 'dictamen_editar', [ 'id' => $id ] );
		}

		return $this->render( ':dictamen:editar.html.twig',
			[
				'form'     => $form->createView(),
				'dictamen' => $dictamen
			] );
	}

	public function verDictamenAction( Request $request, $id ) {

		$em = $this->getDoctrine()->getManager();

		$dictamen = $em->getRepository( 'MesaEntradaBundle:Dictamen' )->find( $id );


		return $this->render( ':dictamen:ver.html.twig',
			[
				'dictamen' => $dictamen
			] );
	}

	public function imprimirDictamenAction( Request $request, $id ) {
		$title = 'Dictamen';

		$em = $this->getDoctrine()->getManager();

		$dictamen = $em->getRepository( 'MesaEntradaBundle:Dictamen' )->find( $id );

		if ( ! $dictamen->getPeriodoLegislativo() ) {
			$this->get( 'session' )->getFlashBag()->add(
				'error',
				'El dictamen no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute( 'dictamen_ver', [ 'id' => $dictamen->getId() ] );
		}

		$header = null;

		$header = $this->renderView( ':default:membrete.pdf.twig',
			[
				"periodo" => $dictamen->getPeriodoLegislativo(),
			] );


		$footer = $this->renderView( ':default:pie_pagina.pdf.twig' );

		$html = $this->renderView( 'dictamen/dictamen.pdf.twig',
			[
				'dictamen' => $dictamen,
				'title'    => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 6,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

	public function asignarAExpteAction( Request $request ) {
		$dictamen = new Dictamen();

		$form = $this->createForm( AsignarDictamenAExpteType::class, $dictamen );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();

			$expediente = $form->get( "expediente" )->getData();
			$dictamen->setExpediente( $expediente );

			$em->persist( $dictamen );
			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen creado correctamente'
			);

			return $this->redirectToRoute( 'dictamen_index' );
		}

		return $this->render( 'dictamen/asignar_a_expte.html.twig',
			[
				'form' => $form->createView()
			] );
	}
}