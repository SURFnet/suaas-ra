<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType;
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
    public function linkSMSInstructionAction()
    {
        $command = new CreateMollieCommand();
        $form = $this->createForm(new CreateMollieType(), $command);

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $command = $form->getData();
            $command->user = $this->get('security.context')->getToken()->getUser();

            $this->get('suaas.service.mollie')->createMollieToken($command);

            return $this->redirect($this->generateUrl('self_service_link_sms_auth'));
        }

        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'tokenType' => 'SMS',
            'tokenExtended' => 'an SMS based one-time-password',
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/link-token/sms/authentication", name="self_service_link_sms_auth")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:smsAuthentication.html.twig")
     *
     * @return array
     */
    public function smsAuthenticationAction()
    {
        $service = $this->get('suaas.service.mollie');
        $token = $service->findTokenForUser(
            $this->get('security.context')->getToken()->getUser()
        );

        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'tokenType' => 'SMS',
            'tokenExtended' => 'an SMS based one-time-password',
            'token' => $token
//            'form' => $form->createView()
        );
    }
}
