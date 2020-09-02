<?php

namespace App\Controller;

use App\Entity\Legajo;
use App\Entity\PersonalAsistencia;
use App\Form\PersonalAsistenciaType;
use App\Repository\PersonalAsistenciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personal/asistencia")
 */
class PersonalAsistenciaController extends AbstractController {
	/**
	 * @Route("/", name="personal_asistencia_index", methods={"GET"})
	 */
	public function index( PersonalAsistenciaRepository $personalAsistenciaRepository ): Response {
		return $this->render( 'personal_asistencia/index.html.twig',
			[
				'personal_asistencias' => $personalAsistenciaRepository->findAll(),
			] );
	}

	/**
	 * @Route("/{legajo}/asistencias", name="personal_asistencias", methods={"GET"})
	 */
	public function asistencias(
		PersonalAsistenciaRepository $personalAsistenciaRepository,
		Legajo $legajo
	): Response {
		if ( ! $legajo ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'La Persona no tiene legajo'
			);

			return $this->redirectToRoute( 'persona_index' );
		}

		$asitencias = $personalAsistenciaRepository->findBy( [ 'legajo' => $legajo->getId() ] );

		return $this->render( 'personal_asistencia/asistencias_index.html.twig',
			[
				'personal_asistencias' => $asitencias,
				'persona'              => $legajo->getPersona()
			] );
	}

	/**
	 * @Route("/new", name="personal_asistencia_new", methods={"GET","POST"})
	 */
	public function new( Request $request ): Response {
		$personalAsistencium = new PersonalAsistencia();
		$form                = $this->createForm( PersonalAsistenciaType::class, $personalAsistencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalAsistencium );
			$entityManager->flush();

			return $this->redirectToRoute( 'personal_asistencia_index' );
		}

		return $this->render( 'personal_asistencia/new.html.twig',
			[
				'personal_asistencium' => $personalAsistencium,
				'form'                 => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_asistencia_show", methods={"GET"})
	 */
	public function show( PersonalAsistencia $personalAsistencium ): Response {
		return $this->render( 'personal_asistencia/show.html.twig',
			[
				'personal_asistencium' => $personalAsistencium,
			] );
	}

	/**
	 * @Route("/{id}/edit", name="personal_asistencia_edit", methods={"GET","POST"})
	 */
	public function edit( Request $request, PersonalAsistencia $personalAsistencium ): Response {
		$form = $this->createForm( PersonalAsistenciaType::class, $personalAsistencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'personal_asistencia_index' );
		}

		return $this->render( 'personal_asistencia/edit.html.twig',
			[
				'personal_asistencium' => $personalAsistencium,
				'form'                 => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_asistencia_delete", methods={"DELETE"})
	 */
	public function delete( Request $request, PersonalAsistencia $personalAsistencium ): Response {
		if ( $this->isCsrfTokenValid( 'delete' . $personalAsistencium->getId(), $request->request->get( '_token' ) ) ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $personalAsistencium );
			$entityManager->flush();
		}

		return $this->redirectToRoute( 'personal_asistencia_index' );
	}

	/**
	 * @Route("/{legajo}/nueva-asistencia", name="personal_asistencia_nuevo", methods={"GET","POST"})
	 */
	public function nuevaAsistencia( Request $request, Legajo $legajo ): Response {
		$personalAsistencium = new PersonalAsistencia();
		$personalAsistencium->setLegajo( $legajo );
		$form = $this->createForm( PersonalAsistenciaType::class, $personalAsistencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalAsistencium );
			$entityManager->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'La asisencia se registró correctamente'
			);

			return $this->redirectToRoute( 'personal_asistencias', [ 'legajo' => $legajo->getId() ] );
		}

		return $this->render( 'personal_asistencia/nueva.html.twig',
			[
				'personal_asistencium' => $personalAsistencium,
				'form'                 => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/editar", name="personal_asistencia_editar", methods={"GET","POST"})
	 */
	public function editarAsistencia( Request $request, PersonalAsistencia $personalAsistencium ): Response {
		$form = $this->createForm( PersonalAsistenciaType::class, $personalAsistencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'La asisencia se actualizó correctamente'
			);

			return $this->redirectToRoute( 'personal_asistencias', [ 'legajo' => $personalAsistencium->getLegajo()->getId() ] );
		}

		return $this->render( 'personal_asistencia/editar.html.twig',
			[
				'personal_asistencium' => $personalAsistencium,
				'form'                 => $form->createView(),
			] );
	}
}
