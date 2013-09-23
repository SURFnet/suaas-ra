<?php

namespace SURFnet\SuAAS\SelfServiceBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\CreateYubikeyCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand;
use SURFnet\SuAAS\DomainBundle\Entity\Mollie;
use SURFnet\SuAAS\DomainBundle\Entity\YubiKey;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateYubikeyType;
use SURFnet\SuAAS\SelfServiceBundle\Form\Type\VerifyMollieTokenType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

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
     * Step 1. Select A Token
     *
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
            'user' => $user->getView(),
            'tokenWarning' => $hasToken
        );
    }

    /**
     * Step 2. (SMS) Instruction page, enter your phonenumber
     *
     * @Route("/link-token/sms/instruction", name="self_service_link_sms_instr")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:smsInstruction.html.twig")
     *
     * @return array
     */
    public function linkSMSInstructionAction()
    {
        /** @var \SURFnet\SuAAS\SelfServiceBundle\Form\Type\CreateMollieType $form */
        /** @var \SURFnet\SuAAS\DomainBundle\Service\MollieService $service */
        $form = $this->createForm(new CreateMollieType(), new CreateMollieCommand());
        $user = $this->get('security.context')->getToken()->getUser();

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $command = $form->getData();
            $command->user = $user;

            $this->get('suaas.service.mollie')->createMollieToken($command);

            return $this->redirect($this->generateUrl('self_service_link_sms_verify'));
        }

        return array(
            'user' => $user->getView(),
            'tokenType' => (new Mollie())->getType(),
            'tokenExtended' => 'an SMS based one-time-password',
            'form' => $form->createView(),
        );
    }

    /**
     * Step 2. (SMS) Verification page, enter the SMS-code
     *
     * @Route("/link-token/sms/verification", name="self_service_link_sms_verify")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:smsAuthentication.html.twig")
     *
     * @return array
     * @throws BadRequestHttpException
     */
    public function smsAuthenticationAction()
    {
        $service = $this->get('suaas.service.mollie');
        $user = $this->get('security.context')->getToken()->getUser();
        $token = $service->findTokenForUser($user);

        // If you don't have a pending OTP, you're getting here through bookmark
        // or something, rather than redirect - WRONG
        if (!$service->hasPendingOTP($token)) {
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
     * Step 2. (Yubikey) OTP page
     *
     * @Route("/link-token/yubikey/instruction", name="self_service_link_yubikey_instr")
     * @Template("SURFnetSuAASSelfServiceBundle:Wizard:yubikeyInstruction.html.twig")
     *
     * @return array
     */
    public function linkYubikeyAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $form = $this->createForm(
            new CreateYubikeyType(),
            new CreateYubikeyCommand(array('owner' => $user))
        );

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            if ($this->get('suaas.service.yubikey')->createToken($form->getData())) {
                return $this->redirect($this->generateUrl('self_service_confirm'));
            }

            // should be done in a custom validator
            $form->get('otp')->addError(new FormError('Invalid password, please try again'));
        }

        return array(
            'user' => $user->getView(),
            'tokenType' => (new YubiKey())->getType(),
            'tokenExtended' => 'a Yubikey',
            'form' => $form->createView(),
        );
    }

    /**
     * Step 3. Confirm token -> send activation email
     *
     * @Route("/confirm-token", name="self_service_confirm")
     * @Template()
     *
     * @return array
     */
    public function confirmAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        $service = $this->get('suaas.service.authentication_method');
        $token = $service->findTokenForUser($user);

        $mail = $service->createActivationEmail($user, $token);
        $this->get('suaas.mailer')->sendMail($mail);

        return array(
            'user' => $user,
            'token' => $token->getView()
        );
    }

    /**
     * Step 4. Email activation confirmation
     * Supports redirecting towards SAML for non-authenticated users
     *
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

        try {
            $user = $this->get('suaas.service.user')->loadUserByUsername($userHash);
        } catch (UsernameNotFoundException $e) {
            throw new BadRequestHttpException("Invalid Request", $e);
        }

        $service = $this->get('suaas.service.authentication_method');
        if (!$service->confirmRegistration($user, $registrationCode)) {
            return $this->error('The registration URL you tried to use is invalid');
        }

        return $this->redirect($this->generateUrl('self_service_registration_instruction'));
    }

    /**
     * step 4. Registration confirmed, send email.
     *
     * @Route("/registration-instruction", name="self_service_registration_instruction")
     * @Method("GET")
     * @Template()
     *
     * @return array
     */
    public function registerTokenAction()
    {
        $service = $this->get('suaas.service.authentication_method');
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
            'user' => $user->getView(),
            'code' => $code,
            'token' => $token->getView()
        );
    }

    /**
     * [!!!] Solely for Pilot
     * Drop all tokens for the currently logged in user
     *
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

    /**
     * Shortcut for redirecting towards the error-action
     *
     * @param string $message
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    private function error($message)
    {
        $this->get('session')->set('error_message', $message);
        return $this->redirect($this->generateUrl('error'));
    }
}
