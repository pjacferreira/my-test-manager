<?php

namespace TestCenter\ModelBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('TestCenterModelBundle:Default:index.html.twig', array('name' => $name));
    }
}
