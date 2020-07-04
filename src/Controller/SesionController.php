<?php

namespace App\Controller;

use App\Entity\BoletinAsuntoEntrado;
use App\Entity\DictamenOD;
use App\Entity\Documento;
use App\Entity\Mocion;
use App\Entity\OrdenDelDia;
use App\Entity\Parametro;
use App\Entity\PeriodoLegislativo;
use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use App\Form\BoletinAsuntoEntradoType;
use App\Form\ExtractoDictamenODType;
use App\Form\ExtractoProyectoBAEType;
use App\Form\Filter\SesionFilterType;
use App\Form\OrdenDelDiaType;
use App\Form\PlanificarSesionType;
use App\Form\ProyectoBAEGiroType;
use App\Form\SesionCargarActaType;
use App\Form\SesionCargarHomenajeType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\Entity\Expediente;
use App\Entity\Log;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\GeneratorInterface;
use Knp\Snappy\Pdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Sesion controller.
 *
 */
class SesionController extends AbstractController {

	/**
	 * @Route("/sesion", name="sesion")
	 */
	public function index() {

		$personaUsuario = $this->getUser()->getPersona();
		$cartaOrganica  = $this->getDoctrine()->getRepository( Documento::class )->findOneBySlug( 'carta-organica' );

		try {
			$sesion = $this->getDoctrine()->getRepository( Sesion::class )->findQbUltimaSesion()->getQuery()->getSingleResult();
		} catch ( NoResultException $e ) {

//			TODO ver flash message cuando no hay sesión activa creada
			$this->addFlash( 'warning', 'No existe sesión activa' );

			return $this->redirectToRoute( 'sesion_logout' );

		} catch ( NonUniqueResultException $e ) {
		}

		if ( $this->isGranted( 'ROLE_CONCEJAL' ) ) {


			return $this->render( 'sesion/index.html.twig',
				array(
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
					'sesion'        => $sesion,
				) );
		}

		if ( $this->isGranted( 'ROLE_SECRETARIO' ) ||
		     $this->isGranted( 'ROLE_LEGISLATIVO' ) ||
		     $this->isGranted( 'ROLE_DEFENSOR' ) ||
		     $this->isGranted( 'ROLE_PROSECRETARIO' )
		) {

			return $this->render( 'sesion/autoridades.html.twig',
				array(
					'sesion'        => $sesion,
					'concejal'      => $personaUsuario,
					'cartaOrganica' => $cartaOrganica,
				) );
		}

		return $this->redirectToRoute( 'sesion_logout' );
	}

	public function indexSesiones( PaginatorInterface $paginator, Request $request ) {

		$em = $this->getDoctrine()->getManager();


		$filterType = $this->createForm( SesionFilterType::class,
			null,
			[
				'method' => 'GET'
			] );
		$filterType->handleRequest( $request );
		if ( $filterType->get( 'buscar' )->isClicked() ) {
			$sesiones = $em->getRepository( Sesion::class )->getQbBuscar( $filterType->getData() );
		} else {
			$sesiones = $em->getRepository( Sesion::class )->getQbAll();
		}


		$sesiones = $paginator->paginate(
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

	public function verSesion( Request $request, $id ) {
		$em = $this->getDoctrine()->getManager();

		$sesion = $em->getRepository( Sesion::class )->find( $id );

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
			'INFORMES DEL DEPARTAMENTO EJECUTIVO'  => $bae->getInformesDeDEM(),
			'PROYECTOS DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'              => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'    => $bae->getProyectosDeDefensor(),
		];

		$dictamenes = [
			'EXPEDIENTES CON TRATAMIENTO PREFERENCIAL' => $od->getDictamenesConTratamientoPreferencial(),
			'DICTÁMENES DE DECLARACIÓN'                => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN'               => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'                 => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'                  => $od->getDictamenesDeOrdenanza(),
		];


		return $this->render( 'sesiones/ver.html.twig',
			[
				'sesion'     => $sesion,
				'proyectos'  => $proyectos,
				'dictamenes' => $dictamenes,
			] );
	}

	public function conformarPlanDeLaborIndex( Request $request ) {
		$em       = $this->getDoctrine()->getManager();
		$sesionQb = $em->getRepository( Sesion::class )->findQbUltimaSesion();
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

		return $this->render( 'sesiones/conformar_plan_de_labor_index.html.twig',
			array(
				'sesion' => $sesion
			) );
	}

	public function asignarProyectosABAE( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

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

			return $this->redirectToRoute( 'sesiones_asignar_proyectos_a_bae',
				[ 'sesionId' => $sesionId ] );

		}

		return $this->render( 'sesiones/asignar_proyectos_a_bae.html.twig',
			array(
				'sesion' => $sesion,
				'form'   => $form->createView()
			) );
	}

