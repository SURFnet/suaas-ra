<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('SURFnetSuAASSelfServiceBundle:Default:index.html.twig', array('name' => $name));
    }
}
