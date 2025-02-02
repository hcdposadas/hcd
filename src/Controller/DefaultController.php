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

	/**
	 * @Route("/pantalla2", name="app_display2")
	 */
	public function display2() {
		return $this->render( 'default/display2.html.twig' );
	}

		/**
	 * @Route("/pantalla3", name="app_display3")
	 */
	public function display3() {
		return $this->render( 'default/display3.html.twig' );
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

	public function reglamento() {
		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
			'slug' => 'reglamento-interno'
		] );

		return $this->render( 'default/index_embed.html.twig',
			[
				'titulo'    => 'Reglamento Interno',
				'documento' => $cartaOrganica
			] );

	}

	public function manual() {
		                  $doc1 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'manual'
                ] );
                $doc2 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'manualdec'
                ] );



                return $this->render( 'default/index_embed2.html.twig',
                        [
                                'titulo'    => ['Manual','Resolución'],
                                'documento' => [$doc1->getDocumento(),$doc2->getDocumento()]
                        ] );

	}

        public function manuallegis() {
                                                  $doc1 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'manuallegis'
                ] );
                $doc2 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'manuallegisdec'
                ] );



                return $this->render( 'default/index_embed2.html.twig',
                        [
                                'titulo'    => ['Manual','Resolución'],
                                'documento' => [$doc1->getDocumento(),$doc2->getDocumento()]
                        ] );

        }


	public function organigrama() {
		                                                  $doc1 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'organigrama1'
                ] );
                $doc2 = $this->getDoctrine()->getRepository( Documento::class )->findOneBy( [
                        'slug' => 'organigrama2'
                ] );


		return $this->render( 'default/index_embed2.html.twig',
			[
				'titulo'    => ['Organigrama Nivel Direcciones','Organigrama Integral'],
				'documento' => [$doc1->getDocumento(),$doc2->getDocumento()]
			] );

	}

	/**
	 * @Route("/pantallota", name="app_display_g")
	 */
	public function displayG() {
		return $this->render( 'default/displayG.html.twig' );
	}

}
