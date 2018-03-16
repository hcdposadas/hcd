<?php

namespace AppBundle\Controller;

use AppBundle\Form\Filter\SesionFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Sesion controller.
 *
 */
class SesionController extends Controller {
	/**
	 * Lists all sesion entities.
	 *
	 */
	public function indexAction() {

		$personaUsuario = $this->getUser()->getPersona();
		$cartaOrganica  = $this->getDoctrine()->getRepository( 'AppBundle:Documento' )->findOneBySlug( 'carta-organica' );

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ) {
			return $this->render( 'sesion/index.html.twig',
				array(
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
				) );
		}

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SECRETARIO' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' )
		) {
			$sesion = $this->getDoctrine()->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion()->getQuery()->getSingleResult();

			return $this->render( 'sesion/autoridades.html.twig',
				array(
					'sesion'        => $sesion,
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
				) );
		}

		return $this->redirectToRoute( 'sesion_logout' );
	}

	public function loginAction( Request $request ) {

		$authUtils = $this->get( 'security.authentication_utils' );
		// get the login error if there is one
		$error = $authUtils->getLastAuthenticationError();

		// last username entered by the user
		$lastUsername = $authUtils->getLastUsername();

		return $this->render( 'sesion/login.html.twig',
			array(
				'last_username' => $lastUsername,
				'error'         => $error,
			) );
	}

	public function indexSesionesAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();


		$filterType = $this->createForm( SesionFilterType::class,
			null,
			[
				'method' => 'GET'
			] );
		$filterType->handleRequest( $request );
		if ( $filterType->get( 'buscar' )->isClicked() ) {
			$sesiones = $em->getRepository( 'AppBundle:Sesion' )->getQbBuscar( $filterType->getData() );
		} else {
			$sesiones = $em->getRepository( 'AppBundle:Sesion' )->getQbAll();
		}


		$paginator = $this->get( 'knp_paginator' );
		$sesiones  = $paginator->paginate(
			$sesiones,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);


		return $this->render( 'sesiones/index.html.twig',
			[
				'sesiones'   => $sesiones,
				'filter_type' => $filterType->createView()
			] );
	}

	public function verSesionAction( Request $request, $id ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $id );

		return $this->render( 'sesiones/ver.html.twig',
			[
				'sesion' => $sesion
			] );
	}

}
