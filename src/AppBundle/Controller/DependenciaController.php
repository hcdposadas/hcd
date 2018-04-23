<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Dependencia;
use AppBundle\Form\Filter\DependenciaFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Dependencia controller.
 *
 */
class DependenciaController extends Controller
{
    /**
     * Lists all dependencium entities.
     *
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filterType = $this->createForm(DependenciaFilterType::class, null,
            [
                'method' => 'GET'
            ]);

        $filterType->handleRequest($request);


        if ($filterType->get('buscar')->isClicked()) {

            $dependencias = $em->getRepository('AppBundle:Dependencia')->getQbAll($filterType->getData());
        } else {

            $dependencias = $em->getRepository('AppBundle:Dependencia')->getQbAll();
        }


        $paginator = $this->get('knp_paginator');
        $dependencias = $paginator->paginate(
            $dependencias,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('dependencia/index.html.twig', array(
            'dependencias' => $dependencias,
            'filter_type' => $filterType->createView()
        ));
    }

    /**
     * Creates a new dependencium entity.
     *
     */
    public function newAction(Request $request)
    {
        $dependencium = new Dependencia();
        $form = $this->createForm('AppBundle\Form\DependenciaType', $dependencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($dependencium);
            $em->flush();

            return $this->redirectToRoute('dependencia_show', array('id' => $dependencium->getId()));
        }

        return $this->render('dependencia/new.html.twig', array(
            'dependencium' => $dependencium,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a dependencium entity.
     *
     */
    public function showAction(Dependencia $dependencium)
    {
        $deleteForm = $this->createDeleteForm($dependencium);

        return $this->render('dependencia/show.html.twig', array(
            'dependencium' => $dependencium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing dependencium entity.
     *
     */
    public function editAction(Request $request, Dependencia $dependencium)
    {
        $deleteForm = $this->createDeleteForm($dependencium);
        $editForm = $this->createForm('AppBundle\Form\DependenciaType', $dependencium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('dependencia_edit', array('id' => $dependencium->getId()));
        }

        return $this->render('dependencia/edit.html.twig', array(
            'dependencium' => $dependencium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a dependencium entity.
     *
     */
    public function deleteAction(Request $request, Dependencia $dependencium)
    {
        $form = $this->createDeleteForm($dependencium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($dependencium);
            $em->flush();
        }

        return $this->redirectToRoute('dependencia_index');
    }

    /**
     * Creates a form to delete a dependencium entity.
     *
     * @param Dependencia $dependencium The dependencium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Dependencia $dependencium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('dependencia_delete', array('id' => $dependencium->getId())))
            ->setMethod('DELETE')
            ->getForm();
    }
}
