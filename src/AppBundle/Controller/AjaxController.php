<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dependencia;
use AppBundle\Entity\Persona;
use AppBundle\Form\DependenciaType;
use AppBundle\Form\PersonaType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class AjaxController extends Controller {
	public function getAjaxDefaultAction( Request $request ) {
		$value = strtoupper( $request->get( 'term' ) );
		//$value = $request->get('term');
		$class        = $request->get( 'class' );
		$property     = $request->get( 'property' );
		$searchMethod = $request->get( 'search_method' );
		$em           = $this->getDoctrine()->getManagerForClass( $class );

		$entities = $em->getRepository( $class )->$searchMethod( $value );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'label' => 'No se encontraron coincidencias',
				'value' => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $entity[ $property ]
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function getPersonaPorDniAction( Request $request ) {

		$em = $this->getDoctrine();

		$value    = $request->get( 'q' );
		$limit    = $request->get( 'page_limit' );
		$entities = $em->getRepository( 'AppBundle:Persona' )->getPersonaPorDni( $value, $limit, true );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $entity['nombre'] . ' ' . $entity['apellido']
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function getCargosPorNombreAction( Request $request ) {

		$em = $this->getDoctrine();

		$value    = $request->get( 'q' );
		$limit    = $request->get( 'page_limit' );
		$entities = $em->getRepository( 'MesaEntradaBundle:Iniciador' )->getCargosPorNombre( $value, $limit, true );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
//				foreach ( $entity['cargoPersona'] as $cargoPersona ) {
					$json[] = array(
						'id'   => $entity['id'],
						//'label' => $entity[$property],
						'text' => $entity['cargoPersona']['cargo']['nombre'] . ' ' . $entity['cargoPersona']['persona']['nombre'] . ' ' . $entity['cargoPersona']['persona']['apellido']
					);
//				}

			}
		}

		return new JsonResponse( $json );
	}

	public function formPersonaAction( Request $request ) {

		$persona = new Persona();
		$form    = $this->createForm( PersonaType::class, $persona );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $persona );
			$em->flush();

			return new JsonResponse( [ 'mensaje' => 'Persona Guardada Correctamente' ] );
		}
		$responseStatus = 200;
		if ( $request->getMethod() == 'POST' ) {
			$responseStatus = 500;
		}

		$formHtml = $this->renderView( '@App/Ajax/form_persona.html.twig',
			array(
				'form' => $form->createView()
			) );

		return new JsonResponse( [ 'form' => $formHtml ], $responseStatus );
	}

	public function getDependenciaPorNombreAction( Request $request ) {
		$em = $this->getDoctrine();

		$value    = $request->get( 'q' );
		$limit    = $request->get( 'page_limit' );
		$entities = $em->getRepository( 'AppBundle:Dependencia' )->getDependenciasPorNombre( $value, $limit, true );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $entity['nombre']
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function formDependenciaAction( Request $request ) {

		$persona = new Dependencia();
		$form    = $this->createForm( DependenciaType::class, $persona );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $persona );
			$em->flush();

			return new JsonResponse( [ 'mensaje' => 'Dependencia Guardada Correctamente' ] );
		}
		$responseStatus = 200;
		if ( $request->getMethod() == 'POST' ) {
			$responseStatus = 500;
		}

		$formHtml = $this->renderView( '@App/Ajax/form.html.twig',
			array(
				'form' => $form->createView()
			) );

		return new JsonResponse( [ 'form' => $formHtml ], $responseStatus );
	}
}
