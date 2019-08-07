<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProyectoBAE;
use AppBundle\Entity\Sesion;
use AppBundle\Form\ProyectoBAEIncorporarType;
use MesaEntradaBundle\Entity\Expediente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Proyectobae controller.
 *
 */
class ProyectoBAEController extends Controller {
	/**
	 * Lists all proyectoBAE entities.
	 *
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$proyectoBAEs = $em->getRepository( 'AppBundle:ProyectoBAE' )->findAll();

		return $this->render( 'proyectobae/index.html.twig',
			array(
				'proyectoBAEs' => $proyectoBAEs,
			) );
	}

	/**
	 * Creates a new proyectoBAE entity.
	 *
	 */
	public function newAction( Request $request ) {
		$proyectoBAE = new Proyectobae();
		$form        = $this->createForm( 'AppBundle\Form\ProyectoBAEType', $proyectoBAE );
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
	public function showAction( ProyectoBAE $proyectoBAE ) {
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
	public function editAction( Request $request, ProyectoBAE $proyectoBAE ) {

		$editForm = $this->createForm( ProyectoBAEIncorporarType::class, $proyectoBAE );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

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
			) );
	}

	/**
	 * Deletes a proyectoBAE entity.
	 *
	 */
	public function deleteAction( Request $request, ProyectoBAE $proyectoBAE ) {
		$form = $this->createDeleteForm( $proyectoBAE );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $proyectoBAE );
			$em->flush();
		}

		return $this->redirectToRoute( 'proyectobae_index' );
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
		            ->setAction( $this->generateUrl( 'proyectobae_delete', array( 'id' => $proyectoBAE->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	public function incorporarASesionAction( Request $request, Expediente $expediente ) {

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

	public function incorporarEnSesionAction( Request $request, Expediente $expediente ) {

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
