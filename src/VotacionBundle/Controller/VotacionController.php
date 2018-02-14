<?php

namespace VotacionBundle\Controller;

use VotacionBundle\Entity\Mocion;
use VotacionBundle\Entity\Votacion;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use VotacionBundle\Form\FinalizarVotacionType;
use VotacionBundle\Form\VotacionType;

/**
 * Votacion controller.
 *
 * @Route("mocion/{mocion}/votacion")
 */
class VotacionController extends Controller
{
    /**
     * Lists all votacion entities.
     *
     * @Route("/", name="votacion_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request, Mocion $mocion)
    {
        $em = $this->getDoctrine()->getManager();

        $votaciones = $em->getRepository(Votacion::class)->findBy(array(
            'mocion' => $mocion->getId()
        ));

        $votacionEnCurso = array_reduce($votaciones, function ($carry, Votacion $votacion) {
            return $votacion->esActiva() ? $votacion : $carry;
        }, null);

        if (!$votacionEnCurso) {
            $lanzarForm = $this->createForm(VotacionType::class);
            $lanzarForm->handleRequest($request);

            if ($lanzarForm->isSubmitted() && $lanzarForm->isValid()) {
                $votacion = $this->get('votacion.service')
                    ->nuevaVotacion($mocion, $lanzarForm->getData()['duracion']);

                return $this->redirectToRoute('votacion_show', array(
                    'mocion' => $mocion->getId(),
                    'id' => $votacion->getId(),
                ));
            }
        }

        return $this->render('votacion/index.html.twig', array(
            'votaciones' => $votaciones,
            'votacionEnCurso' => $votacionEnCurso,
            'mocion' => $mocion,
            'lanzarForm' => $lanzarForm->createView(),
        ));
    }

    /**
     * Creates a new votacion entity.
     *
     * @Route("/new", name="votacion_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request, Mocion $mocion)
    {
        $form = $this->createForm(VotacionType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $votacion = $this->get('votacion.service')
                ->nuevaVotacion($mocion, $form->getData()['duracion']);

            return $this->redirectToRoute('votacion_show', array(
                'mocion' => $mocion->getId(),
                'id' => $votacion->getId(),
            ));
        }

        return $this->render('votacion/new.html.twig', array(
            'mocion' => $mocion,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a votacion entity.
     *
     * @Route("/{id}", name="votacion_show")
     * @Method("GET")
     */
    public function showAction(Request $request, Votacion $votacion)
    {
        $finalizarForm = $this->createFinalizarForm($votacion);
        $finalizarForm->handleRequest($request);

        return $this->render('votacion/show.html.twig', array(
            'votacion' => $votacion,
            'finalizar_form' => $finalizarForm->createView(),
        ));
    }

    /**
     * @Route("/{id}", name="votacion_finalizar")
     * @Method("POST")
     */
    public function finalizarAction(Request $request, Votacion $votacion)
    {
//        $finalizarForm = $this->createFinalizarForm($votacion);
//        $finalizarForm->handleRequest($request);
//
//        if ($finalizarForm->isSubmitted()) {
//            if ($finalizarForm->isValid()) {
                $this->get('votacion.service')->finalizarVotacion($votacion);

                return $this->redirectToRoute('mocion_show', array(
                    'id' => $votacion->getMocion()->getId(),
                ));
//            }
//        }
//
//        return $this->redirectToRoute('votacion_show', array(
//            'id' => $votacion->getId(),
//            'mocion' => $votacion->getMocion()->getId(),
//        ));
    }

    /**
     * Displays a form to edit an existing votacion entity.
     *
     * @Route("/{id}/edit", name="votacion_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Votacion $votacion)
    {
        $deleteForm = $this->createDeleteForm($votacion);
        $editForm = $this->createForm(VotacionType::class, $votacion);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('votacion_edit', array('id' => $votacion->getId()));
        }

        return $this->render('votacion/edit.html.twig', array(
            'votacion' => $votacion,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a votacion entity.
     *
     * @Route("/{id}", name="votacion_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Votacion $votacion)
    {
        $form = $this->createDeleteForm($votacion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($votacion);
            $em->flush();
        }

        return $this->redirectToRoute('votacion_index');
    }

    /**
     * Creates a form to delete a votacion entity.
     *
     * @param Votacion $votacion The votacion entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Votacion $votacion)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('votacion_delete', array(
                'id' => $votacion->getId(),
                'mocion' => $votacion->getMocion()->getId(),
            )))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    private function createFinalizarForm(Votacion $votacion) {
        return $this->createForm(FinalizarVotacionType::class, $votacion, array(
            'action' => $this->generateUrl('votacion_finalizar', array(
                'id' => $votacion->getId(),
                'mocion' => $votacion->getMocion()->getId(),
            ))
        ));
    }
}
