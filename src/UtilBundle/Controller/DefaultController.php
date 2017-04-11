<?php

namespace UtilBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UtilBundle:Default:index.html.twig');
    }
}
