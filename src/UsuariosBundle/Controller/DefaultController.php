<?php

namespace UsuariosBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('UsuariosBundle:Default:index.html.twig');
    }
}
