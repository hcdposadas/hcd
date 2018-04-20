<?php

namespace MesaEntradaBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use MesaEntradaBundle\Entity\Expediente;
use MesaEntradaBundle\Entity\GiroAdministrativo;
use MesaEntradaBundle\Entity\IniciadorExpediente;
use MesaEntradaBundle\Form\ExpedienteAdministrativoExternoType;
use MesaEntradaBundle\Form\ExpedienteAdministrativoType;
use MesaEntradaBundle\Form\ExpedienteLegislativoExternoType;
use MesaEntradaBundle\Form\Filter\ExpedienteFilterType;
use MesaEntradaBundle\Form\Filter\SeguimientoExpedienteFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Expediente controller.
 *
 */
class ExpedienteController extends Controller {
	/**
	 * Lists all expediente entities.
	 *
	 */
	public function indexAction( Request $request ) {
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

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbBuscar( $filterType->getData() );
		} else {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesMesaEntrada();
		}


		$paginator = $this->get( 'knp_paginator' );

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
	public function newAction( Request $request ) {
		$expediente = new Expediente();
		$form       = $this->createForm( 'MesaEntradaBundle\Form\ExpedienteType', $expediente );
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
	public function showAction( Request $request, Expediente $expediente ) {


		$referer = $request->headers
			->get( 'referer' );

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
	public function editAction( Request $request, Expediente $expediente ) {


		$iniciadoresOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $expediente->getIniciadores() as $iniciadorExpediente ) {
			$iniciadoresOriginales->add( $iniciadorExpediente );
		}

		$editForm = $this->createForm( 'MesaEntradaBundle\Form\ExpedienteType', $expediente );
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
	public function deleteAction( Request $request, Expediente $expediente ) {
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
		            ->setAction( $this->generateUrl( 'expediente_delete', array( 'id' => $expediente->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	public function seguimientoExpedienteAction( Request $request ) {

		$filterForm  = $this->createForm( SeguimientoExpedienteFilterType::class );
		$expedientes = array();
		if ( $request->isMethod( 'post' ) ) {
			$filterForm->handleRequest( $request );
			$data = $filterForm->getData();
			$em   = $this->getDoctrine()->getManager();
//            print_r($data);
//            exit;
			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->search( $data );

		}

		return $this->render( 'expediente/seguimiento.html.twig',
			array(
				'filter_form' => $filterForm->createView(),
				'entities'    => $expedientes
			) );
	}

	public function seguimientoExpedienteTimelineAction( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->find( $id );

		$referer = $request->headers
			->get( 'referer' );

		return $this->render( 'expediente/timeline.html.twig',
			array(
				'expediente' => $expediente,
				'referer'    => $referer
			) );

	}

	public function imprimirCaratulaAction( $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->find( $id );
		$title      = 'Carátula';

		$html = $this->renderView( 'expediente/caratula.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
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

	public function imprimirGiroAction( $id, $giroId ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->find( $id );
		if ( strtoupper( $expediente->getTipoExpediente() ) == 'INTERNO' ) {
			$giro = $em->getRepository( 'MesaEntradaBundle:GiroAdministrativo' )->find( $giroId );
			$giro = $giro->getAreaDestino();
		} else {
			$giro = $em->getRepository( 'MesaEntradaBundle:Giro' )->find( $giroId );
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
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
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

	public function expedientesLegislativosIndexAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
			'slug' => 'externo'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );


		} else {

			if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {

				$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesLegislativoTipo( $tipoExpediente );

				$expedientes = $expedientes->andWhere( 'e.expediente is not null' );

//			$expedientes = $expedientes->innerJoin( 'e.giros', 'giros' );

//				$expedientes->join( 'e.giros', 'giros' )->where('count(giros) > 0');
//			$sq = $this->getDoctrine()->getRepository('MesaEntradaBundle:Giro')->createQueryBuilder('giros')
//			                                                                   ->where('giros.expediente = e');
//
//			$expedientes->andWhere($expedientes->expr()->not($expedientes->expr()->exists($sq->getDQL())));

			} else {
				$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
				$expedientes = $expedientes->andWhere( 'e.expediente is null' );
			}

		}

		$paginator = $this->get( 'knp_paginator' );

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

	public function proyectosIndexAction( Request $request ) {


		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {

			$autor = $this->getUser()->getPersona()->getCargoPersona()->first()->getIniciador();

			$proyectos = $this->getDoctrine()->getRepository( 'MesaEntradaBundle:Expediente' )->getQbProyecetosPorConcejal( $autor );


			$paginator = $this->get( 'knp_paginator' );

			$proyectos = $paginator->paginate(
				$proyectos,
				$request->query->get( 'page', 1 )/* page number */,
				10/* limit per page */
			);


			return $this->render( 'expediente/proyectos_index.html.twig',
				array( 'proyectos' => $proyectos ) );

		} else {
			throw $this->createAccessDeniedException();
		}


	}

	public function showProyectoAction( Expediente $expediente ) {

		return $this->render( 'expediente/proyecto_show.html.twig',
			array(
				'expediente' => $expediente,
			) );

	}

	public function newProyectoAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();

//		$iniciarComo = [];
//
//		foreach ( $this->getUser()->getPersona()->getCargoPersona() as $cargoPersona ) {
//			if ( $cargoPersona->getIniciador() ) {
//				$iniciarComo[] = $cargoPersona->getIniciador()->first();
//			}
//		}

		$expediente = new Expediente();
		$form       = $this->createForm( 'MesaEntradaBundle\Form\ProyectoType',
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
				$areaDestino        = $em->getRepository( 'AppBundle:AreaAdministrativa' )->findOneBy( [
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				] );
				$giroAdministrativo->setAreaDestino( $areaDestino );
				$giroAdministrativo->setExpediente( $expediente );
				$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


				$expediente->addGiroAdministrativo( $giroAdministrativo );
				$em->persist( $giroAdministrativo );
				$toRoute = 'proyecto_show';

			}

			$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
				'slug' => 'externo'
			] );

			$expediente->setTipoExpediente( $tipoExpediente );

			$periodoLegislativo = $em->getRepository( 'AppBundle:PeriodoLegislativo' )->findOneBy( [
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
//				'iniciarComo' => $iniciarComo,
				'form'       => $form->createView(),
			) );
	}

	public function editProyectoAction( Request $request, Expediente $expediente ) {

		$em = $this->getDoctrine()->getManager();

//		$iniciarComo = [];
//
//		foreach ( $this->getUser()->getPersona()->getCargoPersona() as $cargoPersona ) {
//			if ( $cargoPersona->getIniciador() ) {
//				$iniciarComo[] = $cargoPersona->getIniciador()->first();
//			}
//		}

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


		$editForm = $this->createForm( 'MesaEntradaBundle\Form\ProyectoType', $expediente );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			$toRoute = 'proyecto_edit';

			foreach ( $iniciadoresOriginales as $iniciadore ) {
				if ( false === $expediente->getIniciadores()->contains( $iniciadore ) ) {
					$iniciadore->setExpediente( null );
					$em->remove( $iniciadore );
				}
			}

			foreach ( $anexosOriginales as $anexo ) {
				if ( false === $expediente->getIniciadores()->contains( $anexo ) ) {
					$anexo->setExpediente( null );
					$em->remove( $anexo );
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
				$areaDestino        = $em->getRepository( 'AppBundle:AreaAdministrativa' )->findOneBy( [
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

	public function imprimirProyectoAction( $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->find( $id );


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

		$header = $this->renderView( ':default:membrete.pdf.twig',
			[
				"periodo"      => $expediente->getPeriodoLegislativo(),
				'dataToEncode' => $dataToEncode
			] );

		$footer = $this->renderView( ':default:pie_pagina.pdf.twig' );

		$html = $this->renderView( 'expediente/proyecto.pdf.twig',
			[
				'expediente' => $expediente,
				'title'      => $title,
			]
		);

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
				array(
//					'margin-left'    => "3cm",
//					'margin-right'   => "3cm",
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
//					'header-spacing' => 10,
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

	public function impresionProyectoAction( Request $request ) {

		$expediente = null;
		$form       = null;

		if ( $request->getMethod() == 'POST' ) {
			$em = $this->getDoctrine()->getManager();

			$form = $this->createForm( 'MesaEntradaBundle\Form\AsignarHojasType',
				$expediente,
				[
					'action' => $this->generateUrl( 'expediente_impresion_proyecto' )
				] );

			$form->handleRequest( $request );

			if ( $form->get( 'asignar' )->isClicked() ) {
				$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->findOneByCodigoReferencia( $request->get( 'codigoReferencia' ) );


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

			$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->findOneByCodigoReferencia( $codigoReferencia );
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

	public function asignarNumeroExpedienteAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();


		$expediente       = null;
		$codigoReferencia = null;
		$form             = null;

		if ( $request->getMethod() == 'POST' ) {

			$form = $this->createForm( 'MesaEntradaBundle\Form\AsignarNumeroType', $expediente );

			$form->handleRequest( $request );

			if ( $form->get( 'asignar' )->isClicked() ) {
//				$codigoReferencia = $request->get( 'codigoReferencia' );
//
//				$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->findOneByCodigoReferencia( $codigoReferencia );
//				$form->setData( $expediente );

				if ( $form->isSubmitted() && $form->isValid() ) {

					$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->findOneByCodigoReferencia( $request->get( 'codigoReferencia' ) );

//			Prosecretaria Legislativa

					$giroAdministrativo = new GiroAdministrativo();
					$areaDestino        = $em->getRepository( 'AppBundle:AreaAdministrativa' )->findOneBy( [
						'nombre' => 'Prosecretaria Legislativas'
					] );
					$giroAdministrativo->setAreaDestino( $areaDestino );
					$giroAdministrativo->setExpediente( $expediente );
					$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


					$expediente->addGiroAdministrativo( $giroAdministrativo );
					$em->persist( $giroAdministrativo );

//					$periodoLegislativo = $form->getData()->getPeriodoLegislativo();
//					$expediente->setPeriodoLegislativo($periodoLegislativo);
					$expediente->setExpediente( $form->getData()->getExpediente() );
					$expediente->setLetra( $form->getData()->getLetra() );

//					$em->persist( $expediente );
					$em->flush();

					return $this->redirectToRoute( 'expediente_show', [ 'id' => $expediente->getId() ] );
				}

			}

			$codigoReferencia = substr( $request->get( 'codigoReferencia' ),
				7,
				strlen( $request->get( 'codigoReferencia' ) ) );

			$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->findOneByCodigoReferencia( $codigoReferencia );


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

	public function expedienteImprimirEtiquetaAction( Request $request, $id ) {
		$em         = $this->getDoctrine()->getManager();
		$expediente = $em->getRepository( 'MesaEntradaBundle:Expediente' )->find( $id );

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
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
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

//  EXPEDIENTES ADMINISTRATIVOS

	public function expedientesAdministrativosIndexAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
			'slug' => 'interno'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
		}


		$paginator = $this->get( 'knp_paginator' );

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

	public function nuevoExpedienteAdministrativoAction( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
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

	public function editarExpedienteAdministrativoAction( Request $request, Expediente $expediente ) {

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

	//  EXPEDIENTES LEGISLATIVOS EXTERNOS

	public function expedientesLegislativoExternosIndexAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
			'slug' => 'externo'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbBuscarExpedientesLegislativosExternos( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesLegislativosExternos( $tipoExpediente );
		}


		$paginator = $this->get( 'knp_paginator' );

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

	public function nuevoExpedienteLegislativoExternoAction( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
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

	public function editarExpedienteLegislativoExternoAction( Request $request, Expediente $expediente ) {

		$form = $this->createForm( ExpedienteLegislativoExternoType::class, $expediente );

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

		return $this->render( 'expediente/new_legislativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}

	//  EXPEDIENTES ADMINISTRATIVOS EXTERNOS

	public function expedientesAdministrativoExternosIndexAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();

		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
			'slug' => 'interno'
		] );

		$filterType = $this->createForm( ExpedienteFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );

		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbBuscar( $filterType->getData(),
				$tipoExpediente );
		} else {

			$expedientes = $em->getRepository( 'MesaEntradaBundle:Expediente' )->getQbExpedientesMesaEntradaTipo( $tipoExpediente );
		}


		$paginator = $this->get( 'knp_paginator' );

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

	public function nuevoExpedienteAdministrativoExternoAction( Request $request ) {

		$em             = $this->getDoctrine()->getManager();
		$tipoExpediente = $em->getRepository( 'MesaEntradaBundle:TipoExpediente' )->findOneBy( [
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

	public function editarExpedienteAdministrativoExternoAction( Request $request, Expediente $expediente ) {

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

			return $this->redirectToRoute( 'expediente_administrativo_externo_editar',
				[ 'id' => $expediente->getId() ] );

		}

		return $this->render( 'expediente/new_administrativo_externo.html.twig',
			[
				'form'       => $form->createView(),
				'expediente' => $expediente
			] );
	}
}
