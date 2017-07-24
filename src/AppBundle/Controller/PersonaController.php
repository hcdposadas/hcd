<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Persona;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Persona controller.
 *
 */
class PersonaController extends Controller {
	/**
	 * Lists all persona entities.
	 *
	 */
	public function indexAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();


//		$filterType->handleRequest( $request );
//
//		if ( $request->query->has( $filterType->getName() ) ) {
//			$filterType->submit( $request->query->get( $filterType->getName() ) );
//		}

		$personas = $em->getRepository( 'AppBundle:Persona' )->getQbAll();

		$paginator = $this->get( 'knp_paginator' );
		$personas  = $paginator->paginate(
			$personas,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'persona/index.html.twig',
			array(
				'personas' => $personas,
			) );
	}

	/**
	 * Creates a new persona entity.
	 *
	 */
	public function newAction( Request $request ) {
		$persona = new Persona();
		$form    = $this->createForm( 'AppBundle\Form\PersonalType', $persona );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $persona );
			$em->flush();

			return $this->redirectToRoute( 'persona_show', array( 'id' => $persona->getId() ) );
		}

		return $this->render( 'persona/new.html.twig',
			array(
				'persona' => $persona,
				'form'    => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a persona entity.
	 *
	 */
	public function showAction( Persona $persona ) {
		$deleteForm = $this->createDeleteForm( $persona );

		return $this->render( 'persona/show.html.twig',
			array(
				'persona'     => $persona,
				'delete_form' => $deleteForm->createView(),
			) );
	}

	/**
	 * Displays a form to edit an existing persona entity.
	 *
	 */
	public function editAction( Request $request, Persona $persona ) {
		$deleteForm = $this->createDeleteForm( $persona );
		$editForm   = $this->createForm( 'AppBundle\Form\PersonalType', $persona );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'persona_edit', array( 'id' => $persona->getId() ) );
		}

		return $this->render( 'persona/edit.html.twig',
			array(
				'persona'     => $persona,
				'edit_form'   => $editForm->createView(),
				'delete_form' => $deleteForm->createView(),
			) );
	}

	/**
	 * Deletes a persona entity.
	 *
	 */
	public function deleteAction( Request $request, Persona $persona ) {
		$form = $this->createDeleteForm( $persona );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $persona );
			$em->flush();
		}

		return $this->redirectToRoute( 'persona_index' );
	}

	/**
	 * Creates a form to delete a persona entity.
	 *
	 * @param Persona $persona The persona entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( Persona $persona ) {
		return $this->createFormBuilder()
		            ->setAction( $this->generateUrl( 'persona_delete', array( 'id' => $persona->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}
}
