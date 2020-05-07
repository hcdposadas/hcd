<?php

namespace MesaEntradaBundle\Controller;

use MesaEntradaBundle\Entity\Dictamen;
use MesaEntradaBundle\Entity\TextoDefinitivo;
use MesaEntradaBundle\Form\TextoDefinitivoType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Textodefinitivo controller.
 *
 */
class TextoDefinitivoController extends Controller {
	/**
	 * Lists all textoDefinitivo entities.
	 *
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getManager();

		$textoDefinitivos = $em->getRepository( 'MesaEntradaBundle:TextoDefinitivo' )->findAll();

		return $this->render( 'textodefinitivo/index.html.twig',
			array(
				'textoDefinitivos' => $textoDefinitivos,
			) );
	}

	/**
	 * Creates a new textoDefinitivo entity.
	 *
	 */
	public function newAction( Request $request ) {
		$textoDefinitivo = new Textodefinitivo();
		$form            = $this->createForm( 'MesaEntradaBundle\Form\TextoDefinitivoType', $textoDefinitivo );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $textoDefinitivo );
			$em->flush();

			return $this->redirectToRoute( 'texto_definitivo_show', array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/new.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'form'            => $form->createView(),
			) );
	}

	/**
	 * Finds and displays a textoDefinitivo entity.
	 *
	 */
	public function showAction( TextoDefinitivo $textoDefinitivo ) {
		$deleteForm = $this->createDeleteForm( $textoDefinitivo );

		return $this->render( 'textodefinitivo/show.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'delete_form'     => $deleteForm->createView(),
			) );
	}

	/**
	 * Displays a form to edit an existing textoDefinitivo entity.
	 *
	 */
	public function editAction( Request $request, TextoDefinitivo $textoDefinitivo ) {
		$deleteForm = $this->createDeleteForm( $textoDefinitivo );
		$editForm   = $this->createForm( 'MesaEntradaBundle\Form\TextoDefinitivoType', $textoDefinitivo );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'texto_definitivo_edit', array( 'id' => $textoDefinitivo->getId() ) );
		}

		return $this->render( 'textodefinitivo/edit.html.twig',
			array(
				'textoDefinitivo' => $textoDefinitivo,
				'edit_form'       => $editForm->createView(),
				'delete_form'     => $deleteForm->createView(),
			) );
	}

	/**
	 * Deletes a textoDefinitivo entity.
	 *
	 */
	public function deleteAction( Request $request, TextoDefinitivo $textoDefinitivo ) {
		$form = $this->createDeleteForm( $textoDefinitivo );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $textoDefinitivo );
			$em->flush();
		}

		return $this->redirectToRoute( 'texto_definitivo_index' );
	}

	/**
	 * Creates a form to delete a textoDefinitivo entity.
	 *
	 * @param TextoDefinitivo $textoDefinitivo The textoDefinitivo entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( TextoDefinitivo $textoDefinitivo ) {
		return $this->createFormBuilder()
		            ->setAction( $this->generateUrl( 'texto_definitivo_delete',
			            array( 'id' => $textoDefinitivo->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	/**
	 * Creates a new textoDefinitivo entity.
	 *
	 */
	public function asignarAction( Request $request, Dictamen $dictamen ) {
		$textoDefinitivo = new Textodefinitivo();
		$textoDefinitivo->setDictamen( $dictamen );
		$form = $this->createForm( TextoDefinitivoType::class, $textoDefinitivo );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $textoDefinitivo );
			$em->flush();

			return $this->redirectToRoute( 'texto_definitivo_show', [ 'id' => $textoDefinitivo->getId() ] );
		}

		return $this->render( 'textodefinitivo/asignar.html.twig',
			[
				'textoDefinitivo' => $textoDefinitivo,
				'form'            => $form->createView(),
			] );
	}
}
