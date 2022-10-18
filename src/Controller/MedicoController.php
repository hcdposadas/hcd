<?php

namespace App\Controller;

use App\Entity\OrdenMedica;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Filter\PersonaFilterType;
use App\Form\PacienteType;
use App\Form\OrdenType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Paciente;
use App\Entity\Persona;
use Symfony\Component\Routing\Annotation\Route;

class MedicoController extends AbstractController
{

	public function index(PaginatorInterface $paginator, Request $request)
	{
		$em = $this->getDoctrine()->getManager();

		$filterType = $this->createForm(
			PersonaFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);


		if ($filterType->get('buscar')->isClicked()) {
			$pacientes = $em->getRepository(Paciente::class)->getQbAll($filterType->getData());
		} else {

			$pacientes = $em->getRepository(Paciente::class)->findAll();
		}

		$pacientes = $paginator->paginate(
			$pacientes,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		return $this->render(
			'medico/index.html.twig',
			array(
				'pacientes'    => $pacientes,
				'filter_type' => $filterType->createView()
			)
		);
	}


	/**
	 * Creates a new persona entity.
	 *
	 */
	public function new($id, Request $request,  PaginatorInterface $paginator)
	{

		$em = $this->getDoctrine()->getManager();
		$personas = null;
		$paciente = null;
		if ($id) {
			$paciente = $em->getRepository(Persona::class)->find($id);
		}


		$form    = $this->createForm(PacienteType::class);
		$form->handleRequest($request);

		$filterType = $this->createForm(
			PersonaFilterType::class,
			null,
			[
				'method' => 'GET'
			]
		);

		$filterType->handleRequest($request);


		if ($filterType->get('buscar')->isClicked()) {
			$paciente = null;
			$personas = $em->getRepository(Persona::class)->getQbAll($filterType->getData());
		} else {

			$personas = $em->getRepository(Persona::class)->getQbAll();
		}

		$personas = $paginator->paginate(
			$personas,
			$request->query->get('page', 1)/* page number */,
			10/* limit per page */
		);

		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$paciente = new Paciente();
			$paciente->setPersona($em->getRepository(Persona::class)->find($id));
			$paciente->setFactor($data['factor']);
			$paciente->setGrupo($data['grupo']);
			$paciente->setFotoFile($data['foto']);
			$paciente->setObservaciones($data['observaciones']);
			$em = $this->getDoctrine()->getManager();
			$em->persist($paciente);
			$em->flush();

			return $this->redirectToRoute('paciente_show', array('id' => $paciente->getId()));
		}

		return $this->render(
			'medico/new.html.twig',
			array(
				'personas' => $personas,
				'paciente' => $paciente,
				'form'    => $form->createView(),
				'filter_type' => $filterType->createView()
			)
		);
	}

	/**
	 * Finds and displays a persona entity.
	 *
	 */
	public function show(Paciente $paciente, Request $request)
	{
		$orden = new OrdenMedica;
		$form    = $this->createForm(OrdenType::class);

		$form->handleRequest($request);
		if ($form->isSubmitted() && $form->isValid()) {
			$data = $form->getData();
			$orden->setMedicoOtorgante($data['medicoOtorgante']);
			$orden->setDesde($data['desde']);
			$orden->setHasta($data['hasta']);
			$orden->setArticulo($data['articulo']);
			$orden->setDiagnosticoFile($data['diagnostico']);
			$orden->setPaciente($paciente);
			$em = $this->getDoctrine()->getManager();
			$em->persist($orden);
			$em->flush();

			return $this->redirectToRoute('paciente_show', array('id' => $paciente->getId()));
		}



		return $this->render(
			'medico/show.html.twig',
			array(
				'form'    => $form->createView(),
				'paciente' => $paciente,
				//				'delete_form' => $deleteForm->createView(),
			)
		);
	}

	public function ordenDelete(OrdenMedica $orden)
	{
		$em = $this->getDoctrine()->getManager();
		$paciente = $orden->getPaciente();
		$em->remove($orden);

		return $this->redirectToRoute('paciente_show', array('id' => $paciente->getId()));
	}
}
