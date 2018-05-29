<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoletinAsuntoEntrado;
use AppBundle\Entity\OrdenDelDia;
use AppBundle\Entity\ProyectoBAE;
use AppBundle\Entity\Sesion;
use AppBundle\Form\BoletinAsuntoEntradoType;
use AppBundle\Form\ExtractoDictamenODType;
use AppBundle\Form\ExtractoProyectoBAEType;
use AppBundle\Form\Filter\SesionFilterType;
use AppBundle\Form\OrdenDelDiaType;
use AppBundle\Form\ProyectoBAEGiroType;
use AppBundle\Form\SesionCargarActaType;
use Doctrine\Common\Collections\ArrayCollection;
use MesaEntradaBundle\Entity\Expediente;
use MesaEntradaBundle\Entity\LogExpediente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

		$personaUsuario = $this->getUser()->getPersona();
		$cartaOrganica  = $this->getDoctrine()->getRepository( 'AppBundle:Documento' )->findOneBySlug( 'carta-organica' );

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ) {

			$sesion = $this->getDoctrine()->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion()->getQuery()->getSingleResult();

			return $this->render( 'sesion/index.html.twig',
				array(
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
					'sesion'        => $sesion,
				) );
		}

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_SECRETARIO' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_PROSECRETARIO' )
		) {
			$sesion = $this->getDoctrine()->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion()->getQuery()->getSingleResult();

			return $this->render( 'sesion/autoridades.html.twig',
				array(
					'sesion'        => $sesion,
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
				) );
		}

		return $this->redirectToRoute( 'sesion_logout' );
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

	public function indexSesionesAction( Request $request ) {

		$em = $this->getDoctrine()->getManager();


		$filterType = $this->createForm( SesionFilterType::class,
			null,
			[
				'method' => 'GET'
			] );
		$filterType->handleRequest( $request );
		if ( $filterType->get( 'buscar' )->isClicked() ) {
			$sesiones = $em->getRepository( 'AppBundle:Sesion' )->getQbBuscar( $filterType->getData() );
		} else {
			$sesiones = $em->getRepository( 'AppBundle:Sesion' )->getQbAll();
		}


		$paginator = $this->get( 'knp_paginator' );
		$sesiones  = $paginator->paginate(
			$sesiones,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);


		return $this->render( 'sesiones/index.html.twig',
			[
				'sesiones'    => $sesiones,
				'filter_type' => $filterType->createView()
			] );
	}

	public function verSesionAction( Request $request, $id ) {
		$em = $this->getDoctrine()->getManager();

		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $id );

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {
			if ( $sesion->getBae()->first() && $sesion->getOd()->first() ) {
				if ( ! $sesion->getBae()->first()->getCerrado() || ! $sesion->getOd()->first()->getCerrado() ) {
					$this->get( 'session' )->getFlashBag()->add(
						'info',
						'El Plan de labor aun no está conformado'
					);

					return $this->redirectToRoute( 'sesiones_index' );
				}
			}
		}

		$bae = $sesion->getBae()->first();
		$od  = $sesion->getOd()->first();

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


		return $this->render( 'sesiones/ver.html.twig',
			[
				'sesion'     => $sesion,
				'proyectos'  => $proyectos,
				'dictamenes' => $dictamenes,
			] );
	}

	public function conformarPlanDeLaborIndexAction( Request $request ) {
		$em       = $this->getDoctrine()->getManager();
		$sesionQb = $em->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion();
		$sesion   = null;


		if ( ! $sesionQb->getQuery()->getResult() ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'No hay una Sesión Activa Creada'
			);
		} else {
			$sesion = $sesionQb->getQuery()->getSingleResult();
		}


		if ( $request->getMethod() == 'POST' ) {
			$ordenDelDia   = new OrdenDelDia();
			$asuntoEntrado = new BoletinAsuntoEntrado();

			$sesion->addOd( $ordenDelDia );
			$sesion->addBae( $asuntoEntrado );

			$ordenDelDia->setSesion( $sesion );
			$ordenDelDia->setCerrado( false );
			$asuntoEntrado->setSesion( $sesion );
			$asuntoEntrado->setCerrado( false );

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El Plan de Labor se creó correctamente.'
			);

		}

		return $this->render( ':sesiones:conformar_plan_de_labor_index.html.twig',
			array(
				'sesion' => $sesion
			) );
	}

	public function asignarProyectosABAEAction( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		$bae = $sesion->getBae()->first();

		$proyectosBaeOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $bae->getProyectos() as $proyectoBae ) {
			$proyectosBaeOriginales->add( $proyectoBae );
		}

		$form = $this->createForm( BoletinAsuntoEntradoType::class, $bae );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			foreach ( $proyectosBaeOriginales as $proyectoBae ) {
				if ( false === $bae->getProyectos()->contains( $proyectoBae ) ) {
					$proyectoBae->setBoletinAsuntoEntrado( null );
					$em->remove( $proyectoBae );
				}
			}

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'BAE modificado correctamente'
			);

		}

		return $this->render( ':sesiones:asignar_proyectos_a_bae.html.twig',
			array(
				'sesion' => $sesion,
				'form'   => $form->createView()
			) );
	}

	public function editarExtractoBAEAction( Request $request, Expediente $expediente ) {

		$em = $this->getDoctrine()->getManager();

		$sesionQb = $em->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion();

		$sesion = $sesionQb->getQuery()->getSingleResult();

		$bae         = $sesion->getBae()->first();
		$proyectoBAE = $em->getRepository( 'AppBundle:ProyectoBAE' )->findOneBy(
			[
				'expediente'           => $expediente,
				'boletinAsuntoEntrado' => $bae,
			]
		);

		if ( ! $proyectoBAE ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'El Proyecto no está asignado al BAE'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );
		}

		// Estos son los campos a auditar en el log
		$campos = [ 'extracto' ];

		$valoresOriginales = [];
		foreach ( $campos as $campo ) {
			$getter                      = 'get' . ucfirst( $campo );
			$valoresOriginales[ $campo ] = [
				'valor'  => $proyectoBAE->{$getter}(),
				'getter' => $getter,
			];
		}

		$editForm = $this->createForm( ExtractoProyectoBAEType::class, $proyectoBAE );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$log = new LogExpediente();
			$log->setExpediente( $expediente );
			$log->setSesion( $sesion );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $proyectoBAE->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $proyectoBAE->{$campo['getter']}() );
				}
			}

			if ( count( $log->getCambios() ) > 0 ) {
				$em->persist( $log );
			}
			$em->persist( $proyectoBAE );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Extracto BAE guardado correctamente'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );
		}

		return $this->render( 'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'sesion'     => $sesion,
			) );
	}

	public function asignarDictamenesAODAction( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		$od = $sesion->getOd()->first();

		$dictamenesOdOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $od->getDictamenes() as $dictamenOd ) {
			$dictamenesOdOriginales->add( $dictamenOd );
		}

		$form = $this->createForm( OrdenDelDiaType::class, $od );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			foreach ( $dictamenesOdOriginales as $dictamenOd ) {
				if ( false === $od->getDictamenes()->contains( $dictamenOd ) ) {
					$dictamenOd->setOrdenDelDia( null );
					$em->remove( $dictamenOd );
				}
			}

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'OD modificado correctamente'
			);

		}

		return $this->render( ':sesiones:asignar_dictamenes_a_od.html.twig',
			array(
				'sesion' => $sesion,
				'form'   => $form->createView()
			) );
	}

    /**
     * @param Request $request
     * @param Expediente $expediente
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
	public function editarExtractoODAction( Request $request, Expediente $expediente ) {

		$em = $this->getDoctrine()->getManager();

		$sesionQb = $em->getRepository( 'AppBundle:Sesion' )->findQbUltimaSesion();

		/** @var Sesion $sesion */
		$sesion = $sesionQb->getQuery()->getSingleResult();

		/** @var OrdenDelDia $od */
		$od         = $sesion->getOd()->first();

		foreach ($od->getDictamenes() as $dod) {
		    if ($dod->getDictamen()->getExpediente()->getId() == $expediente->getId()) {
		        $dictamenOD = $dod;
		        break;
            }
        }