	public function editarExtractoBAE( Request $request, Expediente $expediente ) {

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
			$log = Log::forEntity( $proyectoBAE );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $proyectoBAE->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $proyectoBAE->{$campo['getter']}() );
				}
			}

			if ( $log->hasCambios() ) {
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

		$logs = $em->getRepository( Log::class )->findBy( [
			'entityClass' => ProyectoBAE::class,
			'entityId'    => $proyectoBAE->getId()
		] );

		return $this->render( 'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'sesion'     => $sesion,
				'logs'       => $logs,
			) );
	}

	public function asignarDictamenesAOD( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

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

			return $this->redirectToRoute( 'sesiones_asignar_dictamenes_a_od', [ 'sesionId' => $sesionId ] );

		}

		return $this->render( 'sesiones/asignar_dictamenes_a_od.html.twig',
			array(
				'sesion' => $sesion,
				'form'   => $form->createView()
			) );
	}

	/**
	 * @param Request $request
	 * @param Expediente $expediente
	 *
	 * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
	 * @throws \Doctrine\ORM\NoResultException
	 * @throws \Doctrine\ORM\NonUniqueResultException
	 */
	public function editarExtractoOD( Request $request, Expediente $expediente ) {

		$em = $this->getDoctrine()->getManager();

		$sesionQb = $em->getRepository( Sesion::class )->findQbUltimaSesion();

		/** @var Sesion $sesion */
		$sesion = $sesionQb->getQuery()->getSingleResult();

		/** @var OrdenDelDia $od */
		$od = $sesion->getOd()->first();

		$dictamenOD = null;
		foreach ( $od->getDictamenes() as $dod ) {
			if ( $dod->getDictamen()->getExpediente()->getId() == $expediente->getId() ) {
				$dictamenOD = $dod;
				break;
			}
		}

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
			$log = Log::forEntity( $dictamenOD );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $dictamenOD->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $dictamenOD->{$campo['getter']}() );
				}
			}

			if ( $log->hasCambios() ) {
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

		$logs = $em->getRepository( Log::class )->findBy( [
			'entityClass' => DictamenOD::class,
			'entityId'    => $dictamenOD->getId()
		] );

		return $this->render( 'expediente/editarExtracto.html.twig',
			array(
				'expediente' => $expediente,
				'edit_form'  => $editForm->createView(),
				'sesion'     => $sesion,
				'logs'       => $logs,
			) );
	}

	public function conformarPlanDeLaborConfirmar( Request $request, $sesionId ) {

		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

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
		$parametroMail           = $em->getRepository( Parametro::class )->findOneBySlug( 'mail-concejales' );
		$parametroMailDefensor   = $em->getRepository( Parametro::class )->findOneBySlug( 'mail-defensor' );
		$parametroMailSecretario = $em->getRepository( Parametro::class )->findOneBySlug( 'mail-secretaria' );

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

	public function imprimirBAE( Pdf $knpSnappyPdf, Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

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
			$header = $this->renderView( 'sesiones/encabezado_plan_de_labor.pdf.twig',
				[
					"sesion"    => $sesion,
					'documento' => $title
				] );
		}

		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$proyectos = [
			'INFORMES DEL DEPARTAMENTO EJECUTIVO'  => $bae->getInformesDeDEM(),
			'PROYECTOS DEL DEPARTAMENTO EJECUTIVO' => $bae->getProyectosDeDEM(),
			'PROYECTOS DE CONCEJALES'              => $bae->getProyectosDeConcejales(),
			'PROYECTOS DEL DEFENSOR DEL PUEBLO'    => $bae->getProyectosDeDefensor(),
		];

		$html = $this->renderView( 'sesiones/boletin_asuntos_entrados.pdf.twig',
			[
				'bae'       => $bae,
				'title'     => $title . ' - ' . $sesion->getTitulo(),
				'proyectos' => $proyectos,
				'sesion'    => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
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

	public function imprimirOD( Pdf $knpSnappyPdf, Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

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
			$header = $this->renderView( 'sesiones/encabezado_plan_de_labor.pdf.twig',
				[
					"sesion"    => $sesion,
					'documento' => $title

				] );
		}

		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$dictamenes = [
			'EXPEDIENTES CON TRATAMIENTO PREFERENCIAL' => $od->getDictamenesConTratamientoPreferencial(),
			'DICTÁMENES DE DECLARACIÓN'                => $od->getDictamenesDeDeclaracion(),
			'DICTÁMENES DE COMUNICACIÓN'               => $od->getDictamenesDeComunicacion(),
			'DICTÁMENES DE RESOLUCIÓN'                 => $od->getDictamenesDeResolucion(),
			'DICTÁMENES DE ORDENANZA'                  => $od->getDictamenesDeOrdenanza(),
		];

		$html = $this->renderView( 'sesiones/orden_del_dia.pdf.twig',
			[
				'od'         => $od,
				'title'      => $title . ' - ' . $sesion->getTitulo(),
				'dictamenes' => $dictamenes,
				"sesion"     => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
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

	public function cargarActa( Request $request, $sesionId ) {

		$em = $this->getDoctrine()->getManager();

		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );
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
			$log = Log::forEntity( $sesion );
			foreach ( $valoresOriginales as $nombre => $campo ) {
				if ( $campo['valor'] != $sesion->{$campo['getter']}() ) {
					$log->agregarCambio( $nombre, $campo['valor'], $sesion->{$campo['getter']}() );
				}
			}

			if ( $log->hasCambios() ) {
				$em->persist( $log );
			}

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El acta se modificó correctamente.'
			);

		}

		return $this->render( 'sesiones/cargar_acta.html.twig',
			[
				'sesion' => $sesion,
				'form'   => $form->createView()
			]
		);
	}

	public function verCambiosActa(
		Request $request,
		Sesion $sesion,
		Log $log
	) {
		$this->denyAccessUnlessGranted( 'ROLE_LEGISLATIVO', null, 'No tiene permiso para acceder a esta opción.' );

		return $this->render( 'sesiones/ver_cambios_acta.html.twig',
			array(
				'sesion' => $sesion,
				'log'    => $log,
			) );
	}

	public function imprimirActa( Pdf $knpSnappyPdf, Request $request, $sesionId ) {
		$em     = $this->getDoctrine()->getManager();
		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );

		$title = 'Acta';

		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$html = $this->renderView( 'sesiones/acta.pdf.twig',
			[
				'title'  => $title . ' - ' . $sesion->getTitulo(),
				"sesion" => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
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

	public function proyectoBaeGiro( Request $request, Expediente $expediente ) {

		if ( ! $this->get( 'security.authorization_checker' )->isGranted( 'ROLE_LEGISLATIVO' ) ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'No tiene permisos para modificar los giros.'
			);

			return $this->redirectToRoute( 'app_homepage' );
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
		foreach ( $proyectoBAE->getGiros() as $giro ) {
			$girosAComisionOriginal->add( $giro );
		}


		$editForm = $this->createForm( ProyectoBAEGiroType::class, $proyectoBAE );

		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {

			foreach ( $girosAComisionOriginal as $giro ) {
				if ( false === $proyectoBAE->getGiros()->contains( $giro ) ) {
					$giro->setProyectoBae( null );
					$em->remove( $giro );
				}
			}

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Giro/s modificado/s correctamente'
			);

			return $this->redirectToRoute( 'proyecto_bae_giro', array( 'expediente' => $expediente->getId() ) );
		}

		return $this->render( 'expediente/proyecto_bae_giro.html.twig',
			array(
				'expediente' => $expediente,
				'sesion'     => $sesion,
				'edit_form'  => $editForm->createView(),
			) );
	}

	public function cargarHomenaje( Request $request, $sesionId ) {

		$em = $this->getDoctrine()->getManager();

		$sesion = $em->getRepository( Sesion::class )->find( $sesionId );
		$form   = $this->createForm( SesionCargarHomenajeType::class, $sesion );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'El homenaje se modificó correctamente.'
			);

		}

		return $this->render( 'sesiones/cargar_homenaje.html.twig',
			[
				'sesion' => $sesion,
				'form'   => $form->createView()
			]
		);
	}

	public function imprimirHomenajes( Pdf $knpSnappyPdf, Request $request, $sesionId ) {
		$em                 = $this->getDoctrine()->getManager();
		$sesion             = $em->getRepository( Sesion::class )->find( $sesionId );
		$periodoLegislativo = $em->getRepository( PeriodoLegislativo::class )->findOneByAnio( $sesion->getFecha()->format( 'Y' ) );
		$title              = 'Homenajes';

		$header = $this->renderView( 'default/membrete.pdf.twig',
			[
				"periodo" => $periodoLegislativo,
			] );
		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$html = $this->renderView( 'sesiones/homenajes.pdf.twig',
			[
				'title'  => $title . ' - ' . $sesion->getTitulo(),
				"sesion" => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				array(
					'page-size'      => 'Legal',
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'margin-left'    => "3cm",
					'margin-right'   => "3cm",
					'header-html'    => $header,
					'header-spacing' => 4,
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

	public function verMociones( Request $request, Sesion $id ) {
		return $this->render( 'sesiones/ver_mociones.html.twig',
			[ 'sesion' => $id ] );
	}

	public function imprimirMociones( Pdf $knpSnappyPdf, Request $request, Sesion $id ) {
		$title              = 'Votación Nominal';
		$sesion             = $id;
		$em                 = $this->getDoctrine()->getManager();
		$periodoLegislativo = $em->getRepository( PeriodoLegislativo::class )->findOneByAnio( $sesion->getFecha()->format( 'Y' ) );


		$header = $this->renderView( 'default/membrete.pdf.twig',
			[
				"periodo" => $periodoLegislativo,
			] );
		$footer = $this->renderView( 'default/pie_pagina.pdf.twig' );

		$html = $this->renderView( 'sesiones/ver_mociones.pdf.twig',
			[
				'title'  => $title . ' - ' . $sesion->getTitulo(),
				"sesion" => $sesion,
			] );

//        return new Response($html);

		return new Response(
			$knpSnappyPdf->getOutputFromHtml( $html,
				[
					'page-size'      => 'Legal',
					'margin-top'     => "5cm",
					'margin-bottom'  => "2cm",
					'margin-left'    => "3cm",
					'margin-right'   => "3cm",
					'header-html'    => $header,
					'header-spacing' => 4,
					'footer-spacing' => 5,
					'footer-html'    => $footer,
				]
			)
			, 200, [
				'Content-Type'        => 'application/pdf',
				'Content-Disposition' => 'inline; filename="' . $title . ' - ' . $sesion->getTitulo() . '.pdf"'
			]
		);
	}

	/**
	 * @Route("/{sesion}/planificar", name="planificar_sesion")
	 */
	public function planificar( Request $request, Sesion $sesion ) {

		$em = $this->getDoctrine()->getManager();

		$form = $this->createForm( PlanificarSesionType::class, $sesion );

		$mocionesOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $sesion->getMociones() as $mocion ) {
			$mocionesOriginales->add( $mocion );
		}

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			foreach ( $mocionesOriginales as $mocion ) {
				if ( false === $sesion->getMociones()->contains( $mocion ) ) {
					$mocion->setSesion( null );
					$em->remove( $mocion );
				}
			}


			// asigno mociones para votar para la sesion planificada
			$nuevasMociones = $sesion->getMociones();

			$mocionPlanificada = $this->getDoctrine()->getRepository( Parametro::class )->findOneBySlug( Mocion::TIPO_PLANIFICADA );
			$estadoMocion      = $this->getDoctrine()->getRepository( Parametro::class )->findOneBySlug( Mocion::ESTADO_PARA_VOTAR );

			foreach ( $nuevasMociones as $nuevaMocion ) {
				if ( ! $nuevaMocion->getId() ) {
					$nuevaMocion->setTipo( $mocionPlanificada );
					$nuevaMocion->setSesion( $sesion );
					$nuevaMocion->setEstado( $estadoMocion );
				}
			}

			$em->flush();
			$this->addFlash(
				'success',
				'Sesión guardada correctamente'
			);

		}

		return $this->render( 'sesiones/planificar.html.twig',
			[
				'sesion' => $sesion,
				'form'   => $form->createView()
			] );
	}

}
