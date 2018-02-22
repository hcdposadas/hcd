<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {

        if ($this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('easyadmin');
        }

        return $this->render('AppBundle:Default:index.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function concejalAction()
    {
        return $this->render(':default:concejal.html.twig');
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction()
    {
        return $this->render('default/display.html.twig');
    }
}
