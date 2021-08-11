<?php

namespace App\Controller;

use App\Entity\AreaAdministrativa;
use App\Entity\Expediente;
use App\Entity\GiroAdministrativo;
use App\Entity\ProyectoFirmado;
use App\Form\ProyectoFirmadoType;
use App\Repository\ProyectoFirmadoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/proyecto/firmado")
 */
class ProyectoFirmadoController extends AbstractController {
	/**
	 * @Route("/", name="proyecto_firmado_index", methods={"GET"})
	 */
	public function index( ProyectoFirmadoRepository $proyectoFirmadoRepository ): Response {
		return $this->render( 'proyecto_firmado/index.html.twig',
			[
				'proyecto_firmados' => $proyectoFirmadoRepository->findAll(),
			] );
	}

	/**
	 * @Route("/new", name="proyecto_firmado_new", methods={"GET","POST"})
	 */
	public function new( Request $request ): Response {
		$proyectoFirmado = new ProyectoFirmado();
		$form            = $this->createForm( ProyectoFirmadoType::class, $proyectoFirmado );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $proyectoFirmado );
			$entityManager->flush();

			return $this->redirectToRoute( 'proyecto_firmado_index' );
		}

		return $this->render( 'proyecto_firmado/new.html.twig',
			[
				'proyecto_firmado' => $proyectoFirmado,
				'form'             => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="proyecto_firmado_show", methods={"GET"})
	 */
	public function show( ProyectoFirmado $proyectoFirmado ): Response {
		return $this->render( 'proyecto_firmado/show.html.twig',
			[
				'proyecto_firmado' => $proyectoFirmado,
			] );
	}

	/**
	 * @Route("/{id}/edit", name="proyecto_firmado_edit", methods={"GET","POST"})
	 */
	public function edit( Request $request, ProyectoFirmado $proyectoFirmado ): Response {
		$form = $this->createForm( ProyectoFirmadoType::class, $proyectoFirmado );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'proyecto_firmado_index' );
		}

		return $this->render( 'proyecto_firmado/edit.html.twig',
			[
				'proyecto_firmado' => $proyectoFirmado,
				'form'             => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="proyecto_firmado_delete", methods={"DELETE"})
	 */
	public function delete( Request $request, ProyectoFirmado $proyectoFirmado ): Response {
		if ( $this->isCsrfTokenValid( 'delete' . $proyectoFirmado->getId(), $request->request->get( '_token' ) ) ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $proyectoFirmado );
			$entityManager->flush();
		}

		return $this->redirectToRoute( 'proyecto_firmado_index' );
	}

	/**
	 * @Route("/proyecto-firmar/{expediente}", name="proyecto_firmar")
	 */
	public function firmarProyecto( Request $request, Expediente $expediente ) {

		$em = $this->getDoctrine()->getManager();
		$proyectoFirmado = new ProyectoFirmado();

		$form = $this->createForm( ProyectoFirmadoType::class, $proyectoFirmado );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$proyectoFirmado->setExpediente( $expediente );
			$expediente->addFirmasProyecto( $proyectoFirmado );

			$em->persist( $proyectoFirmado );

			if ( $expediente->getIniciadores()->count() == $expediente->getFirmasProyectos()->count() ) {
				$expediente->setFirmado( true );
				// Departamento de Mesa de Entradas y Salidas

				$giroAdministrativo = new GiroAdministrativo();
				$areaDestino        = $em->getRepository( AreaAdministrativa::class )->findOneBy( [
					'nombre' => 'Departamento de Mesa de Entradas y Salidas'
				] );
				$giroAdministrativo->setAreaDestino( $areaDestino );
				$giroAdministrativo->setExpediente( $expediente );
				$giroAdministrativo->setFechaGiro( new \DateTime( 'now' ) );


				$expediente->addGiroAdministrativo( $giroAdministrativo );
				$em->persist( $giroAdministrativo );
			}

			$em->flush();

			return $this->redirectToRoute( 'proyecto_show', [ 'id' => $expediente->getId() ] );
		}

		$firmado = $this->getDoctrine()->getRepository( ProyectoFirmado::class )->findOneBy( [
			'expediente' => $expediente,
			'creadoPor'  => $this->getUser()
		] );

		return $this->render( 'proyecto_firmado/proyecto_firmar.html.twig',
			[
				'expediente' => $expediente,
				'form'       => $form->createView(),
				'firmado'    => $firmado
			] );

	}
}
