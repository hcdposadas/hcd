<?php

namespace App\Controller;

use App\Form\DecretoType;
use App\Entity\Decreto;

use App\Form\Filter\DecretoFilterType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Decreto controller.
 *
 */
class DecretoController extends AbstractController {
	/**
	 * Lists all decreto entities.
	 *
	 */
	public function index(PaginatorInterface $paginator, Request $request ) {
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( DecretoFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {

			$decretos = $em->getRepository( Decreto::class )->getQbAll( $filterType->getData() );
		} else {

			$decretos = $em->getRepository( Decreto::class )->getQbAll();
		}


		$decretos  = $paginator->paginate(
			$decretos,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'decreto/index.html.twig',
			array(
				'decretos'    => $decretos,
				'filter_type' => $filterType->createView()
			) );
	}

	/**
	 * Creates a new decreto entity.
	 *
	 */
	public function new( Request $request ) {
		$decreto = new Decreto();
		$form    = $this->createForm( DecretoType::class, $decreto );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $decreto );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Decreto creado correctamente'
			);

			return $this->redirectToRoute( 'decreto_show', array( 'id' => $decreto->getId() ) );
		}

		return $this->render( 'decreto/new.html.twig',
			array(
				'decreto' => $decreto,
				'form'    => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a decreto entity.
	 *
	 */
	public function show( Decreto $decreto ) {
//        $deleteForm = $this->createDeleteForm($decreto);

		return $this->render( 'decreto/show.html.twig',
			array(
				'decreto' => $decreto,
//            'delete_form' => $deleteForm->createView(),
			) );
	}

	/**
	 * Displays a form to edit an existing decreto entity.
	 *
	 */
	public function edit( Request $request, Decreto $decreto ) {

		$editForm = $this->createForm( DecretoType::class, $decreto );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Decreto actualizado correctamente'
			);

			return $this->redirectToRoute( 'decreto_edit', array( 'id' => $decreto->getId() ) );
		}

		return $this->render( 'decreto/edit.html.twig',
			array(
				'decreto'   => $decreto,
				'edit_form' => $editForm->createView(),

			) );
	}

	/**
	 * Deletes a decreto entity.
	 *
	 */
	public function delete( Request $request, Decreto $decreto ) {
		$form = $this->createDeleteForm( $decreto );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $decreto );
			$em->flush();
		}

		return $this->redirectToRoute( 'decreto_index' );
	}

	/**
	 * Creates a form to delete a decreto entity.
	 *
	 * @param Decreto $decreto The decreto entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( Decreto $decreto ) {
		return $this->createFormBuilder()
		            ->set( $this->generateUrl( 'decreto_delete', array( 'id' => $decreto->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	public function asignarNumero( Request $request, Decreto $decreto ) {

		$editForm = $this->createForm( DecretoType::class, $decreto );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Decreto actualizado correctamente'
			);

			return $this->redirectToRoute( 'decreto_asignar_numero', array( 'id' => $decreto->getId() ) );
		}

		return $this->render( 'decreto/asignar_numero.html.twig',
			array(
				'decreto'   => $decreto,
				'edit_form' => $editForm->createView(),

			) );
	}
}
