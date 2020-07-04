<?php

namespace App\Controller;

use App\Entity\Giro;
use App\Entity\ProyectoBAE;
use App\Entity\TipoExpediente;
use App\Form\AsignarHojasType;
use App\Form\AsignarNumeroType;
use App\Form\ExpedienteType;
use App\Form\ProyectoType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Expediente;
use App\Entity\GiroAdministrativo;
use App\Entity\IniciadorExpediente;
use App\Entity\Log;
use App\Form\EditarExtractoType;
use App\Form\ExpedienteAdministrativoExternoType;
use App\Form\ExpedienteAdministrativoType;
use App\Form\ExpedienteExtractoType;
use App\Form\ExpedienteLegislativoExternoType;
use App\Form\Filter\ExpedienteFilterType;
use App\Form\Filter\ProyectoFilterType;
use App\Form\Filter\SeguimientoExpedienteFilterType;
use App\Form\NuevoGiroExpedienteComisionType;
use App\Form\NuevoGiroExpedienteDependenciaType;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Form\FormError;

/**
 * Expediente controller.
 *
 */
class ExpedienteController extends AbstractController {
	/**
	 * Lists all expediente entities.
	 *
	 */
	public function index(PaginatorInterface $paginator, Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

//		if ( $request->query->has( $filterType->getName() ) ) {
//			$filterType->submit( $request->query->get( $filterType->getName() ) );
//		}

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscar( $filterType->getData() );
		} else {

			$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesMesaEntrada();
		}




		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}

	/**
	 * Creates a new expediente entity.
	 *
	 */
	public function new( Request $request ) {
		$expediente = new Expediente();
		$form       = $this->createForm( ExpedienteType::class, $expediente );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);


			return $this->redirectToRoute( 'expediente_show', array( 'id' => $expediente->getId() ) );
		}

		return $this->render( 'expediente/new.html.twig',
			array(
				'expediente' => $expediente,
				'form'       => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a expediente entity.
	 *
	 */
	public function show( Request $request, Expediente $expediente ) {


		$referer = $request->headers
			->get( 'referer' );
//
//		$route = $request->getUri();
//
////		TODO is current route
//		if ($referer == null || ($referer == $route)) {
//			$referer = $this->generateUrl('expedientes_legislativos_index');
//		}

		return $this->render( 'expediente/show.html.twig',
			array(
				'expediente' => $expediente,
				'referer'    => $referer
			) );
	}

	/**
	 * Displays a form to edit an existing expediente entity.
	 *
	 */
	public function edit( Request $request, Expediente $expediente ) {


		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getIniciadores() as $iniciadorExpediente ) {
			$iniciadoresOriginales->add( $iniciadorExpediente );
		}

		$editForm = $this->createForm( ExpedienteType::class, $expediente );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			$em = $this->getDoctrine()->getManager();

			foreach ( $iniciadoresOriginales as $iniciadorExpediente ) {
				if ( false === $expediente->getIniciadores()->contains( $iniciadorExpediente ) ) {
					// remove the Task from the Tag
//					$iniciadorExpediente->getExpediente()->removeElement( $expediente );

					// if it was a many-to-one relationship, remove the relationship like this
					// $tag->setTask(null);
					$iniciadorExpediente->setExpediente( null );

					$em->persist( $iniciadorExpediente );

					// if you wanted to delete the Tag entirely, you can also do that
					$em->remove( $iniciadorExpediente );
				}
			}


			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute( 'expediente_edit', array( 'id' => $expediente->getId() ) );
		}

		return $this->render( 'expediente/edit.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
			) );
	}

	/**
	 * Deletes a expediente entity.
	 *
	 */
	public function delete( Request $request, Expediente $expediente ) {
		$form = $this->createDeleteForm( $expediente );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $expediente );
			$em->flush();
		}

		return $this->redirectToRoute( 'expediente_index' );
	}

	/**
	 * Creates a form to delete a expediente entity.
	 *
	 * @param Expediente $expediente The expediente entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( Expediente $expediente ) {
		return $this->createFormBuilder()
		            ->set( $this->generateUrl( 'expediente_delete', array( 'id' => $expediente->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	public function seguimientoExpediente( Request $request ) {

		$filterForm  = $this->createForm( SeguimientoExpedienteFilterType::class );
		$expedientes = array();
		if ( $request->isMethod( 'post' ) ) {
			$filterForm->handleRequest( $request );
			$data = $filterForm->getData();
			$em   = $this->getDoctrine()->getManager();
//            print_r($data);
//            exit;
			$expedientes = $em->getRepository( Expediente::class )->search( $data );

		}

		return $this->render( 'expediente/seguimiento.html.twig',
			array(
				'filter_form' => $filterForm->createView(),
				'entities'    => $expedientes
			) );
	}

	public function seguimientoExpedienteTimeline( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );
        $girosBae = $em->getRepository( ProyectoBAE::class )->findByExpediente( $expediente );

		$referer = $request->headers
			->get( 'referer' );

		return $this->render( 'expediente/timeline.html.twig',
			array(
				'expediente' => $expediente,
				'girosBae' => $girosBae,
				'referer'    => $referer
			) );

	}

	public function imprimirCaratula(Pdf $knpSnappyPdf, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );
		$title      = 'Carátula';

		$html = $this->renderView( 'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				array(
					'margin-left'  => "3cm",
					'margin-right' => "3cm",
					'margin-top'   => "3cm",
//                    'margin-bottom' => "1cm"
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);

	}

	public function imprimirGiro(Pdf $knpSnappyPdf, $id, $giroId, $tipoExpediente ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );

		if ( strtoupper( $tipoExpediente ) == 'ADMINISTRATIVO' ) {
			$giro = $em->getRepository( GiroAdministrativo::class )->find( $giroId );
			$giro = $giro->getAreaDestino();
		} elseif ( strtoupper( $tipoExpediente ) == 'LEGISLATIVO' ) {
			$giro = $em->getRepository( Giro::class )->find( $giroId );
			$giro = $giro->getComisionDestino();
		}

		$title = 'Giro';

		$html = $this->renderView( 'expediente/giro.pdf.twig',
			[
				'expediente' => $expediente,
				'giro'       => $giro,
				'title'      => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				array(
					'margin-left'  => "3cm",
					'margin-right' => "3cm",
					'margin-top'   => "3cm",
//                    'margin-bottom' => "1cm"
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);
	}

//	EXPEDIENTES LEGISLATIVOS

	public function expedientesLegislativosIndex(PaginatorInterface $paginator,  Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'externo'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );


		} else {

			if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {

				$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesLegislativoTipo( $tipoExpediente );

				$expedientes = $expedientes->andWhere( 'e.expediente is not null' );


			} else {
				$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
				$expedientes = $expedientes->andWhere( 'e.expediente is not null' );
			}

		}
		
		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/expedientes_legislativos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}

	public function proyectosIndex( PaginatorInterface $paginator, Request $request ) {


		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {

			$autor = $this->getUser()->getPersona()->getCargoPersona()->first()->getIniciador();


			$filterType = $this->createForm( ProyectoFilterType::class,
				null,
				[
					'method' => 'GET'
				] );

			$filterType->handleRequest( $request );


			if ( $filterType->get( 'buscar' )->isClicked() ) {
				$proyectos = $this->getDoctrine()->getRepository( Expediente::class )->getQbProyecetosPorConcejal( $autor,
					$filterType->getData() );
			} else {
				$proyectos = $this->getDoctrine()->getRepository( Expediente::class )->getQbProyecetosPorConcejal( $autor );
			}



			$proyectos = $paginator->paginate(
				$proyectos,
				$request->query->get( 'page', 1 )/* page number */,
				10/* limit per page */
			);


			return $this->render( 'expediente/proyectos_index.html.twig',
				[
					'proyectos'   => $proyectos,
					'filter_type' => $filterType->createView()
				] );

		} else {
			throw $this->createAccessDeniedException();
		}


	}

	public function showProyecto( Expediente $expediente ) {

		return $this->render( 'expediente/proyecto_show.html.twig',
			array(
				'expediente' => $expediente,
			) );

	}

	public function newProyecto( Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$expediente = new Expediente();
		$form       = $this->createForm( ProyectoType::class,
			$expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {


			$toRoute = 'proyecto_edit';
			if ( $form->get( 'guardar' )->isClicked() ) {
				$expediente->setBorrador( true );

			}

			if ( $form->get( 'guardarYEnviar' )->isClicked() ) {
				$expediente->setBorrador( false );
				$codigoReferencia = md5( uniqid( 'hcd_' ) );
				$expediente->setCodigoReferencia( $codigoReferencia );

//				Departamento de Mesa de Entradas y Salidas

				$giroAdministrativo = new GiroAdministrativo();
				$areaDestino        = $em->getRepository( 'App:AreaAdministrativa' )->findOneBy( [
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				] );
				$giroAdministrativo->setAreaDestino( $areaDestino );
				$giroAdministrativo->setExpediente( $expediente );
				$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


				$expediente->addGiroAdministrativo( $giroAdministrativo );
				$em->persist( $giroAdministrativo );
				$toRoute = 'proyecto_show';

			}

			$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
				'slug' => 'externo'
			] );

			$expediente->setTipoExpediente( $tipoExpediente );

			$periodoLegislativo = $em->getRepository( 'App:PeriodoLegislativo' )->findOneBy( [
				'anio' => $expediente->getFecha()->format( 'Y' )
			] );

			if ( ! $periodoLegislativo ) {
				$this->get( 'session' )->getFlashBag()->add(
					'warning',
					'No existe periodo legislativo creado. Contacte con el Administrador'
				);

				return $this->render( 'expediente/proyecto_new.html.twig',
					array(
						'expediente' => $expediente,
						'form'       => $form->createView(),
					) );
			}

			$expediente->setPeriodoLegislativo( $periodoLegislativo );

			$iniciadorExpediente = new IniciadorExpediente();

			$iniciadorExpediente->setExpediente( $expediente );

			$autor = $this->getUser()->getPersona()->getCargoPersona()->first()->getIniciador()->first();
			$iniciadorExpediente->setIniciador( $autor );
			$iniciadorExpediente->setAutor( true );

			$expediente->addIniciadore( $iniciadorExpediente );


			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Proyecto creado correctamente'
			);


			return $this->redirectToRoute( $toRoute, array( 'id' => $expediente->getId() ) );
		}

		return $this->render( 'expediente/proyecto_new.html.twig',
			array(
				'expediente' => $expediente,
				'form'       => $form->createView(),
			) );
	}

	public function editProyecto( Request $request, Expediente $expediente ) {

		if ( ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) &&
		     ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) &&
		     ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'No tiene permisos para modificar un Proyecto.'
			);

			return $this->redirectToRoute( 'app_homepage' );
		}

		$em = $this->getDoctrine()->getManager();

		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getIniciadores() as $iniciadore ) {
			$iniciadoresOriginales->add( $iniciadore );
		}

		$anexosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getAnexos() as $anexo ) {
			$anexosOriginales->add( $anexo );
		}

		$girosAComisionOriginal = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getGiros() as $giro ) {
			$girosAComisionOriginal->add( $giro );
		}


		$editForm = $this->createForm( ProyectoType::class, $expediente );
		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			$editForm->remove( 'tipoProyecto' );
			$editForm->remove( 'fecha' );
		}
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			$toRoute = 'proyecto_edit';

			foreach ( $iniciadoresOriginales as $iniciadore ) {
				if ( false === $expediente->getIniciadores()->contains( $iniciadore )) {
					$iniciadore->setExpediente( null );
					$em->remove( $iniciadore );
				}
			}

			foreach ( $anexosOriginales as $anexo ) {
				if ( false === $expediente->getAnexos()->contains( $anexo ) ) {
					$anexo->setExpediente( null );
					$em->remove( $anexo );
				}
			}

			foreach ( $girosAComisionOriginal as $giro ) {
				if ( false === $expediente->getGiros()->contains( $giro ) ) {
					$giro->setExpediente( null );
					$em->remove( $giro );
				}
			}

			if ( $editForm->get( 'guardar' )->isClicked() ) {
				if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ) {
					$expediente->setBorrador( true );
				}
			}

			if ( $editForm->get( 'guardarYEnviar' )->isClicked() ) {
				$expediente->setBorrador( false );
				$codigoReferencia = md5( uniqid( 'hcd_' ) );
				$expediente->setCodigoReferencia( $codigoReferencia );

//				Departamento de Mesa de Entradas y Salidas

				$giroAdministrativo = new GiroAdministrativo();
				$areaDestino        = $em->getRepository( 'App:AreaAdministrativa' )->findOneBy( [
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				] );
				$giroAdministrativo->setAreaDestino( $areaDestino );
				$giroAdministrativo->setExpediente( $expediente );
				$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


				$expediente->addGiroAdministrativo( $giroAdministrativo );
				$em->persist( $giroAdministrativo );
				$toRoute = 'proyecto_show';

			}

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Proyecto modificado correctamente'
			);

			return $this->redirectToRoute( $toRoute, array( 'id' => $expediente->getId() ) );
		}

		return $this->render( 'expediente/proyecto_edit.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
			) );
	}

	public function imprimirProyecto( Pdf $knpSnappyPdf, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );


		$dataToEncode = $expediente->getCodigoReferencia();
		if ( $expediente->getBorrador() ) {
			$dataToEncode = null;
		}

		$title = 'Proyecto';

		if ( ! $expediente->getPeriodoLegislativo() ) {
			$this->get( 'session' )->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute( 'expediente_show', [ 'id' => $expediente->getId() ] );
		}

		$header = null;
		if ( ! $expediente->getBorrador() ) {
			$header = $this->renderView( 'default/membrete.pdf.twig',
				[
					"periodo"      => $expediente->getPeriodoLegislativo(),
					'dataToEncode' => $dataToEncode
				] );
		}

		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$html = $this->renderView( 'expediente/proyecto.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
//					'page-width'     => '220mm',
//					'page-height'     => '340mm',
//					'margin-left'    => "3cm",
//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 4,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
//                    'margin-bottom' => "1cm"
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
			)
		);

	}

	public function impresionProyecto( Request $request ) {

		$expediente = null;
		$form       = null;

		if ( $request->getMethod() == 'POST' ) {
			$em = $this->getDoctrine()->getManager();

			$form = $this->createForm( AsignarHojasType::class,
				$expediente,
				[
					'action' => $this->generateUrl( 'expediente_impresion_proyecto' )
				] );

			$form->handleRequest( $request );

			if ( $form->get( 'asignar' )->isClicked() ) {
				$expediente = $em->getRepository( Expediente::class )->findOneByCodigoReferencia( $request->get( 'codigoReferencia' ) );


				$numeroDeHojas = $form->getData()->getNumeroDeHojas();
				$expediente->setNumeroDeHojas( $numeroDeHojas );
				$em->flush();

				$this->get( 'session' )->getFlashBag()->add(
					'success',
					'Las hojas se han asignado correctamente'
				);

				return $this->render( 'expediente/impresion.html.twig',
					array(
						'expediente' => null
					) );

			}

			$codigoReferencia = substr( $request->get( 'codigoReferencia' ),
				7,
				strlen( $request->get( 'codigoReferencia' ) ) );

			$expediente = $em->getRepository( Expediente::class )->findOneByCodigoReferencia( $codigoReferencia );
			if ( ! $expediente ) {
				$this->get( 'session' )->getFlashBag()->add(
					'error',
					'El Proyecto no existe o el código no es válido'
				);

				return $this->render( 'expediente/impresion.html.twig',
					array(
						'expediente' => $expediente
					) );
			}


			return $this->render( 'expediente/impresion.html.twig',
				array(
					'expediente' => $expediente,
					'form'       => $form->createView()
				) );
		}

		return $this->render( 'expediente/impresion.html.twig',
			array(
				'expediente' => $expediente
			) );
	}

	public function asignarNumeroExpediente( Request $request ) {
		$em = $this->getDoctrine()->getManager();


		$expediente       = null;
		$codigoReferencia = null;
		$form             = null;

		if ( $request->getMethod() == 'POST' ) {

			$form = $this->createForm( AsignarNumeroType::class, $expediente );

			$form->handleRequest( $request );

			if ( $form->get( 'asignar' )->isClicked() ) {


				if ( $form->isSubmitted() && $form->isValid() ) {


					$expediente = $em->getRepository( Expediente::class )->findOneByCodigoReferencia( $request->get( 'codigoReferencia' ) );

					$existeExpediente = $em->getRepository( Expediente::class )->findOneBy(
						[
							'expediente'         => $form->getData()->getExpediente(),
							'periodoLegislativo' => $expediente->getPeriodoLegislativo()
						]
					);

					if ( $existeExpediente ) {
						$form->get( 'expediente' )->addError( new FormError( 'Ya existe el expediente en el año' ) );

						return $this->render( 'expediente/asignar_numero_expediente.html.twig',
							array(
								'expediente'       => $existeExpediente,
								'form'             => $form->createView(),
								'codigoReferencia' => $request->get( 'codigoReferencia' )
							) );

					} else {
						//			Prosecretaria Legislativa

						$giroAdministrativo = new GiroAdministrativo();
						$areaDestino        = $em->getRepository( 'App:AreaAdministrativa' )->findOneBy( [
							'nombre' => 'Prosecretaria Legislativa'
						] );
						$giroAdministrativo->setAreaDestino( $areaDestino );
						$giroAdministrativo->setExpediente( $expediente );
						$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


						$expediente->addGiroAdministrativo( $giroAdministrativo );
						$em->persist( $giroAdministrativo );

						$expediente->setExpediente( $form->getData()->getExpediente() );
						$expediente->setLetra( $form->getData()->getLetra() );
						$expediente->setFechaPresentacion( new \DateTime( 'now' ) );
						$expediente->setAsignadoPor( $this->getUser() );

						$em->flush();

						return $this->redirectToRoute( 'expediente_show', [ 'id' => $expediente->getId() ] );
					}


				}

			}

			$codigoReferencia = substr( $request->get( 'codigoReferencia' ),
				7,
				strlen( $request->get( 'codigoReferencia' ) ) );

			$expediente = $em->getRepository( Expediente::class )->findOneByCodigoReferencia( $codigoReferencia );


			if ( $expediente->getExpediente() && $expediente->getLetra() ) {
				$this->get( 'session' )->getFlashBag()->add(
					'info',
					'El Proyecto ya tiene asignado un Número y una Letra'
				);

				return $this->render( 'expediente/asignar_numero_expediente.html.twig',
					array(
						'expediente'       => $expediente,
						'codigoReferencia' => $codigoReferencia
					) );
			}

			if ( $form ) {
				$form = $form->createView();
			}


		}

		return $this->render( 'expediente/asignar_numero_expediente.html.twig',
			array(
				'expediente'       => $expediente,
				'form'             => $form,
				'codigoReferencia' => $codigoReferencia
			) );
	}

	public function expedienteImprimirEtiqueta(Pdf $knpSnappyPdf, Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );

		if ( ! $expediente->getPeriodoLegislativo() ) {
			$this->get( 'session' )->getFlashBag()->add(
				'error',
				'El expediente no tiene periodo legislativo asignado'
			);

			return $this->redirectToRoute( 'expediente_show', [ 'id' => $expediente->getId() ] );
		}

		$html = $this->renderView( 'expediente/etiqueta.pdf.twig',
			[
				'expediente' => $expediente,
			] );


