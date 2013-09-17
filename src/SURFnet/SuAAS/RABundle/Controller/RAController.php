<?php

namespace SURFnet\SuAAS\RABundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use SURFnet\SuAAS\DomainBundle\Command\PromoteRACommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyRegistrationCodeCommand;
use SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod;
use SURFnet\SuAAS\DomainBundle\Entity\User;
use SURFnet\SuAAS\RABundle\Form\Type\CreateRAType;
use SURFnet\SuAAS\RABundle\Form\Type\VerifyRegistrationCodeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

/**
 * Class RAController
 * @package SURFnet\SuAAS\RABundle\Controller
 *
 * @Route("/management")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class RAController extends Controller
{
    /**
     * @Route("/registration", name="management_user_overview")
     * @Template()
     */
    public function registrationAction()
    {
        $currentRa = $this->get('security.context')->getToken()->getUser();
        $candidates = $this->get('suaas.service.authentication_method')->getTokensToVet($currentRa);

        return array('candidates' => $candidates);
    }

    /**
     * @Route("/ra/overview", name="management_ra_overview")
     * @Template()
     */
    public function raOverviewAction()
    {
        return array(
            'users' => $this->get('suaas.service.user')->findAll()
        );
    }

    /**
     * @Route("/ra/create/{user}", name="management_ra_create", requirements={"user":"\d+"})
     * @Template()
     */
    public function raCreateAction(User $user)
    {
        $form = $this->createForm(
            new CreateRAType(),
            new PromoteRACommand()
        );

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {
            $this->get('suaas.service.user')->promoteRa($user, $form->getData());

            return $this->redirect($this->generateUrl('management_ra_overview'));
        }

        return array(
            'user' => $user->getView(),
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/ra/revoke/{user}", name="management_ra_revoke", requirements={"user":"\d+"})
     * @Template()
     */
    public function raRevokeAction(User $user)
    {
        $this->get('suaas.service.user')->revokeRA($user);

        return $this->redirect($this->generateUrl('management_ra_overview'));
    }

    /**
     * @Route("/registration/confirm-code/{token}", name="management_registration_code")
     * @Template()
     */
    public function registrationCodeAction(AuthenticationMethod $token)
    {
        if (!$token->canVerifyRegistrationCode()) {
            return $this->error('Can not yet confirm the registration code of this token');
        }

        $service = $this->get('suaas.service.authentication_method');

        $form = $this->createForm(
            new VerifyRegistrationCodeType(),
            new VerifyRegistrationCodeCommand()
        );

        $form->handleRequest($this->getRequest());

        if ($form->isValid() && $service->verifyRegistrationCode($token, $form)) {
            return $this->redirect($this->generateUrl('management_confirm_token'));
        }

        return array(
            'form' => $form->createView(),
            'tokenType' => $token->getType()
        );
    }

    /**
     * @Route("/registration/confirm-token/{token}", name="management_confirm_token")
     * @Template()
     */
    public function confirmTokenAction(AuthenticationMethod $token)
    {
        if (!$token->canConfirmToken()) {
            return $this->error('This token cannot be confirmed yet.');
        }

        return array(
            'tokenType' => $token->getType()
        );
    }

    /**
     * @Route("/registration/confirm-identity/{token}", name="management_confirm_identity")
     * @Template()
     */
    public function confirmIdentityAction(AuthenticationMethod $token)
    {
        if (!$token->canConfirmIdentity()) {
            return $this->error('Can not yet confirm the identity of the owner of this token');
        }

        return array(
            'tokenType' => $token->getType()
        );
    }

    private function error($message)
    {
        $this->get('session')->set('error_message', $message);
        return $this->redirect($this->generateUrl('error'));
    }
}
