<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dependencia;
use AppBundle\Entity\Persona;
use AppBundle\Form\DependenciaAjaxType;
use AppBundle\Form\PersonaType;
use Endroid\QrCode\QrCode;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use UsuariosBundle\Entity\Usuario;

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

		$value = $request->get( 'q' );
		$limit = $request->get( 'page_limit' );
//		$entities = $em->getRepository( 'MesaEntradaBundle:Iniciador' )->getCargosPorNombre( $value, $limit, true );
		$entities = $em->getRepository( 'MesaEntradaBundle:Iniciador' )->getACargosPorNombre( $value, $limit );

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
					'text' => $entity['cargo'] . ' ' . $entity['nombre_persona'] . ' ' . $entity['apellido_persona']
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
		$form    = $this->createForm( DependenciaAjaxType::class, $persona );
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

	public function getTiposProyectosAction( Request $request ) {

		$em = $this->getDoctrine();

		$id = $request->get( 'id' );

		$tipoProyecto = $em->getRepository( 'MesaEntradaBundle:TipoProyecto' )->find( $id );

		if ( ! $tipoProyecto ) {
			return new JsonResponse( 'No se encontro el tipo de proyeto', 404 );
		}

		$response = '';
		if ( $tipoProyecto->getTextoPorDefecto() ) {
			$response = $tipoProyecto->getTextoPorDefecto();
		}

		return new JsonResponse( $response );
	}

	public function getMisCargosPorNombreAction( Request $request ) {

		$em = $this->getDoctrine();

		$value    = $request->get( 'q' );
		$limit    = $request->get( 'page_limit' );
		$entities = $em->getRepository( 'MesaEntradaBundle:Iniciador' )->getACargosPorNombre( $value, $limit );

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
					'text' => $entity['cargo'] . ' ' . $entity['nombre_persona'] . ' ' . $entity['apellido_persona']
				);
