<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\VerifyRegistrationCodeCommand;
use SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod;
use SURFnet\SuAAS\DomainBundle\Entity\User;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormError;

class AuthenticationMethodService extends ORMService
{
    protected $rootEntityClass = 'SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod';

    protected function findTokenOfTypeForUser($type, User $user)
    {
        return $this->getRepository()->getTokenOfTypeForUser($type, $user);
    }

    public function removeTokensForUser(User $user)
    {
        return $this->getRepository()->removeForUser($user);
    }

    public function hasToken(User $user)
    {
        return (bool) $this->getRepository()->getTokenCountForUser($user);
    }

    public function confirmRegistration(User $user, $registrationCode)
    {
        $token = $this->getRepository()->findByEmailCode($registrationCode);

        if ($token === null) {
            return false;
        }

        if (!$token->isUserOwner($user)) {
            return false;
        }

        return true;
    }

    public function verifyRegistrationCode(AuthenticationMethod $token, Form $form)
    {
        if (!$token->matchRegistrationCode($form->getData())) {
            $form->get('code')->addError(new FormError('Invalid Code Entered'));
            return false;
        }

        $this->persist($token)->flush();

        return true;
    }

    public function getTokensToVet(User $user)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $tokens */
        $tokens = $this->getRepository()->findUnvettedTokens($user->getOrganisation());

        return $tokens->map(function ($token) {
            return $token->getRegistrationView();
        });
    }
}
