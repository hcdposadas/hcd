<?php

namespace MesaEntradaBundle\Controller;

use MesaEntradaBundle\Entity\Expediente;
use MesaEntradaBundle\Form\Filter\ExpedienteFilterType;
use MesaEntradaBundle\Form\Filter\SeguimientoExpedienteFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $filterType = $this->createForm(ExpedienteFilterType::class,
            null,
            [
                'method' => 'GET'
            ]);

        $filterType->handleRequest($request);

//		if ( $request->query->has( $filterType->getName() ) ) {
//			$filterType->submit( $request->query->get( $filterType->getName() ) );
//		}

        if ($filterType->get('buscar')->isClicked()) {

            $expedientes = $em->getRepository('MesaEntradaBundle:Expediente')->getQbBuscar($filterType->getData());
        } else {

            $expedientes = $em->getRepository('MesaEntradaBundle:Expediente')->getQbAll();
        }


        $paginator = $this->get('knp_paginator');

        $expedientes = $paginator->paginate(
            $expedientes,
            $request->query->get('page', 1)/* page number */,
            10/* limit per page */
        );

        return $this->render('expediente/index.html.twig',
            array(
                'expedientes' => $expedientes,
                'filter_type' => $filterType->createView()
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

            $this->get('session')->getFlashBag()->add(
                'success',
                'Expediente creado correctamente'
            );


            return $this->redirectToRoute('expediente_show', array('id' => $expediente->getId()));
        }

        return $this->render('expediente/new.html.twig',
            array(
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

        return $this->render('expediente/show.html.twig',
            array(
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
            $this->get('session')->getFlashBag()->add(
                'success',
                'Expediente modificado correctamente'
            );

            return $this->redirectToRoute('expediente_edit', array('id' => $expediente->getId()));
        }

        return $this->render('expediente/edit.html.twig',
            array(
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
            ->getForm();
    }

    public function seguimientoExpedienteAction(Request $request)
    {

        $filterForm = $this->createForm(SeguimientoExpedienteFilterType::class);
        $expedientes = array();
        if ($request->isMethod('post')) {
            $filterForm->handleRequest($request);
            $data = $filterForm->getData();
            $em = $this->getDoctrine()->getManager();
//            print_r($data);
//            exit;
            $expedientes = $em->getRepository('MesaEntradaBundle:Expediente')->search($data);

        }

        return $this->render('expediente/seguimiento.html.twig',
            array(
                'filter_form' => $filterForm->createView(),
                'entities' => $expedientes
            ));
    }

    public function seguimientoExpedienteTimelineAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $expediente = $em->getRepository('MesaEntradaBundle:Expediente')->find($id);

        return $this->render('expediente/timeline.html.twig',
            array(
                'expediente' => $expediente
            ));

    }

    public function imprimirCaratulaAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $expediente = $em->getRepository('MesaEntradaBundle:Expediente')->find($id);
	    $title = 'Carátula';

        $html = $this->renderView('expediente/caratula.pdf.twig',
            [
                'expediente' => $expediente,
                'title' => $title,
            ]
        );

//        return new Response($html);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
                array(
                    'margin-left' => "3cm",
                    'margin-right' => "3cm",
                    'margin-top' => "3cm",
//                    'margin-bottom' => "1cm"
                )
            )
            , 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
            )
        );

    }

    public function imprimirGiroAction($id, $giroId)
    {
        $em = $this->getDoctrine()->getManager();
        $expediente = $em->getRepository('MesaEntradaBundle:Expediente')->find($id);
        if (strtoupper($expediente->getTipoExpediente()) == 'INTERNO') {
            $giro = $em->getRepository('MesaEntradaBundle:GiroAdministrativo')->find($giroId);
            $giro = $giro->getAreaDestino();
        } else {
            $giro = $em->getRepository('MesaEntradaBundle:Giro')->find($giroId);
            $giro = $giro->getComisionDestino();
        }

        $title = 'Giro';

        $html = $this->renderView('expediente/giro.pdf.twig',
            [
                'expediente' => $expediente,
                'giro' => $giro,
                'title' => $title,
            ]
        );

//        return new Response($html);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html,
                array(
                    'margin-left' => "3cm",
                    'margin-right' => "3cm",
                    'margin-top' => "3cm",
//                    'margin-bottom' => "1cm"
                )
            )
            , 200, array(
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $title . '.pdf"'
            )
        );
    }


}
