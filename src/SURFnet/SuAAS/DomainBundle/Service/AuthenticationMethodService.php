<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\VerifyIdentityCommand;
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

    public function approveToken(AuthenticationMethod $token, VerifyIdentityCommand $command)
    {
        if (!$command->verified || !$token->canConfirmIdentity()) {
            throw new \LogicException("Cannot approve token that has not been verified");
        }

        $token->approve($command->approvedBy);

        $this->persist($token)->flush();
    }

    public function declineToken(AuthenticationMethod $token)
    {
        if (!$token->canConfirmIdentity()) {
            throw new \LogicException("Cannot decline token that has not been verified");
        }

        $token->decline();

        $this->persist($token)->flush();
    }

    public function getTokensToVet(User $user)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $tokens */
        $tokens = $this->getRepository()->findUnvettedTokens($user->getOrganisation());

        return $tokens->map(function ($token) {
            return $token->getView();
        });
    }

    public function getApprovedTokens(User $user)
    {
        /** @var \Doctrine\Common\Collections\ArrayCollection $tokens */
        $tokens = $this->getRepository()->findVettedTokens($user->getOrganisation());

        return $tokens->map(function ($token) {
            return $token->getView();
        });
    }

    public function remove(AuthenticationMethod $token)
    {
        $em = $this->doctrine->getManager();
        $em->remove($token);
        $em->flush();
    }
}
