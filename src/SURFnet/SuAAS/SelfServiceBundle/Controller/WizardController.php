<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DefaultController
 * @package SURFnet\SuAAS\SelfServiceBundle\Controller
 *
 * @Route("/self-service")
 *
 * @author Daan van Renterghem <dvrenterghem@gmail.com>
 */
class WizardController extends Controller
{
    /**
     * @Route("/start", name="self_registration_start")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->get('session')->set('target', 'self_registration_selecttoken');
        return $this->redirect($this->generateUrl('saml_login'));
    }

    /**
     * @Route("/select-token", name="self_registration_selecttoken")
     * @Template()
     *
     * @return array
     */
    public function selectTokenAction()
    {
        throw new HttpException(418, 'Not Yet Implemented');
    }
}
