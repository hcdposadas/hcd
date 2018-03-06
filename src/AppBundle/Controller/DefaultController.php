<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {
	public function indexAction() {

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) ) {
			return $this->redirectToRoute( 'easyadmin' );
		}

		return $this->render( 'AppBundle:Default:index.html.twig' );
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function concejalAction() {
		return $this->render( ':default:concejal.html.twig' );
	}

	/**
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function displayAction() {
		return $this->render( 'default/display.html.twig' );
	}

	public function cartaOrganicaAction() {
		$cartaOrganica = $this->getDoctrine()->getRepository( 'AppBundle:Documento' )->findOneBy( [
			'slug' => 'carta-organica'
		] );

		return $this->render('default/index_embed.html.twig', [
			'titulo' => 'Carta Orgánica',
			'documento' => $cartaOrganica
		]);

	}
}