//				}

			}
		}

		return new JsonResponse( $json );
	}

	public function getUltimaSesionAction() {

		$json = $this->getDoctrine()->getRepository( 'AppBundle:Sesion' )->findUltimaSesion();

		return new JsonResponse( $json[0] );
	}

	public function buscarExpedienteAction( Request $request ) {

		$data = $request->get( 'data' );
		$data = json_decode( $data, true );

		if ( $data['expediente'] ) {
			$expediente = explode( ' ', $data['expediente'] );
			if ( count( $expediente ) == 3 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
				$data['anio']       = $expediente[2];
			} else if ( count( $expediente ) == 2 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
			} else if ( count( $expediente ) == 1 ) {
				$data['expediente'] = $expediente[0];
			}
		}

		$json = $this->getDoctrine()->getRepository( 'MesaEntradaBundle:Expediente' )->buscarExpedientesSesion( $data );

		return new JsonResponse( $json );
	}

	public function enviarMailCodigoProyectoAction( Request $request ) {

		$expedienteId = $request->get( 'expedienteId' );
		$expediente   = $this->getDoctrine()->getRepository( 'MesaEntradaBundle:Expediente' )->find( $expedienteId );

		if ( ! $expediente ) {
			return new JsonResponse( 'No se encontro el tipo de proyeto', 404 );
		}

		$mailer = $this->get( 'mailer' );

		$mail = $this->getUser()->getEmail();

		$asunto = 'HCD Posadas - Código Impresión Proyecto';

		$code = new QrCode( $expediente->getCodigoReferencia() );
		$code->setLogoPath( $this->get( 'kernel' )->getRootDir() . '/../web/apple-touch-icon.png' )
		     ->setLogoWidth( 50 );

		$nombreAdjunto = $expediente->getId() . '.png';

		$message = ( new \Swift_Message( $asunto ) );

		$img = $message->embed( \Swift_Image::newInstance( $code->writeString(),
			$nombreAdjunto,
			$code->getContentType() ) );

		$message
			->setFrom( $this->getParameter( 'mailer_sender_as' ), $this->getParameter( 'mailer_sender' ) )
			->setTo( $mail )
			->setBody(
				$this->renderView(
					'emails/codigo_proyecto.html.twig',
					[
						'expediente' => $expediente,
						'img'        => $img
					]
				),
				'text/html'
			);

		$mailer->send( $message );

		return new JsonResponse( 'ok' );
	}

	public function getProyectosBAEAction( Request $request ) {
		$em = $this->getDoctrine();

		$value = $request->get( 'q' );
		$limit = $request->get( 'page_limit' );


		if ( $value ) {
			$expediente = explode( ' ', $value );
			if ( count( $expediente ) == 3 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
				$data['anio']       = $expediente[2];
			} else if ( count( $expediente ) == 2 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
			} else if ( count( $expediente ) == 1 ) {
				$data['expediente'] = $expediente[0];
			}
		}


		$entities = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getProyectosBAE( $data );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$anio   = $entity['anio'] ? $entity['anio'] : $entity['periodoLegislativo']['anio'];
				$text   = $entity['expediente'] . '-' . strtoupper( $entity['letra'] ) . '-' . $anio;
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $text
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function getDictamenesODAction( Request $request ) {
		$em = $this->getDoctrine();

		$value = $request->get( 'q' );
		$limit = $request->get( 'page_limit' );


		if ( $value ) {
			$expediente = explode( ' ', $value );
			if ( count( $expediente ) == 3 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
				$data['anio']       = $expediente[2];
			} else if ( count( $expediente ) == 2 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
			} else if ( count( $expediente ) == 1 ) {
				$data['expediente'] = $expediente[0];
			}
		}


		$entities = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getDictamenesOD( $data );

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$anio   = $entity['anio'] ? $entity['anio'] : $entity['periodoLegislativo']['anio'];
				$text   = $entity['expediente'] . '-' . strtoupper( $entity['letra'] ) . '-' . $anio;
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $text
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function getExpedientesAction( Request $request ) {
		$em = $this->getDoctrine();

		$value = $request->get( 'q' );
		$limit = $request->get( 'page_limit' );


		if ( $value ) {
			$expediente = explode( ' ', $value );
			if ( count( $expediente ) == 3 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
				$data['anio']       = $expediente[2];
			} else if ( count( $expediente ) == 2 ) {
				$data['expediente'] = $expediente[0];
				$data['letra']      = $expediente[1];
			} else if ( count( $expediente ) == 1 ) {
				$data['expediente'] = $expediente[0];
			}
		}


		$entities = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientes( $data )
		               ->join( 'e.periodoLegislativo', 'pl' )
		               ->addSelect( 'pl' )
		               ->getQuery()->getArrayResult();

		$json = array();

		if ( ! count( $entities ) ) {
			$json[] = array(
				'text' => 'No se encontraron coincidencias',
				'id'   => ''
			);
		} else {

			foreach ( $entities as $entity ) {
				$anio   = $entity['anio'] ? $entity['anio'] : $entity['periodoLegislativo']['anio'];
				$text   = $entity['expediente'] . '-' . strtoupper( $entity['letra'] ) . '-' . $anio;
				$json[] = array(
					'id'   => $entity['id'],
					//'label' => $entity[$property],
					'text' => $text
				);
			}
		}

		return new JsonResponse( $json );
	}

	public function consultarSesionesAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$entities = $em->getRepository( 'AppBundle:Sesion' )->getQbAll()
		               ->getQuery()->getArrayResult();

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
					'text' => $entity['titulo'],
					'acta' => $entity['acta']
				);
			}
		}

		return new JsonResponse( $json );

	}

	public function getUsuariosAction() {
		$usuarios = $this->getDoctrine()
		                 ->getManager()
		                 ->getRepository( Usuario::class )
		                 ->findBy( [ 'enabled' => true ] );

		$usuarios = array_map( function ( Usuario $usuario ) {
			return [
				'id'       => $usuario->getId(),
				'username' => $usuario->getUsername(),
				'nombre'   => $usuario->getPersona() ? $usuario->getPersona()->getNombreCompleto() : null,
				'roles'    => $usuario->getRoles(),
			];
		},
			$usuarios );

		return JsonResponse::create( $usuarios );
	}
}
