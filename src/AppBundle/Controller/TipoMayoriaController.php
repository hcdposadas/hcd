<?php

namespace AppBundle\Controller;

use AppBundle\Entity\TipoMayoria;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * TipoMayoria controller.
 *
 */
class TipoMayoriaController extends Controller
{
    /**
     * Lists all tipoMayorium entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $tipoMayorias = $em->getRepository('AppBundle:TipoMayoria')->findAll();

        return $this->render('tipomayoria/index.html.twig', array(
            'tipoMayorias' => $tipoMayorias,
        ));
    }

    /**
     * Creates a new tipoMayorium entity.
     *
     */
    public function newAction(Request $request)
    {
        $tipoMayoria = new TipoMayoria();
        $form = $this->createForm('AppBundle\Form\TipoMayoriaType', $tipoMayoria);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($tipoMayoria);
            $em->flush();

            return $this->redirectToRoute('tipomayoria_show', array('id' => $tipoMayoria->getId()));
        }

        return $this->render('tipomayoria/new.html.twig', array(
            'tipoMayorium' => $tipoMayoria,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a tipoMayorium entity.
     *
     */
    public function showAction(TipoMayoria $tipoMayorium)
    {
        $deleteForm = $this->createDeleteForm($tipoMayorium);

        return $this->render('tipomayoria/show.html.twig', array(
            'tipoMayorium' => $tipoMayorium,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing tipoMayorium entity.
     *
     */
    public function editAction(Request $request, TipoMayoria $tipoMayorium)
    {
        $deleteForm = $this->createDeleteForm($tipoMayorium);
        $editForm = $this->createForm('AppBundle\Form\TipoMayoriaType', $tipoMayorium);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('tipomayoria_edit', array('id' => $tipoMayorium->getId()));
        }

        return $this->render('tipomayoria/edit.html.twig', array(
            'tipoMayorium' => $tipoMayorium,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a tipoMayorium entity.
     *
     */
    public function deleteAction(Request $request, TipoMayoria $tipoMayorium)
    {
        $form = $this->createDeleteForm($tipoMayorium);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($tipoMayorium);
            $em->flush();
        }

        return $this->redirectToRoute('tipomayoria_index');
    }

    /**
     * Creates a form to delete a tipoMayorium entity.
     *
     * @param TipoMayoria $tipoMayorium The tipoMayorium entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(TipoMayoria $tipoMayorium)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('tipomayoria_delete', array('id' => $tipoMayorium->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
