<?php

namespace App\Controller;

use App\Entity\Legajo;
use App\Entity\Persona;
use App\Entity\PersonalNovedad;
use App\Form\PersonalNovedadType;
use App\Repository\PersonalNovedadRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personal/novedad")
 */
class PersonalNovedadController extends AbstractController {
	/**
	 * @Route("/", name="personal_novedad_index", methods={"GET"})
	 */
	public function index( PersonalNovedadRepository $personalNovedadRepository ): Response {
		return $this->render( 'personal_novedad/index.html.twig',
			[
				'personal_novedads' => $personalNovedadRepository->findAll(),
			] );
	}

	/**
	 * @Route("/{persona}", name="personal_novedades", methods={"GET"})
	 */
	public function novedades(
		PaginatorInterface $paginator,
		Request $request,
		PersonalNovedadRepository $personalNovedadRepository,
		Persona $persona
	): Response {

		$legajo = $persona->getLegajo();

		if ( ! $legajo ) {
			$this->get( 'session' )->getFlashBag()->add(
				'warning',
				'La Persona no tiene legajo'
			);

			return $this->redirectToRoute( 'persona_index' );
		}

		$novedades = $personalNovedadRepository->getQbAll( [ 'legajo' => $legajo ] );

		$novedades = $paginator->paginate(
			$novedades,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'personal_novedad/novedades_index.html.twig',
			[
				'novedades' => $novedades,
				'persona'   => $persona
			] );
	}


	/**
	 * @Route("/new", name="personal_novedad_new", methods={"GET","POST"})
	 */
	public function new( Request $request ): Response {
		$personalNovedad = new PersonalNovedad();
		$form            = $this->createForm( PersonalNovedadType::class, $personalNovedad );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalNovedad );
			$entityManager->flush();

			return $this->redirectToRoute( 'personal_novedad_index' );
		}

		return $this->render( 'personal_novedad/new.html.twig',
			[
				'personal_novedad' => $personalNovedad,
				'form'             => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/ver", name="personal_novedad_show", methods={"GET"})
	 */
	public function show( PersonalNovedad $personalNovedad ): Response {
		return $this->render( 'personal_novedad/show.html.twig',
			[
				'personal_novedad' => $personalNovedad,
			] );
	}

	/**
	 * @Route("/{id}/edit", name="personal_novedad_edit", methods={"GET","POST"})
	 */
	public function edit( Request $request, PersonalNovedad $personalNovedad ): Response {
		$form = $this->createForm( PersonalNovedadType::class, $personalNovedad );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'personal_novedad_index' );
		}

		return $this->render( 'personal_novedad/edit.html.twig',
			[
				'personal_novedad' => $personalNovedad,
				'form'             => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_novedad_delete", methods={"DELETE"})
	 */
	public function delete( Request $request, PersonalNovedad $personalNovedad ): Response {
		if ( $this->isCsrfTokenValid( 'delete' . $personalNovedad->getId(), $request->request->get( '_token' ) ) ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $personalNovedad );
			$entityManager->flush();
		}

		return $this->redirectToRoute( 'personal_novedad_index' );
	}

	/**
	 * @Route("/{legajo}/nueva-novedad", name="personal_novedad_nueva", methods={"GET","POST"})
	 */
	public function nuevaNovedad( Request $request, Legajo $legajo ): Response {
		$personalNovedad = new PersonalNovedad();
		$personalNovedad->setLegajo( $legajo );
		$form = $this->createForm( PersonalNovedadType::class, $personalNovedad );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalNovedad );
			$entityManager->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'La novedad se creó correctamente'
			);

			return $this->redirectToRoute( 'personal_novedades', [ 'persona' => $legajo->getPersona()->getId() ] );
		}

		return $this->render( 'personal_novedad/nueva.html.twig',
			[
				'personal_novedad' => $personalNovedad,
				'form'             => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/edit-novedad", name="personal_novedad_editar", methods={"GET","POST"})
	 */
	public function editarNovedad( Request $request, PersonalNovedad $personalNovedad ): Response {
		$form = $this->createForm( PersonalNovedadType::class, $personalNovedad );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'personal_novedad_index' );
		}

		return $this->render( 'personal_novedad/editar.html.twig',
			[
				'personal_novedad' => $personalNovedad,
				'form'             => $form->createView(),
			] );
	}
}
