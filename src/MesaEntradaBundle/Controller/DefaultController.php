<?php

namespace MesaEntradaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MesaEntradaBundle:Default:index.html.twig');
    }
}
