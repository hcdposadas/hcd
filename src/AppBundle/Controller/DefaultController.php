<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {
	public function indexAction() {

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) ) {
			return $this->redirectToRoute('easyadmin');
		}

		return $this->render( 'AppBundle:Default:index.html.twig' );
	}
}
