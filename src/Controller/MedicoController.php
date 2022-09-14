<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Filter\PersonaFilterType;
use App\Form\PacienteType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Paciente;
use Symfony\Component\Routing\Annotation\Route;

class MedicoController extends AbstractController
{
    /**
     * @Route("/medico", name="medico")
     */
    public function index(PaginatorInterface $paginator,Request $request )
    {
        $em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm( PersonaFilterType::class,
			null,
			[
				'method' => 'GET'
			] );

		$filterType->handleRequest( $request );


		if ( $filterType->get( 'buscar' )->isClicked() ) {
			//$pacientes = $em->getRepository( Paciente::class )->getQbAll($filterType->getData());
		} else {

			$pacientes = $em->getRepository( Paciente::class )->getQbAll();
		}

		$pacientes = $paginator->paginate(
			$pacientes,
			$request->query->get( 'page', 1 )/* page number */,
			10/* limit per page */
		);

		return $this->render( 'medico/index.html.twig',
			array(
				'pacientes'    => $pacientes,
				'filter_type' => $filterType->createView()
			) );
	}


	/**
	 * Creates a new persona entity.
	 *
	 */
    public function new( Request $request ) {
		$paciente = new Paciente();
		$form    = $this->createForm( PacienteType::class, $paciente );
		$form->handleRequest( $request );

		$filterType = $this->createForm( PersonaFilterType::class,
			null,
			[
				'method' => 'GET'
			] );


		if ( $form->isSubmitted() && $form->isValid() ) {
			$em = $this->getDoctrine()->getManager();
			$em->persist( $paciente );
			$em->flush();

			return $this->redirectToRoute( 'paciente_show', array( 'id' => $paciente->getId() ) );
		}

		return $this->render( 'medico/new.html.twig',
			array(
				'paciente' => $paciente,
				'form'    => $form->createView(),
				'filter_type' => $filterType->createView()
			) );
	}

    /**
	 * Finds and displays a persona entity.
	 *
	 */
	public function show( Paciente $paciente ) {
        //		$deleteForm = $this->createDeleteForm( $persona );
        
                return $this->render( 'medico/show.html.twig',
                    array(
                        'paciente' => $paciente,
        //				'delete_form' => $deleteForm->createView(),
                    ) );
            }

}



