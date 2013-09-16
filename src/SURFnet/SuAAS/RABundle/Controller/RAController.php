<?php

namespace SURFnet\SuAAS\RABundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Class RaController
 * @package SURFnet\SuAAS\RABundle\Controller
 *
 * @Route("/management")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class RAController extends Controller
{
    /**
     * @Route("/user-overview", name="management_user_overview")
     * @Template()
     */
    public function userOverviewAction()
    {
        return array();
    }
}
