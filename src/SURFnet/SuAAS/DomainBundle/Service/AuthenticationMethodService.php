<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod;
use SURFnet\SuAAS\DomainBundle\Entity\User;

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
}
