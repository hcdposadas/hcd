<?php

namespace App\Controller;

use App\Entity\OrdenDePago;
use App\Form\Filter\OrdenDePagoFilterType;
use App\Form\OrdenDePagoType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Ordendepago controller.
 *
 */
class OrdenDePagoController extends AbstractController {
	/**
	 * Lists all ordenDePago entities.
	 *
	 */
	public function index(PaginatorInterface $paginator, Request $request ) {
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
	public function new( Request $request ) {
		$ordenDePago = new Ordendepago();
		$form        = $this->createForm( OrdenDePagoType::class, $ordenDePago );
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
	public function show( OrdenDePago $ordenDePago ) {

		return $this->render( 'ordendepago/show.html.twig',
			array(
				'ordenDePago' => $ordenDePago,
			) );
	}

	/**
	 * Displays a form to edit an existing ordenDePago entity.
	 *
	 */
	public function edit( Request $request, OrdenDePago $ordenDePago ) {

		$editForm = $this->createForm( OrdenDePagoType::class, $ordenDePago );
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
	public function delete( Request $request, OrdenDePago $ordenDePago ) {
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
		            ->set( $this->generateUrl( 'orden_de_pago_delete', array( 'id' => $ordenDePago->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}
}
