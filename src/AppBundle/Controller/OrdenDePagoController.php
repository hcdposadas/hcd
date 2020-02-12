<?php

namespace AppBundle\Controller;

use AppBundle\Entity\OrdenDePago;
use AppBundle\Form\Filter\OrdenDePagoFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ordendepago controller.
 *
 */
class OrdenDePagoController extends Controller {
	/**
	 * Lists all ordenDePago entities.
	 *
	 */
	public function indexAction( Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( OrdenDePagoFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$ordenesDePag = $em->getRepository( OrdenDePago::class )->getQbAll( $filterType->getData() );
		} else {

			$ordenesDePag = $em->getRepository( OrdenDePago::class )->getQbAll();
		}


		$paginator = $this->get( 'knp_paginator' );
		$ordenesDePag  = $paginator->paginate(
			$ordenesDePag,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'ordendepago/index.html.twig',
			array(
				'ordenDePagos'    => $ordenesDePag,
				'filter_type' => $filterType->createView()
			) );
	}

	/**
	 * Creates a new ordenDePago entity.
	 *
	 */
	public function newAction( Request $request ) {
		$ordenDePago = new Ordendepago();
		$form        = $this->createForm( 'AppBundle\Form\OrdenDePagoType', $ordenDePago );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $ordenDePago );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Orden de pago creada correctamente'
			);

			return $this->redirectToRoute( 'orden_de_pago_show', array( 'id' => $ordenDePago->getId() ) );
		}

		return $this->render( 'ordendepago/new.html.twig',
			array(
				'ordenDePago' => $ordenDePago,
				'form'        => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a ordenDePago entity.
	 *
	 */
	public function showAction( OrdenDePago $ordenDePago ) {

		return $this->render( 'ordendepago/show.html.twig',
			array(
				'ordenDePago' => $ordenDePago,
			) );
	}

	/**
	 * Displays a form to edit an existing ordenDePago entity.
	 *
	 */
	public function editAction( Request $request, OrdenDePago $ordenDePago ) {

		$editForm = $this->createForm( 'AppBundle\Form\OrdenDePagoType', $ordenDePago );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Orden de pago actualizada correctamente'
			);

			return $this->redirectToRoute( 'orden_de_pago_edit', array( 'id' => $ordenDePago->getId() ) );
		}

		return $this->render( 'ordendepago/edit.html.twig',
			array(
				'ordenDePago' => $ordenDePago,
				'edit_form'   => $editForm->createView(),
			) );
	}

	/**
	 * Deletes a ordenDePago entity.
	 *
	 */
	public function deleteAction( Request $request, OrdenDePago $ordenDePago ) {
		$form = $this->createDeleteForm( $ordenDePago );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $ordenDePago );
			$em->flush();
		}

		return $this->redirectToRoute( 'orden_de_pago_index' );
	}

	/**
	 * Creates a form to delete a ordenDePago entity.
	 *
	 * @param OrdenDePago $ordenDePago The ordenDePago entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( OrdenDePago $ordenDePago ) {
		return $this->createFormBuilder()
		            ->setAction( $this->generateUrl( 'orden_de_pago_delete', array( 'id' => $ordenDePago->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}
}
