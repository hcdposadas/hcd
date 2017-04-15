<?php

namespace MesaEntradaBundle\Controller;

use MesaEntradaBundle\Entity\Expediente;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Expediente controller.
 *
 */
class ExpedienteController extends Controller
{
    /**
     * Lists all expediente entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $expedientes = $em->getRepository('MesaEntradaBundle:Expediente')->findAll();

        return $this->render('expediente/index.html.twig', array(
            'expedientes' => $expedientes,
        ));
    }

    /**
     * Creates a new expediente entity.
     *
     */
    public function newAction(Request $request)
    {
        $expediente = new Expediente();
        $form = $this->createForm('MesaEntradaBundle\Form\ExpedienteType', $expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($expediente);
            $em->flush();

            return $this->redirectToRoute('expediente_show', array('id' => $expediente->getId()));
        }

        return $this->render('expediente/new.html.twig', array(
            'expediente' => $expediente,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a expediente entity.
     *
     */
    public function showAction(Expediente $expediente)
    {
        $deleteForm = $this->createDeleteForm($expediente);

        return $this->render('expediente/show.html.twig', array(
            'expediente' => $expediente,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing expediente entity.
     *
     */
    public function editAction(Request $request, Expediente $expediente)
    {
        $deleteForm = $this->createDeleteForm($expediente);
        $editForm = $this->createForm('MesaEntradaBundle\Form\ExpedienteType', $expediente);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('expediente_edit', array('id' => $expediente->getId()));
        }

        return $this->render('expediente/edit.html.twig', array(
            'expediente' => $expediente,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a expediente entity.
     *
     */
    public function deleteAction(Request $request, Expediente $expediente)
    {
        $form = $this->createDeleteForm($expediente);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($expediente);
            $em->flush();
        }

        return $this->redirectToRoute('expediente_index');
    }

    /**
     * Creates a form to delete a expediente entity.
     *
     * @param Expediente $expediente The expediente entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Expediente $expediente)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('expediente_delete', array('id' => $expediente->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
