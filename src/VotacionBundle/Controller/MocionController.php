<?php

namespace VotacionBundle\Controller;

use DateTime;
use VotacionBundle\Entity\Mocion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use VotacionBundle\Form\MocionType;

/**
 * Mocion controller.
 *
 * @Route("mocion")
 */
class MocionController extends Controller
{
    /**
     * Lists all mocion entities.
     *
     * @Route("/", name="mocion_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mocions = $em->getRepository(Mocion::class)->findAll();

        return $this->render('mocion/index.html.twig', array(
            'mocions' => $mocions,
        ));
    }

    /**
     * Creates a new mocion entity.
     *
     * @Route("/new", name="mocion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $mocion = new Mocion();
        $form = $this->createForm(MocionType::class, $mocion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $mocion->setFecha(new DateTime());
            $mocion->setEstado(0);

            $em = $this->getDoctrine()->getManager();
            $em->persist($mocion);
            $em->flush();

            return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
        }

        return $this->render('mocion/new.html.twig', array(
            'mocion' => $mocion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a mocion entity.
     *
     * @Route("/{id}", name="mocion_show")
     * @Method("GET")
     */
    public function showAction(Mocion $mocion)
    {
        $deleteForm = $this->createDeleteForm($mocion);

        return $this->render('mocion/show.html.twig', array(
            'mocion' => $mocion,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mocion entity.
     *
     * @Route("/{id}/edit", name="mocion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Mocion $mocion)
    {
        $deleteForm = $this->createDeleteForm($mocion);
        $editForm = $this->createForm(MocionType::class, $mocion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('mocion_edit', array('id' => $mocion->getId()));
        }

        return $this->render('mocion/edit.html.twig', array(
            'mocion' => $mocion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a mocion entity.
     *
     * @Route("/{id}", name="mocion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Mocion $mocion)
    {
        $form = $this->createDeleteForm($mocion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($mocion);
            $em->flush();
        }

        return $this->redirectToRoute('mocion_index');
    }

    /**
     * Creates a form to delete a mocion entity.
     *
     * @param Mocion $mocion The mocion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Mocion $mocion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('mocion_delete', array('id' => $mocion->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
