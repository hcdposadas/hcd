<?php

namespace AppBundle\Controller;

use AppBundle\Entity\BoletinAsuntoEntrado;
use AppBundle\Entity\OrdenDelDia;
use AppBundle\Entity\Sesion;
use AppBundle\Form\BoletinAsuntoEntradoType;
use AppBundle\Form\Filter\SesionFilterType;
use AppBundle\Form\OrdenDelDiaType;
use AppBundle\Form\SesionCargarActaType;
use Doctrine\Common\Collections\ArrayCollection;
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
			return $this->render( 'sesion/index.html.twig',
				array(
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
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


		return $this->render( 'sesiones/ver.html.twig',
			[
				'sesion' => $sesion
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

	public function asignarDictamenesAODAction( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( 'AppBundle:Sesion' )->find( $sesionId );

		$od = $sesion->getOd()->first();

		$form = $this->createForm( OrdenDelDiaType::class, $od );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

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

		$em                    = $this->getDoctrine()->getManager();
		$parametroMail         = $em->getRepository( 'AppBundle:Parametro' )->findOneBySlug( 'mail-concejales' );
		$parametroMailDefensor = $em->getRepository( 'AppBundle:Parametro' )->findOneBySlug( 'mail-defensor' );

		if ( $parametroMail && $parametroMailDefensor ) {
			$asunto = 'HCD Posadas - Plan de Labor ' . $sesion->getTitulo();

			$message = ( new \Swift_Message( $asunto ) );

			$message
				->setFrom( $this->getParameter( 'mailer_sender_as' ), $this->getParameter( 'mailer_sender' ) )
				->setTo( $parametroMail->getValor() )
				->addTo( $parametroMailDefensor->getValor() )
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
            'PROYECTOS DE CONCEJALES' => $bae->getProyectosDeConcejales(),
            'PROYECTOS DEL DEFENSOR DEL PUEBLO' => $bae->getProyectosDeDefensor(),
        ];

        $html = $this->renderView(':sesiones:boletin_asuntos_entrados.pdf.twig', [
            'bae' => $bae,
            'title' => $title . ' - ' . $sesion->getTitulo(),
            'proyectos' => $proyectos,
        ]);

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
            'DICTÁMENES DE DECLARACIÓN' => $od->getDictamenesDeDeclaracion(),
            'DICTÁMENES DE COMUNICACIÓN' => $od->getDictamenesDeComunicacion(),
            'DICTÁMENES DE RESOLUCIÓN' => $od->getDictamenesDeResolucion(),
            'DICTÁMENES DE ORDENANZA' => $od->getDictamenesDeOrdenanza(),
        ];

        $html = $this->renderView(':sesiones:orden_del_dia.pdf.twig', [
            'od' => $od,
            'title' => $title . ' - ' . $sesion->getTitulo(),
            'dictamenes' => $dictamenes,
        ]);

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

}