//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				array(
					'margin-left'   => "0mm",
					'margin-right'  => "0mm",
					'margin-top'    => "1mm",
					'margin-bottom' => "0mm",
					'page-width'    => '5cm',
					'page-height'   => '2.5cm'
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="etiqueta.pdf"'
			)
		);
	}

	public function cargarExtracto( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );
		$form       = $this->createForm( ExpedienteExtractoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Extracto Cargado Correctamentes'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );

		}


		return $this->render( 'expediente\cargar_extracto.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView()
			] );
	}

	public function nuevoGiro( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );
		$form       = $this->createForm( NuevoGiroExpedienteComisionType::class, $expediente );
		$girosBae = $em->getRepository( ProyectoBAE::class )->findByExpediente( $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El expediente se ha girado a la/s comision/es'
			);

			return $this->redirectToRoute( 'expediente_nuevo_giro_legislativo', [ 'id' => $id ] );

		}


		return $this->render( 'expediente/nuevo_giro_legislativo.html.twig',
			[
				'expediente' => $expediente,
				'girosBae' => $girosBae,
				'form'       => $form->createView()
			] );
	}

//  EXPEDIENTES ADMINISTRATIVOS

	public function expedientesAdministrativosIndex(PaginatorInterface $paginator, Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'interno'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
		}




		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/expedientes_administrativos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}

	public function nuevoExpedienteAdministrativo( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'interno'
		] );

		$expediente = new Expediente();
		$expediente->setTipoExpediente( $tipoExpediente );

		$form = $this->createForm( ExpedienteAdministrativoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute( 'expediente_administrativo_editar', [ 'id' => $expediente->getId() ] );

		}

		return $this->render( 'expediente/new_administrativo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	public function editarExpedienteAdministrativo( Request $request, Expediente $expediente ) {

		$form = $this->createForm( ExpedienteAdministrativoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

		}

		return $this->render( 'expediente/new_administrativo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	public function nuevoGiroAdministrativo( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( Expediente::class )->find( $id );
		$form       = $this->createForm( NuevoGiroExpedienteDependenciaType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El expediente se ha girado a la/s dependencia/s'
			);

			return $this->redirectToRoute( 'expedientes_administrativos_index' );

		}


		return $this->render( 'expediente/nuevo_giro_administrativo.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView()
			] );
	}

	//  EXPEDIENTES LEGISLATIVOS EXTERNOS

	public function expedientesLegislativoExternosIndex(PaginatorInterface $paginator, Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'externo'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscarExpedientesLegislativosExternos( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesLegislativosExternos( $tipoExpediente );
		}




		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/expedientes_legislativos_externos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}

	public function nuevoExpedienteLegislativoExterno( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'externo'
		] );

		$expediente = new Expediente();
		$expediente->setTipoExpediente( $tipoExpediente );

		$form = $this->createForm( ExpedienteLegislativoExternoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$expediente->setBorrador( false );

			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute( 'expediente_legislativo_externo_editar', [ 'id' => $expediente->getId() ] );
		}

		return $this->render( 'expediente/new_legislativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	public function editarExpedienteLegislativoExterno( Request $request, Expediente $expediente ) {

		$form = $this->createForm( ExpedienteLegislativoExternoType::class, $expediente );

		$em = $this->getDoctrine()->getManager();

		$girosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getGiros() as $giro ) {
			$girosOriginales->add( $giro );
		}

		$girosAdministrativosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getGiroAdministrativos() as $giroAdministrativo ) {
			$girosAdministrativosOriginales->add( $giroAdministrativo );
		}

		$adjuntosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getExpedientesAdjunto() as $expAdj ) {
			$adjuntosOriginales->add( $expAdj );
		}

		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getIniciadores() as $iniciador ) {
			$iniciadoresOriginales->add( $iniciador );
		}


		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			foreach ( $girosOriginales as $giro ) {
				if ( false === $expediente->getGiros()->contains( $giro ) ) {
					$giro->setExpediente( null );
					$em->remove( $giro );
				}
			}

			foreach ( $girosAdministrativosOriginales as $giroAdministrativo ) {
				if ( false === $expediente->getGiroAdministrativos()->contains( $giroAdministrativo ) ) {
					$giroAdministrativo->setExpediente( null );
					$em->remove( $giroAdministrativo );
				}
			}

			foreach ( $adjuntosOriginales as $expAdj ) {
				if ( false === $expediente->getExpedientesAdjunto()->contains( $expAdj ) ) {
					$expAdj->setExpediente( null );
					$em->remove( $expAdj );
				}
			}

			foreach ( $iniciadoresOriginales as $iniciador ) {
				if ( false === $expediente->getIniciadores()->contains( $iniciador ) ) {
					$iniciador->setExpediente( null );
					$em->remove( $iniciador );
				}
			}

			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute( 'expediente_legislativo_externo_editar', [ 'id' => $expediente->getId() ] );

		}

		return $this->render( 'expediente/new_legislativo_externo.html.twig',
			[
				'form'       => $form->createView(),
//				'expediente' => $expediente
			] );
	}

	//  EXPEDIENTES ADMINISTRATIVOS EXTERNOS

	public function expedientesAdministrativoExternosIndex(PaginatorInterface $paginator, Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'interno'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
		}




		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/expedientes_administrativos_externos_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}

	public function nuevoExpedienteAdministrativoExterno( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'interno'
		] );

		$expediente = new Expediente();
		$expediente->setTipoExpediente( $tipoExpediente );

		$form = $this->createForm( ExpedienteAdministrativoExternoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$expediente->setBorrador( false );
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente creado correctamente'
			);

			return $this->redirectToRoute( 'expediente_administrativo_externo_editar',
				[ 'id' => $expediente->getId() ] );

		}

		return $this->render( 'expediente/new_administrativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	public function editarExpedienteAdministrativoExterno( Request $request, Expediente $expediente ) {

		$form = $this->createForm( ExpedienteAdministrativoExternoType::class, $expediente );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente modificado correctamente'
			);

			return $this->redirectToRoute( 'expediente_administrativo_externo_editar',
				[ 'id' => $expediente->getId() ] );

		}

		return $this->render( 'expediente/new_administrativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	/**
	 * @param Request $request
	 * @param Expediente $expediente
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function editarExtracto( Request $request, Expediente $expediente ) {
		$this->denyAccessUnlessGranted( 'ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.' );

		// Estos son los campos a auditar en el log
		$campos = [ 'extracto' ];

		$valoresOriginales = [];
		foreach ( $campos as $campo ) {
			$getter                      = 'get' . ucfirst( $campo );
			$valoresOriginales[ $campo ] = [
				'valor'  => $expediente->{$getter}(),
				'getter' => $getter,
			];
		}

		$editForm = $this->createForm( EditarExtractoType::class, $expediente );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$log = Log::forEntity( $expediente );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $expediente->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $expediente->{$campo['getter']}() );
				}
			}

			$em = $this->getDoctrine()->getManager();
			if ( $log->hasCambios() ) {
				$em->persist( $log );
			}
			$em->persist( $expediente );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Extracto guardado correctamente'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );
		}

		return $this->render( 'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'logs'       => []
			) );
	}

	/**
	 * @param Request $request
	 * @param Expediente $expediente
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
	 */
	public function showEdicionExtracto(
		Request $request,
		Expediente $expediente,
		Log $logExpediente
	) {
		$this->denyAccessUnlessGranted( 'ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.' );

		return $this->render( 'expediente/showEdicionExtracto.html.twig',
			array(
				'expediente'    => $expediente,
				'logExpediente' => $logExpediente,
			) );
	}


	public function incorporarProyectosASesionIndex( PaginatorInterface $paginator,Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( TipoExpediente::class )->findOneBy( [
			'slug' => 'externo'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( Expediente::class )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );


		} else {

			$expedientes = $em->getRepository( Expediente::class )->getQbExpedientesLegislativoTipo( $tipoExpediente );

			$expedientes = $expedientes->andWhere( 'e.expediente is not null' );

		}


		$expedientes = $paginator->paginate(
			$expedientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'expediente/incorporar_expedientes_a_sesion_index.html.twig',
			array(
				'expedientes' => $expedientes,
				'filter_type' => $filterType->createView()
			) );
	}
}
