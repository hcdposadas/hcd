<?php

namespace VotacionBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use VotacionBundle\Entity\Votacion;
use VotacionBundle\Form\VotacionType;
use VotacionBundle\Repository\VotacionRepository;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
        $votaciones = $this->get('doctrine.orm.default_entity_manager')
            ->getRepository(Votacion::class)
            ->findAll();

        return $this->render('VotacionBundle:Default:index.html.twig', array(
            'votaciones' => $votaciones,
        ));
    }

    /**
     * @Route("/new/:duracion")
     */
    public function newAction($duracion = 10)
    {
        $votacion = $this->get('votacion.service')->nuevaVotacion($duracion);

        return $this->redirectToRoute('votacion_default_index');
    }

    /**
     * @Route("votar")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function votarAction()
    {
        return $this->render('@Votacion/Default/votar.html.twig');
    }
}
