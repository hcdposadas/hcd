<?php

namespace App\Controller;

use App\Entity\Documento;
use App\Entity\Mocion;
use App\Entity\Parametro;
use App\Entity\Sesion;
use App\Entity\Voto;
use App\Form\MocionType;
use App\Service\NotificationsManager;
use App\Service\VotacionManager;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Mocion controller.
 *
 */
class MocionController extends AbstractController {

	protected $votacionManager;
	protected $notificacionesManager;

	public function __construct( VotacionManager $votacionManager, NotificationsManager $notificacionesManager ) {
		$this->votacionManager = $votacionManager;
		$this->notificacionesManager = $notificacionesManager;
	}

	/**
	 * Lists all mocion entities.
	 *
	 */
	public function index( Request $request ) {
		if ( ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SECRETARIO' ) ) {
			throw $this->createAccessDeniedException( 'Solo el Secretario puede lanzar mociones' );
		}
		$em = $this->getDoctrine()->getManager();

		try {
			$sesion = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();
		} catch ( NoResultException $e ) {
			$this->addFlash( 'warning', 'No existe sesión activa' );

			return $this->redirectToRoute( 'sesion_logout' );
		} catch ( NonUniqueResultException $e ) {
		}

		$bae = $sesion->getBae()->first();
		$od  = $sesion->getOd()->first();

		if ( ! $bae || ! $od ) {

			$this->addFlash( 'warning', 'La sesión no posee Plan de Labor' );

			return $this->redirectToRoute( 'sesion' );
		}

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'             => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'   => $bae->getProyectosDeDefensor(),
		];

		$dictamenes = [
			'DICTÁMENES DE DECLARACIÓN'  => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'   => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'    => $od->getDictamenesDeOrdenanza(),
		];

