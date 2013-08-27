<?php

namespace SURFnet\SuAAS\RABundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SURFnetSuAASRABundle:Default:index.html.twig', array('name' => $name));
    }
}
