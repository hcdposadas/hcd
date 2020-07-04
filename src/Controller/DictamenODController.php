<?php

namespace App\Controller;

use App\Entity\DictamenOD;
use App\Entity\Sesion;
use App\Form\DictamenODIncorporarType;
use App\Form\DictamenODType;
use App\Entity\Dictamen;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dictamenod")
 */

class DictamenODController extends AbstractController {

	/**
	 * @Route("/", name="texto_definitivo_index")
	 */
	public function index() {
		$em = $this->getDoctrine()->getManager();

		$dictamenODs = $em->getRepository( DictamenOD::class )->findAll();

		return $this->render( 'dictamenod/index.html.twig',
			array(
				'dictamenODs' => $dictamenODs,
			) );
	}

	/**
	 * @Route("/new", name="dictamenod_new")
	 */
	public function new( Request $request ) {
		$dictamenOD = new Dictamenod();
		$form       = $this->createForm( DictamenODType::class, $dictamenOD );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $dictamenOD );
			$em->flush();

			return $this->redirectToRoute( 'dictamenod_show', array( 'id' => $dictamenOD->getId() ) );
		}

		return $this->render( 'dictamenod/new.html.twig',
			array(
				'dictamenOD' => $dictamenOD,
				'form'       => $form->createView(),
			) );
	}

	/**
	 * @Route("/{id}/show", name="dictamenod_show")
	 */
	public function show( DictamenOD $dictamenOD ) {
		$deleteForm = $this->createDeleteForm( $dictamenOD );

		return $this->render( 'dictamenod/show.html.twig',
			array(
				'dictamenOD'  => $dictamenOD,
				'delete_form' => $deleteForm->createView(),
			) );
	}

	/**
	 * @Route("/{id}/edit", name="dictamenod_edit")
	 */
	public function edit( Request $request, DictamenOD $dictamenOD ) {
		$editForm = $this->createForm( DictamenODIncorporarType::class, $dictamenOD );
		$editForm->handleRequest( $request );

		if ( $editForm->isSubmitted() && $editForm->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'dictamenod_edit', array( 'id' => $dictamenOD->getId() ) );
		}

		return $this->render( 'dictamenod/edit.html.twig',
			array(
				'dictamenOD' => $dictamenOD,
				'edit_form'  => $editForm->createView(),
			) );
	}

	/**
	 * @Route("/{id}/delete", name="dictamenod_delete")
	 */
	public function delete( Request $request, DictamenOD $dictamenOD ) {
		$form = $this->createDeleteForm( $dictamenOD );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->remove( $dictamenOD );
			$em->flush();
		}

		return $this->redirectToRoute( 'dictamenod_index' );
	}

	/**
	 * Creates a form to delete a dictamenOD entity.
	 *
	 * @param DictamenOD $dictamenOD The dictamenOD entity
	 *
	 * @return \Symfony\Component\Form\Form The form
	 */
	private function createDeleteForm( DictamenOD $dictamenOD ) {
		return $this->createFormBuilder()
		            ->set( $this->generateUrl( 'dictamenod_delete', array( 'id' => $dictamenOD->getId() ) ) )
		            ->setMethod( 'DELETE' )
		            ->getForm();
	}

	/**
	 * @Route("/{dictamen}/incorporar-a-sesion", name="dictamenod_incorporar_a_sesion")
	 */
	public function incorporarASesion( Request $request, Dictamen $dictamen ) {
		$em         = $this->getDoctrine()->getManager();
		$dictamenOD = new DictamenOD();
		$dictamenOD->setDictamen( $dictamen );

		$dictamenEnSesiones = $em->getRepository( DictamenOD::class )->findBy( [ 'dictamen' => $dictamen ] );

		$form = $this->createForm( DictamenODIncorporarType::class, $dictamenOD );
		$form->remove('dictamen');

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$dictamenOD->setIncorporadoEnSesion( true );

			$em->persist( $dictamenOD );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen ingresado correctamente'
			);

			return $this->redirectToRoute( 'dictamenod_incorporar_a_sesion',
				[ 'dictamen' => $dictamen->getId() ] );
		}

		return $this->render( 'dictamenod/incorporar_a_sesion.html.twig',
			[
				'form'               => $form->createView(),
				'dictamen'           => $dictamen,
				'dictamenEnSesiones' => $dictamenEnSesiones
			] );
	}

	/**
	 * @Route("/{dictamen}/incorporar-en-sesion", name="dictamenod_incorporar_en_sesion")
	 */
	public function incorporarEnSesion( Request $request, Dictamen $dictamen ) {

		$em         = $this->getDoctrine()->getManager();
		$dictamenOD = new DictamenOD();
		$dictamenOD->setDictamen( $dictamen );


		$sesionQb = $em->getRepository( Sesion::class )->findQbUltimaSesion();

		$sesion = null;


		if ( ! $sesionQb->getQuery()->getResult() ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'No hay una Sesión Activa Creada'
			);
		} else {
			$sesion = $sesionQb->getQuery()->getSingleResult();
		}

		if ( $sesion ) {

			if ( $sesion->getBae()->last() ) {
				$dictamenOD->setOrdenDelDia( $sesion->getOd()->last() );
			} else {
				$this->get( 'session' )->getFlashBag()->add(
					'warning',
					'El Plan de labor aun no está conformado'
				);

				return $this->redirectToRoute( 'incorporar_dictamenes_a_sesion_index' );
			}
		}

//		$dictamenOD->setExtracto( $dictamen->getExtracto() );


		$form = $this->createForm( DictamenODIncorporarType::class, $dictamenOD );

		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$dictamenOD->setIncorporadoEnSesion( true );

			$em->persist( $dictamenOD );
			$em->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'Dictamen ingresado correctamente'
			);

			return $this->redirectToRoute( 'incorporar_dictamenes_a_sesion_index' );
		}

		return $this->render( 'dictamenod/incorporar_en_sesion.html.twig',
			[
				'form'                 => $form->createView(),
				'dictamen'           => $dictamen,
			] );
	}
}
