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
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class WizardController extends Controller
{
    /**
     * @Route("/start", name="self_service_start")
     *
     * @return array
     */
    public function indexAction()
    {
        $this->get('session')->set('target', 'self_service_selecttoken');
        return $this->redirect($this->generateUrl('saml_login'));
    }

    /**
     * @Route("/select-token", name="self_service_selecttoken")
     * @Template()
     *
     * @return array
     */
    public function selectTokenAction()
    {
        return array(
            'user' => $this->get('security.context')->getToken()->getUser()
        );
    }

    /**
     * @Route("/link-token/sms/instruction", name="self_service_link_sms_instr")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:smsInstruction.html.twig")
     *
     * @return array
     */
    public function linkTokenInstructionAction()
    {
        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'tokenType' => 'SMS',
            'tokenExtended' => 'an SMS based one-time-password'
        );
    }
}
