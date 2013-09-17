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
        return array(
            'items' => $this->get('suaas.service.authentication_method')->getTokensToVet()
        );
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
     * @Route("/registration/code/{token}", name="management_registration_code")
     * @Template()
     */
    public function registrationCodeAction(AuthenticationMethod $token)
    {
        $service = $this->get('suaas.service.authentication_method');

        $form = $this->createForm(
            new VerifyRegistrationCodeType(),
            new VerifyRegistrationCodeCommand()
        );

        $form->handleRequest($this->getRequest());

        if ($form->isValid()) {/*} && $service->verifyRegistrationCode()) {*/

        }

        return array(
            'form' => $form->createView()
        );
    }
}
