<?php

namespace AppBundle\Controller;

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

		$concejal = $this->getUser()->getPersona();

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SECRETARIO' ) ) {
			$sesion = $this->getDoctrine()->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion()->getQuery()->getSingleResult();

			return $this->render( 'sesion/secretario.html.twig',
				array(
					'sesion'   => $sesion,
					'concejal' => $concejal,
				) );
		}

		return $this->render( 'sesion/index.html.twig',
			array(
				'concejal' => $concejal,
			) );
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

}
