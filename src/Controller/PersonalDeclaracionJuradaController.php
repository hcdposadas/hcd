<?php

namespace App\Controller;

use App\Entity\Persona;
use App\Entity\PersonaACargo;
use App\Entity\PersonalDDJJConyuge;
use App\Entity\PersonalDDJJPersonaACargo;
use App\Entity\PersonalDeclaracionJurada;
use App\Form\PersonalDeclaracionJurada1Type;
use App\Repository\PersonalDeclaracionJuradaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/personal/declaracion/jurada")
 */
class PersonalDeclaracionJuradaController extends AbstractController {
	/**
	 * @Route("/", name="personal_declaracion_jurada_index", methods={"GET"})
	 */
	public function index( PersonalDeclaracionJuradaRepository $personalDeclaracionJuradaRepository ): Response {
		return $this->render( 'personal_declaracion_jurada/index.html.twig',
			[
				'personal_declaracion_juradas' => $personalDeclaracionJuradaRepository->findAll(),
			] );
	}

	/**
	 * @Route("/{persona}", name="personal_declaraciones_jurada", methods={"GET"})
	 */
	public function declaraciones(
		PaginatorInterface $paginator,
		Request $request,
		PersonalDeclaracionJuradaRepository $personalDeclaracionJuradaRepository,
		Persona $persona
	): Response {

//		$declaracionesJuradas = $persona->getLegajo()->getPersonalDeclaracionJuradas();


		$declaracionesJuradas = $personalDeclaracionJuradaRepository->getQbAll(
			[ 'legajo' => $persona->getLegajo() ],
			[ 'anio' => 'DESC' ]
		);

		$declaracionesJuradas = $paginator->paginate(
			$declaracionesJuradas,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'personal_declaracion_jurada/indexdeclaraciones.html.twig',
			[
				'personal_declaraciones_juradas' => $declaracionesJuradas,
				'persona'                        => $persona
			] );
	}

	/**
	 * @Route("/new", name="personal_declaracion_jurada_new", methods={"GET","POST"})
	 */
	public function new( Request $request ): Response {
		$personalDeclaracionJurada = new PersonalDeclaracionJurada();
		$form                      = $this->createForm( PersonalDeclaracionJurada1Type::class,
			$personalDeclaracionJurada );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalDeclaracionJurada );
			$entityManager->flush();

			return $this->redirectToRoute( 'personal_declaracion_jurada_index' );
		}

		return $this->render( 'personal_declaracion_jurada/new.html.twig',
			[
				'personal_declaracion_jurada' => $personalDeclaracionJurada,
				'form'                        => $form->createView(),
			] );
	}

	/**
	 * @Route("/{persona}/nueva-ddjj", name="personal_declaracion_jurada_nueva", methods={"GET","POST"})
	 */
	public function nuevaDDJJ( Request $request, Persona $persona ): Response {


		$personalDeclaracionJurada = new PersonalDeclaracionJurada();

		$personalDeclaracionJurada->setLegajo( $persona->getLegajo() );
		$anio = new \DateTime( 'now' );
		$personalDeclaracionJurada->setAnio( $anio->format( 'Y' ) );


		$form = $this->createForm( PersonalDeclaracionJurada1Type::class,
			$personalDeclaracionJurada );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->persist( $personalDeclaracionJurada );
			$entityManager->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'DDJJ creda correctamente'
			);

			return $this->redirectToRoute( 'personal_declaraciones_jurada', [ 'persona' => $persona->getId() ] );
		}

		return $this->render( 'personal_declaracion_jurada/nuevaDDJJ.html.twig',
			[
				'personal_declaracion_jurada' => $personalDeclaracionJurada,
				'persona'                     => $persona,
				'form'                        => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}", name="personal_declaracion_jurada_show", methods={"GET"})
	 */
	public function show( PersonalDeclaracionJurada $personalDeclaracionJurada ): Response {
		return $this->render( 'personal_declaracion_jurada/show.html.twig',
			[
				'personal_declaracion_jurada' => $personalDeclaracionJurada,
			] );
	}

	/**
	 * @Route("/{id}/edit", name="personal_declaracion_jurada_edit", methods={"GET","POST"})
	 */
	public function edit( Request $request, PersonalDeclaracionJurada $personalDeclaracionJurada ): Response {
		$form = $this->createForm( PersonalDeclaracionJurada1Type::class, $personalDeclaracionJurada );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {
			$this->getDoctrine()->getManager()->flush();

			return $this->redirectToRoute( 'personal_declaracion_jurada_index' );
		}

		return $this->render( 'personal_declaracion_jurada/edit.html.twig',
			[
				'personal_declaracion_jurada' => $personalDeclaracionJurada,
				'form'                        => $form->createView(),
			] );
	}

	/**
	 * @Route("/{id}/editarDDJJ", name="personal_declaracion_jurada_editar", methods={"GET","POST"})
	 */
	public function editarDDJJ( Request $request, PersonalDeclaracionJurada $personalDeclaracionJurada ): Response {

		$personaACargoOriginal = new ArrayCollection();

		// Create an ArrayCollection of the current Tag objects in the database
		foreach ( $personalDeclaracionJurada->getPersonalDDJJPersonaACargos() as $personaDDJJACargo ) {
			$personaACargoOriginal->add( $personaDDJJACargo );
		}

		$form = $this->createForm( PersonalDeclaracionJurada1Type::class, $personalDeclaracionJurada );
		$form->handleRequest( $request );

		if ( $form->isSubmitted() && $form->isValid() ) {

			$this->getDoctrine()->getManager()->flush();

			$this->get( 'session' )->getFlashBag()->add(
				'success',
				'DDJJ modificada correctamente'
			);

			return $this->redirectToRoute( 'personal_declaraciones_jurada',
				[ 'persona' => $personalDeclaracionJurada->getLegajo()->getPersona()->getId() ] );
		}

		return $this->render( 'personal_declaracion_jurada/editarDDJJ.html.twig',
			[
				'personal_declaracion_jurada' => $personalDeclaracionJurada,
				'form'                        => $form->createView(),
				'persona'                     => $personalDeclaracionJurada->getLegajo()->getPersona()
			] );
	}

	/**
	 * @Route("/{id}", name="personal_declaracion_jurada_delete", methods={"DELETE"})
	 */
	public function delete( Request $request, PersonalDeclaracionJurada $personalDeclaracionJurada ): Response {
		if ( $this->isCsrfTokenValid( 'delete' . $personalDeclaracionJurada->getId(),
			$request->request->get( '_token' ) ) ) {
			$entityManager = $this->getDoctrine()->getManager();
			$entityManager->remove( $personalDeclaracionJurada );
			$entityManager->flush();
		}

		return $this->redirectToRoute( 'personal_declaracion_jurada_index' );
	}
}