//		$dictamenOD = $em->getRepository( 'AppBundle:DictamenOD' )->findOneBy(
//			[
//				'expediente'  => $expediente,
//				'ordenDelDia' => $od,
//			]
//		);

		if ( ! $dictamenOD ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'El Dictamen no está asignado al OD'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );
		}

		// Estos son los campos a auditar en el log
		$campos = [ 'extracto' ];

		$valoresOriginales = [];
		foreach ( $campos as $campo ) {
			$getter                      = 'get' . ucfirst( $campo );
			$valoresOriginales[ $campo ] = [
				'valor'  => $dictamenOD->{$getter}(),
				'getter' => $getter,
			];
		}

		$editForm = $this->createForm( ExtractoDictamenODType::class, $dictamenOD );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$log = new LogExpediente();
			$log->setExpediente( $expediente );
			$log->setSesion( $sesion );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $dictamenOD->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $dictamenOD->{$campo['getter']}() );
				}
			}

			if ( count( $log->getCambios() ) > 0 ) {
				$em->persist( $log );
			}
			$em->persist( $dictamenOD );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Extracto OD guardado correctamente'
			);

			return $this->redirectToRoute( 'expedientes_legislativos_index' );
		}

		return $this->render( 'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'sesion'     => $sesion,
			) );
	}

	public function conformarPlanDeLaborConfirmarAction( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		if ( $sesion && $sesion->getActivo() ) {
			$od  = $sesion->getOd()->first();
			$bae = $sesion->getBae()->first();

			if ( ! $bae->getCerrado() && ! $od->getCerrado() ) {
				$bae->setCerrado( true );
				$od->setCerrado( true );

				$em->flush();

				$this->get( 'session' )->getFlashBag()->add(
					'success',
					'El plan de labor fue creado correctamente.'
				);

				if ( $this->notificarConcejales( $sesion ) ) {
					$this->get( 'session' )->getFlashBag()->add(
						'info',
						'Se ha enviado un mail a los concejales para notificarles que está disponible el plan de labor.'
					);
				} else {
					$this->get( 'session' )->getFlashBag()->add(
						'warning',
						'Hubo un problema tratando de enviar el mail a los concejales.
						Conctacte con el administrador.'
					);
				}


			} else {
				$this->get( 'session' )->getFlashBag()->add(
					'warning',
					'El Plan de Labor ya se encuentra Cerrado'
				);
			}
		} else {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'No existe una Sesión Activa'
			);
		}

		return $this->redirectToRoute( 'sesiones_conformar_plan_de_labor_index' );

	}

	public function notificarConcejales( Sesion $sesion ) {

		$mailer = $this->get( 'mailer' );

		$em                      = $this->getDoctrine()->getManager();
		$parametroMail           = $em->getRepository( 'AppBundle:Parametro' )->findOneBySlug( 'mail-concejales' );
		$parametroMailDefensor   = $em->getRepository( 'AppBundle:Parametro' )->findOneBySlug( 'mail-defensor' );
		$parametroMailSecretario = $em->getRepository( 'AppBundle:Parametro' )->findOneBySlug( 'mail-secretaria' );

		if ( $parametroMail && $parametroMailDefensor ) {
			$asunto = 'HCD Posadas - Plan de Labor ' . $sesion->getTitulo();

			$message = ( new \Swift_Message( $asunto ) );

			$message
				->setFrom( $this->getParameter( 'mailer_sender_as' ), $this->getParameter( 'mailer_sender' ) )
				->setTo( $parametroMail->getValor() )
				->addTo( $parametroMailDefensor->getValor() )
				->addTo( $parametroMailSecretario->getValor() )
				->setBody(
					$this->renderView(
						'emails/plan_de_labor.html.twig',
						[
							'sesion' => $sesion
						]
					),
					'text/html'
				);

			$mailer->send( $message );

			return true;
		} else {
			return false;
		}
	}

	public function imprimirBAEAction( Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {
			if ( $sesion->getBae()->first() && $sesion->getOd()->first() ) {
				if ( ! $sesion->getBae()->first()->getCerrado() || ! $sesion->getOd()->first()->getCerrado() ) {
					$this->get( 'session' )->getFlashBag()->add(
						'info',
						'El Plan de labor aun no está conformado'
					);

					return $this->redirectToRoute( 'sesiones_index' );
				}
			}
		}

		$bae = $sesion->getBae()->first();

		if ( ! $bae ) {
			$this->get( 'session' )->getFlashBag()->add(
				'error',
				'El Plan de Labor no Posee Boletin de Asuntos Entrados y/u Orden del Día.'
			);

			return $this->redirectToRoute( 'sesiones_index' );
		}

		$title = 'Boletín de Asuntos Entrados';

		$header = null;
		if ( $bae->getCerrado() ) {
			$header = $this->renderView( ':sesiones:encabezado_plan_de_labor.pdf.twig',
				[
					"sesion"    => $sesion,
					'documento' => $title
				] );
		}

		$footer = $this->renderView( ':default:pie_pagina.pdf.twig' );

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'             => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'   => $bae->getProyectosDeDefensor(),
		];

		$html = $this->renderView( ':sesiones:boletin_asuntos_entrados.pdf.twig',
			[
				'bae'       => $bae,
				'title'     => $title . ' - ' . $sesion->getTitulo(),
				'proyectos' => $proyectos,
				'sesion'    => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
//					'page-width'     => '220mm',
//					'page-height'     => '340mm',
//					'margin-left'    => "3cm",
//					'margin-right'   => "3cm",
					'margin-top'     => "8cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 5,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
//                    'margin-bottom' => "1cm"
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . ' - ' . $sesion->getTitulo() . '.pdf"'
			)
		);

	}

	public function imprimirODAction( Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		if ( $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_CONCEJAL' ) ||
		     $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_DEFENSOR' ) ) {
			if ( $sesion->getBae()->first() && $sesion->getOd()->first() ) {
				if ( ! $sesion->getBae()->first()->getCerrado() || ! $sesion->getOd()->first()->getCerrado() ) {
					$this->get( 'session' )->getFlashBag()->add(
						'info',
						'El Plan de labor aun no está conformado'
					);

					return $this->redirectToRoute( 'sesiones_index' );
				}
			}
		}

		$od = $sesion->getOd()->first();


		if ( ! $od ) {
			$this->get( 'session' )->getFlashBag()->add(
				'error',
				'El Plan de Labor no Posee Orden del Día.'
			);

			return $this->redirectToRoute( 'sesiones_index' );
		}

		$title = 'Orden del Día';

		$header = null;
		if ( $od->getCerrado() ) {
			$header = $this->renderView( ':sesiones:encabezado_plan_de_labor.pdf.twig',
				[
					"sesion"    => $sesion,
					'documento' => $title

				] );
		}

		$footer = $this->renderView( ':default:pie_pagina.pdf.twig' );

		$dictamenes = [
			'DICTÁMENES DE DECLARACIÓN'  => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'   => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'    => $od->getDictamenesDeOrdenanza(),
		];

		$html = $this->renderView( ':sesiones:orden_del_dia.pdf.twig',
			[
				'od'         => $od,
				'title'      => $title . ' - ' . $sesion->getTitulo(),
				'dictamenes' => $dictamenes,
				"sesion"     => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
//					'page-width'     => '220mm',
//					'page-height'     => '340mm',
//					'margin-left'    => "3cm",
//					'margin-right'   => "3cm",
					'margin-top'     => "8cm",
					'margin-bottom'  => "2cm",
					'header-html'    => $header,
					'header-spacing' => 5,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
//                    'margin-bottom' => "1cm"
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . ' - ' . $sesion->getTitulo() . '.pdf"'
			)
		);

	}

	public function cargarActaAction( Request $request, $sesionId ) {

		$em = $this->getDoctrine()->getManager();

		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );
		$form   = $this->createForm( SesionCargarActaType::class, $sesion );

		// Estos son los campos a auditar en el log
		$campos = [ 'acta' ];

		$valoresOriginales = [];
		foreach ( $campos as $campo ) {
			$getter                      = 'get' . ucfirst( $campo );
			$valoresOriginales[ $campo ] = [
				'valor'  => $sesion->{$getter}(),
				'getter' => $getter,
			];
		}

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$log = new LogExpediente();
			$log->setSesion( $sesion );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $sesion->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $sesion->{$campo['getter']}() );
				}
			}

			if ( count( $log->getCambios() ) > 0 ) {
				$em->persist( $log );
			}

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El acta se modificó correctamente.'
			);

		}

		return $this->render( ':sesiones:cargar_acta.html.twig',
			[
				'sesion' => $sesion,
				'form'   => $form->createView()
			]
		);
	}

	public function verCambiosActaAction(
		Request $request,
		Sesion $sesion,
		LogExpediente $log
	) {
		$this->denyAccessUnlessGranted( 'ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.' );

		return $this->render( ':sesiones:ver_cambios_acta.html.twig',
			array(
				'sesion' => $sesion,
				'log'    => $log,
			) );
	}

	public function imprimirActaAction( Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		$title = 'Acta';

		$footer = $this->renderView( ':default:pie_pagina.pdf.twig' );

		$html = $this->renderView( ':sesiones:acta.pdf.twig',
			[
				'title'  => $title . ' - ' . $sesion->getTitulo(),
				"sesion" => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$this->get( 'knp_snappy.pdf' )->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
					'margin-top'     => "2.5cm",
					'margin-bottom'  => "2.5cm",
					'margin-left'    => "3cm",
					'margin-right'   => "3cm",
//					'header-html'    => $header,
//					'header-spacing' => 5,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
				)
			)
			, 200, array(
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . ' - ' . $sesion->getTitulo() . '.pdf"'
			)
		);

	}

    public function proyectoBaeGiroAction(Request $request, Expediente $expediente)
    {

        if (!$this->get('security.authorization_checker')->isGranted('ROLE_LEGISLATIVO')) {
            $this->get('session')->getFlashBag()->add(
                'warning',
                'No tiene permisos para modificar los giros.'
            );

            return $this->redirectToRoute('app_homepage');
        }

        $em = $this->getDoctrine()->getManager();

        $sesionQb = $em->getRepository( Sesion::class )->findQbUltimaSesion();

        $sesion = $sesionQb->getQuery()->getSingleResult();

        $bae         = $sesion->getBae()->first();
        $proyectoBAE = $em->getRepository( ProyectoBAE::class )->findOneBy(
            [
                'expediente'           => $expediente,
                'boletinAsuntoEntrado' => $bae,
            ]
        );

        if ( ! $proyectoBAE ) {
            $this->get( 'session' )->getFlashBag()->add(
                'warning',
                'El Proyecto no está asignado al BAE'
            );

            return $this->redirectToRoute( 'expedientes_legislativos_index' );
        }

        $girosAComisionOriginal = new ArrayCollection();

        // Create an ArrayCollection of the current Tag objects in the database
        foreach ($proyectoBAE->getGiros() as $giro) {
            $girosAComisionOriginal->add($giro);
        }


        $editForm = $this->createForm(ProyectoBAEGiroType::class, $proyectoBAE);

        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            foreach ($girosAComisionOriginal as $giro) {
                if (false === $proyectoBAE->getGiros()->contains($giro)) {
                    $giro->setProyectoBae(null);
                    $em->remove($giro);
                }
            }

            $em->flush();
            $this->get('session')->getFlashBag()->add(
                'success',
                'Giro/s modificado/s correctamente'
            );

            return $this->redirectToRoute('proyecto_bae_giro', array('expediente' => $expediente->getId()));
        }

        return $this->render('expediente/proyecto_bae_giro.html.twig',
            array(
                'expediente' => $expediente,
                'edit_form' => $editForm->createView(),
            ));
    }

}
