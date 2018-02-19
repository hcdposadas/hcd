<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Parametro;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Parametro controller.
 *
 */
class ParametroController extends Controller
{
    /**
     * Lists all parametro entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $parametros = $em->getRepository(Parametro::class)->findAll();

        return $this->render('parametro/index.html.twig', array(
            'parametros' => $parametros,
        ));
    }

    /**
     * Creates a new parametro entity.
     *
     */
    public function newAction(Request $request)
    {
        $parametro = new Parametro();
        $form = $this->createForm('AppBundle\Form\ParametroType', $parametro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $parametro->setActivo(true);
            $em->persist($parametro);
            $em->flush();

            return $this->redirectToRoute('parametro_show', array('id' => $parametro->getId()));
        }

        return $this->render('parametro/new.html.twig', array(
            'parametro' => $parametro,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a parametro entity.
     *
     */
    public function showAction(Parametro $parametro)
    {
        $deleteForm = $this->createDeleteForm($parametro);

        return $this->render('parametro/show.html.twig', array(
            'parametro' => $parametro,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing parametro entity.
     *
     */
    public function editAction(Request $request, Parametro $parametro)
    {
        $deleteForm = $this->createDeleteForm($parametro);
        $editForm = $this->createForm('AppBundle\Form\ParametroType', $parametro);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('parametro_edit', array('id' => $parametro->getId()));
        }

        return $this->render('parametro/edit.html.twig', array(
            'parametro' => $parametro,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a parametro entity.
     *
     */
    public function deleteAction(Request $request, Parametro $parametro)
    {
        $form = $this->createDeleteForm($parametro);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($parametro);
            $em->flush();
        }

        return $this->redirectToRoute('parametro_index');
    }

    /**
     * Creates a form to delete a parametro entity.
     *
     * @param Parametro $parametro The parametro entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Parametro $parametro)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('parametro_delete', array('id' => $parametro->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
