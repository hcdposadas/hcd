<?php

namespace App\Controller;

use App\Entity\Documento;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController {
	/**
	 * @Route("/default", name="default")
	 */
	public function index() {

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_ADMIN' ) ) {
			return $this->redirectToRoute( 'easyadmin' );
		}

		return $this->render( 'default/index.html.twig',
			[
				'controller_name' => 'DefaultController',
			] );
	}

	//sesion
	public function concejalAction() {
		return $this->render( ':default:concejal.html.twig' );
	}

	/**
	 * @Route("/pantalla", name="app_display")
	 */
	public function display() {
		return $this->render( 'default/display.html.twig' );
	}

	public function cartaOrganica() {
		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
			'slug' => 'carta-organica'
		] );

		return $this->render( 'default/index_embed.html.twig',
			[
				'titulo'    => 'Carta Orgánica',
				'documento' => $cartaOrganica
			] );

	}

}
