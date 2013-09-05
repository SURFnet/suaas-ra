<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\VerifyMollieTokenType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
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
        $user = $this->get('security.context')->getToken()->getUser();
        $hasToken = $this->get('suaas.service.authentication_method')->hasToken($user);

        if ($hasToken) {
            $this->get('session')->set('_target_route', 'self_service_selecttoken');
        }

        return array(
            'user' => $user,
            'tokenWarning' => $hasToken
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
        /** @var \SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType $form */
        /** @var \SURFnet\SuAAS\DomainBundle\Service\MollieService $service */
        $command = new CreateMollieCommand();
        $form = $this->createForm(new CreateMollieType(), $command);
        $service = $this->get('suaas.service.mollie');
        $user = $this->get('security.context')->getToken()->getUser();

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $command = $form->getData();
            $command->user = $user;

            $service->createMollieToken($command);

            return $this->redirect($this->generateUrl('self_service_link_sms_auth'));
        }

        return array(
            'user' => $user,
            'tokenType' => 'SMS',
            'tokenExtended' => 'an SMS based one-time-password',
            'form' => $form->createView(),
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

        // call mollie to send sms if not done yet

        $token = $service->findTokenForUser(
            $this->get('security.context')->getToken()->getUser()
        );

        $command = new VerifyMollieTokenCommand();
        $form = $this->createForm(new VerifyMollieTokenType(), $command);

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            // update token
            // this->get('suaas.service.mollie')->verifyToken($token, $form->getData())

            return $this->redirect($this->generateUrl('self_service_confirm'));
        }

        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'tokenType' => 'SMS',
            'tokenExtended' => 'an SMS based one-time-password',
            'token' => $token,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/confirm-token", name="self_service_confirm")
     * @Template()
     *
     * @return array
     */
    public function smsConfirmAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $service = $this->get('suaas.service.mollie');
        $token = $service->findTokenForUser($user);

        $mail = $service->createActivationEmail($user, $token);
        $this->get('suaas.mailer')->sendMail($mail);

        return array(
            'user' => $this->get('security.context')->getToken()->getUser(),
            'token' => $token
        );
    }

    /**
     * @Route("/registration-instruction", name="self_service_registration_instruction")
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function smsRegisterTokenAction()
    {
        $service = $this->get('suaas.service.mollie');
        $user = $this->get('security.context')->getToken()->getUser();

        $token = $service->findTokenForUser($user);
        if ($token->hasRegistrationCode()) {
            $this->get('session')->set('error_message', 'The token already has a registration code attached');
            return $this->redirect($this->generateUrl('error'));
        }

        $mail = $service->createRegistrationMail($user, $token);
        // quick hack
        $code = $mail->parameters['code'];
        $this->get('suaas.mailer')->sendMail($mail);

        return array(
            'user' => $user,
            'code' => $code
        );
    }

    /**
     * @Route("/registration-code/", name="self_service_registration")
     * @Method("GET")
     *
     * @return array
     *
     * @throws BadRequestHttpException
     */
    public function confirmTokenAction()
    {
        $registrationCode = $this->getRequest()->get('c', false);
        $userHash = $this->getRequest()->get('n', false);

        if ($registrationCode === false || $userHash === false) {
            throw new BadRequestHttpException("Invalid Request");
        }

        $user = $this->get('suaas.service.user')->loadUserByUsername($userHash);
        $service = $this->get('suaas.service.authentication_method');

        if (!$service->confirmRegistration($user, $registrationCode)) {
            $this->get('session')->set('error_message', 'The registration URL you tried to use is invalid');
            return $this->redirect($this->generateUrl('error'));
        }

        return $this->redirect($this->generateUrl('self_service_registration_instruction'));
    }

    /**
     * @Route("/clear-tokens", name="self_service_clear_tokens")
     *
     * @return Response
     */
    public function dropTokens()
    {
        $session = $this->get('session');
        $targetRoute = $session->get('_target_route');
        $session->remove('_target_route');

        $this
            ->get('suaas.service.authentication_method')
            ->removeTokensForUser(
                $this->get('security.context')->getToken()->getUser()
            );

        return $this->redirect($this->generateUrl($targetRoute));
    }
}