		$mocions = $em->getRepository( Mocion::class )->findByUltimaSesion( $sesion );

		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );

		return $this->render( 'mocion/index.html.twig',
			array(
				'mocions'       => $mocions,
				'sesion'        => $sesion,
				'cartaOrganica' => $cartaOrganica,
				'proyectos'     => $proyectos,
				'dictamenes'    => $dictamenes,
			) );
	}

	/**
	 * Creates a new mocion entity.
	 *
	 */
	public function new( Request $request ) {
		$mocion = new Mocion();
		try {
			$sesion = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();
		} catch ( NoResultException $e ) {
			$this->addFlash( 'warning', 'No existe sesión activa' );

			return $this->redirectToRoute( 'mocion_index' );
		} catch ( NonUniqueResultException $e ) {
		}


		if ( $request->get( 'tipo' ) === 'mocion-tipo-espontanea' ) {
			$mocion->setTipo( $this->getDoctrine()
			                       ->getRepository( Parametro::class )
			                       ->getBySlug( Mocion::TIPO_ESPONTANEA ) );
		}

		$form = $this->createForm( MocionType::class, $mocion );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$this->votacionManager->crear( $mocion );

			if ( $request->get( 'guardar-y-lanzar' ) ) {
				try {
					$this->votacionManager->lanzar( $mocion );
				} catch ( \Exception $ex ) {
					$this->addFlash(
						'error',
						$ex->getMessage()
					);
				}

				return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
			}

			return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
		}

		$enVotacion = $this->getDoctrine()->getRepository( Mocion::class )->getEnVotacion();

		if ( $enVotacion ) {
			$this->addFlash( 'warning', 'La moción Nº ' . $enVotacion . ' no está finalizada' );
		}

		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );

		$bae = $sesion->getBae()->first();
		$od  = $sesion->getOd()->first();

		if ( ! $bae || ! $od ) {

			$this->addFlash( 'warning', 'La sesión no posee Plan de Labor' );

			return $this->redirectToRoute( 'mocion_index' );
		}

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'             => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'   => $bae->getProyectosDeDefensor(),
		];

		$dictamenes = [
			'DICTÁMENES DE DECLARACIÓN'  => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'   => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'    => $od->getDictamenesDeOrdenanza(),
		];

		return $this->render( 'mocion/new.html.twig',
			array(
				'mocion'        => $mocion,
				'sesion'        => $sesion,
				'cartaOrganica' => $cartaOrganica,
				'form'          => $form->createView(),
				'proyectos'     => $proyectos,
				'dictamenes'    => $dictamenes,
			) );
	}

	/**
	 * Finds and displays a mocion entity.
	 *
	 */
	public function show( Request $request, Mocion $mocion ) {
		$deleteForm = $this->createDeleteForm( $mocion );
		$sesion     = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();
		$bae        = $sesion->getBae()->first();
		$od         = $sesion->getOd()->first();

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'             => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'   => $bae->getProyectosDeDefensor(),
		];

		$dictamenes    = [
			'DICTÁMENES DE DECLARACIÓN'  => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'   => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'    => $od->getDictamenesDeOrdenanza(),
		];
		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );

		return $this->render( 'mocion/show.html.twig',
			array(
				'sesion'        => $sesion,
				'mocion'        => $mocion,
				'cartaOrganica' => $cartaOrganica,
				'segundos'      => 15,
				'votar'         => false,
				'lanzar'        => false,
				'delete_form'   => $deleteForm->createView(),
				'proyectos'     => $proyectos,
				'dictamenes'    => $dictamenes,
			) );
	}

	public function mostrarResultado( Mocion $mocion ) {
		$textoMocion = '';
		if ( $expediente = $mocion->getExpediente() ) {
			$textoMocion = 'Expediente ' . $expediente . ': ' . $expediente->getExtracto();
		}

		$tipoMayoria = $mocion->getTipoMayoria();
		if ( $tipoMayoria ) {
			$tipoMayoria = 'Se aprueba con ' . $tipoMayoria;
		}

		$votos           = $mocion->getVotos();
		$votaronPositivo = [];
		$votaronNegativo = [];
		$aNoVotaron      = [];

		foreach ( $votos as $voto ) {
			switch ( $voto->getValor() ) {
				case Voto::VOTO_AFIRMATIVO:
					$votaronPositivo[] = strtoupper( $voto->getConcejal()->getPersona()->getApellido() );
					break;
				case Voto::VOTO_NEGATIVO:
					$votaronNegativo[] = strtoupper( $voto->getConcejal()->getPersona()->getApellido() );
					break;
				case Voto::VOTO_ABSTENCION:
					$aNoVotaron[] = strtoupper( $voto->getConcejal()->getPersona()->getApellido() );
					break;
			}
		}

		$this->notificacionesManager->notify(
			'votacion.resultados',
			array(
				'mocion'          => 'Moción Nº' . $mocion->__toString() . ' (Finalizada)',
				'textoMocion'     => $textoMocion,
				'tipoMayoria'     => $tipoMayoria,
				'sesion'          => $mocion->getSesion()->__toString(),
				'afirmativos'     => $mocion->getCuentaAfirmativos(),
				'negativos'       => $mocion->getCuentaNegativos(),
				'abstenciones'    => $mocion->getCuentaAbstenciones(),
				'total'           => $mocion->getCuentaTotal(),
				'aprobado'        => $mocion->isAprobado(),
				'votaronNegativo' => $votaronNegativo,
				'votaronPositivo' => $votaronPositivo,
				'seAbstuvieron'   => $aNoVotaron,
			)
		);

		return JsonResponse::create( array() );
	}

	public function mostrarPresentes() {
		$this->notificacionesManager->notify( 'votacion.finalizada', array() );

		return JsonResponse::create( array() );
	}

	/**
	 * Finds and displays a mocion entity.
	 *
	 */
	public function votar( Request $request, Mocion $mocion ) {
		$deleteForm = $this->createDeleteForm( $mocion );

		$enVotacion = $this->getDoctrine()
		                   ->getRepository( Mocion::class )
		                   ->getAllEnVotacion();

		if ( $enVotacion ) {

			foreach ( $enVotacion as $item ) {

				$urlToShow = $this->generateUrl( 'mocion_show', [ 'id' => $item->getId() ] );

				$mensaje = '<a href="' . $urlToShow . '">Nº ' . $item->getNumero() . '</a>';

				$this->addFlash(
					'error',
					'No se puede votar esta moción porque la Moción ' . $mensaje . ' se encuentra en votación.'
				);
			}

			return $this->redirectToRoute( 'mocion_show',
				array(
					'id' => $mocion->getId()
				) );
		}

		$sesion = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();

		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );

		return $this->render( 'mocion/show.html.twig',
			array(
				'mocion'        => $mocion,
				'sesion'        => $sesion,
				'cartaOrganica' => $cartaOrganica,
				'segundos'      => 15,
				'votar'         => true,
				'lanzar'        => false,
				'delete_form'   => $deleteForm->createView(),
			) );
	}

	/**
	 * Displays a form to edit an existing mocion entity.
	 *
	 */
	public function edit( Request $request, Mocion $mocion ) {
		$cartaOrganica = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );
		$deleteForm    = $this->createDeleteForm( $mocion );
		$editForm      = $this->createForm( MocionType::class, $mocion );
		$editForm->handleRequest( $request );
		$sesion = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();
		$bae    = $sesion->getBae()->first();
		$od     = $sesion->getOd()->first();

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'             => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'   => $bae->getProyectosDeDefensor(),
		];

		$dictamenes = [
			'DICTÁMENES DE DECLARACIÓN'  => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'   => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'    => $od->getDictamenesDeOrdenanza(),
		];

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'mocion_edit', array( 'id' => $mocion->getId() ) );
		}

		return $this->render( 'mocion/edit.html.twig',
			array(
				'sesion'        => $sesion,
				'mocion'        => $mocion,
				'cartaOrganica' => $cartaOrganica,
				'edit_form'     => $editForm->createView(),
				'delete_form'   => $deleteForm->createView(),
				'proyectos'     => $proyectos,
				'dictamenes'    => $dictamenes,
			) );
	}

	/**
	 * Deletes a mocion entity.
	 *
	 */
	public function delete( Request $request, Mocion $mocion ) {
		$form = $this->createDeleteForm( $mocion );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $mocion );
			$em->flush();
		}

		return $this->redirectToRoute( 'mocion_index' );
	}

	/**
	 * Creates a form to delete a mocion entity.
	 *
	 * @param Mocion $mocion The mocion entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( Mocion $mocion ) {
		return $this->createFormBuilder()
		            ->setAction( $this->generateUrl( 'mocion_delete', array( 'id' => $mocion->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function lanzarVotacion( Mocion $mocion ) {
		try {
			$votacion = $this->votacionManager->lanzar( $mocion );
		} catch ( \Exception $ex ) {
			$this->addFlash(
				'error',
				$ex->getMessage()
			);
		}

		return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function extenderVotacion( Mocion $mocion ) {
		try {
			$votacion = $this->votacionManager->extender( $mocion );
		} catch ( \Exception $ex ) {
			$this->addFlash(
				'error',
				$ex->getMessage()
			);
		}

		return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 */
	public function resultadosVotacion( Mocion $mocion ) {
		try {
			$votacion = $this->votacionManager->calcularResultados( $mocion );
		} catch ( \Exception $ex ) {
			$this->addFlash(
				'error',
				$ex->getMessage()
			);
		}

		return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
	}

	/**
	 * @param Mocion $mocion
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse
	 * @throws \Doctrine\ORM\OptimisticLockException
	 */
	public function finalizarVotacion( Mocion $mocion ) {
		try {
			$this->votacionManager->finalizar( $mocion );

			$this->addFlash(
				'success',
				'La votación finalizó correctamente'
			);
		} catch ( \Exception $ex ) {
			$this->addFlash(
				'error',
				$ex->getMessage()
			);
		}

		return $this->redirectToRoute( 'mocion_show', array( 'id' => $mocion->getId() ) );
	}


	/**
	 * @Route("/sesion/votar", name="app_votar", methods={"POST"})
	 */
	public function votoConcejal( Request $request ) {

		if ( ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ) {
			return JsonResponse::create( array(
				'status'  => 'error',
				'message' => 'Solo los concejales pueden votar',
			) );
		}

		try {
			$usuario = $this->getUser();

			$mocion = $this->getDoctrine()->getRepository( Mocion::class )->getEnVotacion();
			if ( ! $mocion ) {
				throw new Exception( 'No hay una moción en votación en este momento' );
			}

			$data      = json_decode( $request->getContent(), JSON_OBJECT_AS_ARRAY );
			$valorVoto = $data['voto'];

			if ( $valorVoto == 'no' ) {
				$valorVoto = Voto::VOTO_NEGATIVO;
			} else if ( $valorVoto == 'si' ) {
				$valorVoto = Voto::VOTO_AFIRMATIVO;
			} else {
				throw new Exception( 'El valor del voto no es válido (' . $valorVoto . ')' );
			}

			$voto = $this->votacionManager->votar( $mocion, $usuario, $valorVoto );

			return JsonResponse::create( array(
				'status' => 'success',
				'data'   => array(
					'voto' => array(
						'id' => $voto->getId(),
					),
				),
			) );
		} catch ( Exception $ex ) {
			return JsonResponse::create( array(
				'status'  => 'error',
				'message' => $ex->getMessage(),
			) );
		}
	}
}
