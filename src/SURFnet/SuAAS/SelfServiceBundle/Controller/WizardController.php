<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\VerifyMollieTokenType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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

            $this->get('session')->set('redirect_after_sms', 'self_service_link_sms_verify');
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
     * @Method("GET")
     */
    public function sendSmsAction()
    {
        $redirectAfter = $this->get('session')->get('redirect_after_sms', false);

        if ($redirectAfter === false) {
            return $this->error('You tried to access a page that could not be accessed');
        }
        $this->get('session')->remove('redirect_after_sms');

        $service = $this->get('suaas.service.mollie');
        $user = $this->get('security.context')->getToken()->getUser();

        if ($service->hasPendingOTP($user)) {
            return $this->error('You still have a pending token, please log out and start again');
        } else {
            $service->sendOTP($user);
        }

        return $this->redirect($this->generateUrl($redirectAfter));
    }

    /**
     * @Route("/link-token/sms/verification", name="self_service_link_sms_verify")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:smsAuthentication.html.twig")
     *
     * @return array
     */
    public function smsAuthenticationAction()
    {
        $service = $this->get('suaas.service.mollie');
        $user = $this->get('security.context')->getToken()->getUser();
        $token = $service->findTokenForUser($user);

        if (!$service->hasPendingOTP($user)) {
            throw new BadRequestHttpException(
                "Invalid request - no pending sms OTP."
            );
        }

        $form = $this->createForm(
            new VerifyMollieTokenType(),
            new VerifyMollieTokenCommand()
        );

        $form->handleRequest($this->getRequest());
        if ($form->isValid()) {
            if ($service->verifyToken($token, $form->getData())) {
                return $this->redirect($this->generateUrl('self_service_confirm'));
            }

            $form->get('password')->addError(new FormError('Invalid Password'));
        }

        return array(
            'user' => $user,
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
            return $this->error('The token already has a registration code attached');
        }

        $mail = $service->createRegistrationMail($user, $token);
        // quick hack
        $code = $mail->parameters['code'];
        $this->get('suaas.mailer')->sendMail($mail);

        return array(
            'ras' => $this->get('suaas.service.user')->findRAByOrganisation($user->getOrganisation()),
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
            return $this->error('The registration URL you tried to use is invalid');
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

    private function error($message)
    {
        $this->get('session')->set('error_message', $message);
        return $this->redirect($this->generateUrl('error'));
    }
}
