<?php

namespace AppBundle\Controller;

use AppBundle\Form\CargarDictamenType;
use AppBundle\Form\CrearDictamenType;
use AppBundle\Form\Filter\DictamenFilterType;
use MesaEntradaBundle\Entity\Dictamen;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
				'filter_type'=>$filterType->createView(),
				'dictamenes' => $dictamenes
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
}
