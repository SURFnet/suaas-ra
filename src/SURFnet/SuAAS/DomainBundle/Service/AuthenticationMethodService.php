<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Entity\User;

class AuthenticationMethodService extends ORMService
{
    protected $rootEntityClass = 'SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethod';

    protected function findTokenOfTypeForUser($type, User $user)
    {
        return $this->getRepository()->getTokenOfTypeForUser($type, $user);
    }
}
