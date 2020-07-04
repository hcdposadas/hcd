<?php

namespace App\Controller;

use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use App\Form\ProyectoBAEIncorporarType;
use App\Form\ProyectoBAEType;
use Doctrine\Common\Collections\ArrayCollection;
use App\Entity\Expediente;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Proyectobae controller.
 *
 */
class ProyectoBAEController extends AbstractController {
	/**
	 * Lists all proyectoBAE entities.
	 *
	 */
	public function index() {
		$em = $this->getDoctrine()->getManager();

		$proyectoBAEs = $em->getRepository( ProyectoBAE::class )->findAll();

		return $this->render( 'proyectobae/index.html.twig',
			array(
				'proyectoBAEs' => $proyectoBAEs,
			) );
	}

	/**
	 * Creates a new proyectoBAE entity.
	 *
	 */
	public function new( Request $request ) {
		$proyectoBAE = new Proyectobae();
		$form        = $this->createForm( ProyectoBAEType::class, $proyectoBAE );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $proyectoBAE );
			$em->flush();

			return $this->redirectToRoute( 'proyectobae_show', array( 'id' => $proyectoBAE->getId() ) );
		}

		return $this->render( 'proyectobae/new.html.twig',
			array(
				'proyectoBAE' => $proyectoBAE,
				'form'        => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a proyectoBAE entity.
	 *
	 */
	public function show( ProyectoBAE $proyectoBAE ) {
		$deleteForm = $this->createDeleteForm( $proyectoBAE );

		return $this->render( 'proyectobae/show.html.twig',
			array(
				'proyectoBAE' => $proyectoBAE,
				'delete_form' => $deleteForm->createView(),
			) );
	}

	/**
	 * Displays a form to edit an existing proyectoBAE entity.
	 *
	 */
	public function edit( Request $request, ProyectoBAE $proyectoBAE ) {

		$editForm = $this->createForm( ProyectoBAEIncorporarType::class, $proyectoBAE );

		$em     = $this->getDoctrine()->getManager();

		$girosOriginales = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $proyectoBAE->getGiros() as $giros ) {
			$girosOriginales->add( $giros );
		}

		$deleteForm = $this->createDeleteForm( $proyectoBAE );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {


			foreach ( $girosOriginales as $giros ) {
				if ( false === $proyectoBAE->getGiros()->contains( $giros ) ) {
					$giros->setProyectoBae( null );
					$em->remove( $giros );
				}
			}

			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'BAE modificado Correctamente'
			);

			return $this->redirectToRoute( 'proyectobae_incorporar_a_sesion',
				[ 'expediente' => $proyectoBAE->getExpediente()->getId() ] );
		}

		return $this->render( 'proyectobae/edit.html.twig',
			array(
				'proyectoBAE' => $proyectoBAE,
				'edit_form'   => $editForm->createView(),
				'delete_form'   => $deleteForm->createView(),
			) );
	}

	/**
	 * Deletes a proyectoBAE entity.
	 *
	 */
	public function delete( Request $request, ProyectoBAE $proyectoBAE ) {
		$form = $this->createDeleteForm( $proyectoBAE );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $proyectoBAE );
			$em->flush();
			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'ProyectoBAE eliminado correctamente'
			);
		}

		return $this->redirectToRoute( 'proyectobae_incorporar_a_sesion',
			[ 'expediente' => $proyectoBAE->getExpediente()->getId() ] );
	}

	/**
	 * Creates a form to delete a proyectoBAE entity.
	 *
	 * @param ProyectoBAE $proyectoBAE The proyectoBAE entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( ProyectoBAE $proyectoBAE ) {
		return $this->createFormBuilder()
		            ->set( $this->generateUrl( 'proyectobae_delete', array( 'id' => $proyectoBAE->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	public function incorporarASesion( Request $request, Expediente $expediente ) {

		$em          = $this->getDoctrine()->getManager();
		$proyectoBAE = new ProyectoBAE();
		$proyectoBAE->setExpediente( $expediente );

		$expedienteEnSesiones = $em->getRepository( ProyectoBAE::class )->findBy( [ 'expediente' => $expediente ] );

		$form = $this->createForm( ProyectoBAEIncorporarType::class, $proyectoBAE );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$proyectoBAE->setIncorporadoEnSesion( true );

			$em->persist( $proyectoBAE );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente ingresado correctamente'
			);

			return $this->redirectToRoute( 'proyectobae_incorporar_a_sesion',
				[ 'expediente' => $expediente->getId() ] );
		}

		return $this->render( 'proyectobae/incorporar_a_sesion.html.twig',
			[
				'form'                 => $form->createView(),
				'expediente'           => $expediente,
				'expedienteEnSesiones' => $expedienteEnSesiones
			] );
	}

	public function incorporarEnSesion( Request $request, Expediente $expediente ) {

		$em          = $this->getDoctrine()->getManager();
		$proyectoBAE = new ProyectoBAE();
		$proyectoBAE->setExpediente( $expediente );

		$expedienteEnSesiones = $em->getRepository( ProyectoBAE::class )->findBy( [ 'expediente' => $expediente ] );

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

		if ($sesion){
			$proyectoBAE->setBoletinAsuntoEntrado($sesion->getBae()->last());
		}

		$proyectoBAE->setExtracto($expediente->getExtracto());


		$form = $this->createForm( ProyectoBAEIncorporarType::class, $proyectoBAE );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$proyectoBAE->setIncorporadoEnSesion( true );

			$em->persist( $proyectoBAE );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Expediente ingresado correctamente'
			);

			return $this->redirectToRoute( 'incorporar_expedientes_a_sesion_index' );
		}

		return $this->render( 'proyectobae/incorporar_en_sesion.html.twig',
			[
				'form'                 => $form->createView(),
				'expediente'           => $expediente,
				'expedienteEnSesiones' => $expedienteEnSesiones
			] );
	}
}
