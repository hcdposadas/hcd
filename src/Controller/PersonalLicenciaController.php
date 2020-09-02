<?php

namespace App\Controller;

use App\Entity\Legajo;
use App\Entity\PersonalLicencia;
use App\Form\PersonalLicenciaType;
use App\Repository\PersonalLicenciaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personal/licencia")
 */
class PersonalLicenciaController extends AbstractController {
	/**
	 * @Route("/", name="personal_licencia_index", methods={"GET"})
	 */
	public function index( PersonalLicenciaRepository $personalLicenciaRepository ): Response {
		return $this->render( 'personal_licencia/index.html.twig',
			[
				'personal_licencias' => $personalLicenciaRepository->findAll(),
			] );
	}

	/**
	 * @Route("/{legajo}/licencias", name="personal_licencias", methods={"GET"})
	 */
	public function licencias( PersonalLicenciaRepository $personalLicenciaRepository, Legajo $legajo ): Response {

		if ( ! $legajo ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'La Persona no tiene legajo'
			);

			return $this->redirectToRoute( 'persona_index' );
		}

		$licencias = $personalLicenciaRepository->findBy( [ 'legajo' => $legajo ] );

		return $this->render( 'personal_licencia/licencias_index.html.twig',
			[
				'personal_licencias' => $licencias,
				'persona'            => $legajo->getPersona()
			] );
	}

	/**
	 * @Route("/new", name="personal_licencia_new", methods={"GET","POST"})
	 */
	public function new( Request $request ): Response {
		$personalLicencium = new PersonalLicencia();
		$form              = $this->createForm( PersonalLicenciaType::class, $personalLicencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalLicencium );
			$entityManager->flush();

			return $this->redirectToRoute( 'personal_licencia_index' );
		}

		return $this->render( 'personal_licencia/new.html.twig',
			[
				'personal_licencium' => $personalLicencium,
				'form'               => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_licencia_show", methods={"GET"})
	 */
	public function show( PersonalLicencia $personalLicencium ): Response {
		return $this->render( 'personal_licencia/show.html.twig',
			[
				'personal_licencium' => $personalLicencium,
			] );
	}

	/**
	 * @Route("/{id}/edit", name="personal_licencia_edit", methods={"GET","POST"})
	 */
	public function edit( Request $request, PersonalLicencia $personalLicencium ): Response {
		$form = $this->createForm( PersonalLicenciaType::class, $personalLicencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'personal_licencia_index' );
		}

		return $this->render( 'personal_licencia/edit.html.twig',
			[
				'personal_licencium' => $personalLicencium,
				'form'               => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_licencia_delete", methods={"DELETE"})
	 */
	public function delete( Request $request, PersonalLicencia $personalLicencium ): Response {
		if ( $this->isCsrfTokenValid( 'delete' . $personalLicencium->getId(), $request->request->get( '_token' ) ) ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $personalLicencium );
			$entityManager->flush();
		}

		return $this->redirectToRoute( 'personal_licencia_index' );
	}

	/**
	 * @Route("/{legajo}/nueva-licencia", name="personal_licencia_nueva", methods={"GET","POST"})
	 */
	public function nuevaLicencia( Request $request, Legajo $legajo ): Response {
		$personalLicencium = new PersonalLicencia();
		$personalLicencium->setLegajo( $legajo );
		$form = $this->createForm( PersonalLicenciaType::class, $personalLicencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalLicencium );
			$entityManager->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'La licencia se creó correctamente'
			);

			return $this->redirectToRoute( 'personal_licencias', [ 'legajo' => $legajo->getId() ] );
		}

		return $this->render( 'personal_licencia/nueva.html.twig',
			[
				'personal_licencium' => $personalLicencium,
				'form'               => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/editar", name="personal_licencia_editar", methods={"GET","POST"})
	 */
	public function editarLicencia( Request $request, PersonalLicencia $personalLicencium ): Response {
		$form = $this->createForm( PersonalLicenciaType::class, $personalLicencium );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'La licencia se actualizó correctamente'
			);

			return $this->redirectToRoute( 'personal_licencias', [ 'legajo' => $personalLicencium->getLegajo()->getId() ] );
		}

		return $this->render( 'personal_licencia/editar.html.twig',
			[
				'personal_licencium' => $personalLicencium,
				'form'               => $form->createView(),
			] );
	}
}
