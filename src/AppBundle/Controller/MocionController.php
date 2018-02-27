<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\Voto;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Mocion controller.
 *
 */
class MocionController extends Controller
{
    /**
     * Lists all mocion entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $mocions = $em->getRepository('AppBundle:Mocion')->findAll();

        return $this->render('mocion/index.html.twig', array(
            'mocions' => $mocions,
        ));
    }

    /**
     * Creates a new mocion entity.
     *
     */
    public function newAction(Request $request)
    {
        $mocion = new Mocion();

        if ($request->get('tipo') === 'mocion-tipo-espontanea') {
            $mocion->setTipo($this->get('doctrine.orm.default_entity_manager')
                    ->getRepository(Parametro::class)
                    ->getBySlug(Mocion::TIPO_ESPONTANEA));
        }

        $form = $this->createForm('AppBundle\Form\MocionType', $mocion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->get('votacion.manager')->crear($mocion);

            if ($request->get('guardar-y-lanzar')) {
                try {
                    $this->get('votacion.manager')->lanzar($mocion);
                } catch (\Exception $ex) {
                    $this->addFlash(
                        'error',
                        $ex->getMessage()
                    );
                }

                return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
            }

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
     */
    public function showAction(Request $request, Mocion $mocion)
    {
        $deleteForm = $this->createDeleteForm($mocion);

        return $this->render('mocion/show.html.twig', array(
            'mocion' => $mocion,
            'segundos' => 15,
            'votar' => false,
            'lanzar' => false,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Finds and displays a mocion entity.
     *
     */
    public function votarAction(Request $request, Mocion $mocion)
    {
        $deleteForm = $this->createDeleteForm($mocion);

        $enVotacion = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository(Mocion::class)
            ->getEnVotacion();

        if ($enVotacion) {
            $this->addFlash(
                'error',
                'No se puede votar esta moción porque la Moción Nº'.$enVotacion.' se encuentra en votación.'
            );
            return $this->redirectToRoute('mocion_show', array(
                'id' => $mocion->getId()
            ));
        }

        return $this->render('mocion/show.html.twig', array(
            'mocion' => $mocion,
            'segundos' => 15,
            'votar' => true,
            'lanzar' => false,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing mocion entity.
     *
     */
    public function editAction(Request $request, Mocion $mocion)
    {
        $deleteForm = $this->createDeleteForm($mocion);
        $editForm = $this->createForm('AppBundle\Form\MocionType', $mocion);
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

    /**
     * @param Mocion $mocion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function lanzarVotacionAction(Mocion $mocion)
    {
        try {
            $votacion = $this->get('votacion.manager')->lanzar($mocion);
        } catch (\Exception $ex) {
            $this->addFlash(
                'error',
                $ex->getMessage()
            );
        }

        return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
    }

    /**
     * @param Mocion $mocion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function extenderVotacionAction(Mocion $mocion)
    {
        try {
            $votacion = $this->get('votacion.manager')->extender($mocion);
        } catch (\Exception $ex) {
            $this->addFlash(
                'error',
                $ex->getMessage()
            );
        }

        return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
    }

    /**
     * @param Mocion $mocion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function resultadosVotacionAction(Mocion $mocion)
    {
        try {
            $votacion = $this->get('votacion.manager')->calcularResultados($mocion);
        } catch (\Exception $ex) {
            $this->addFlash(
                'error',
                $ex->getMessage()
            );
        }

        return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
    }

    /**
     * @param Mocion $mocion
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finalizarVotacionAction(Mocion $mocion)
    {
        try {
            $this->get('votacion.manager')->finalizar($mocion);

            $this->addFlash(
                'success',
                'La votación finalizó correctamente'
            );
        } catch (\Exception $ex) {
            $this->addFlash(
                'error',
                $ex->getMessage()
            );
        }

        return $this->redirectToRoute('mocion_show', array('id' => $mocion->getId()));
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function votoConcejalAction(Request $request)
    {
        try {
            // TODO verificar que el usuario sea concejal

            $usuario = $this->getUser();

            $mocion = $this->get('doctrine.orm.default_entity_manager')->getRepository(Mocion::class)->getEnVotacion();
            if (!$mocion) {
                throw new Exception('No hay una moción en votación en este momento');
            }

            $data = json_decode($request->getContent(), JSON_OBJECT_AS_ARRAY);
            $valorVoto = $data['voto'];

            if ($valorVoto == 'no') {
                $valorVoto = Voto::VOTO_NEGATIVO;
            } else if ($valorVoto == 'si') {
                $valorVoto = Voto::VOTO_AFIRMATIVO;
            } else {
                throw new Exception('El valor del voto no es válido ('.$valorVoto.')');
            }

            $voto = $this->get('votacion.manager')->votar($mocion, $usuario, $valorVoto);

            return JsonResponse::create(array(
                'status' => 'success',
                'data' => array(
                    'voto' => array(
                        'id' => $voto->getId(),
                    ),
                ),
            ));
        } catch (Exception $ex) {
            return JsonResponse::create(array(
                'status' => 'error',
                'message' => $ex->getMessage(),
            ));
        }
    }
}
