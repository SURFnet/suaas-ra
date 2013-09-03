<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Entity\Mollie;
use SURFnet\SuAAS\DomainBundle\Entity\User;

class MollieService extends AuthenticationMethodService
{
    public function createMollieToken(CreateMollieCommand $command)
    {
        $authMethod = new Mollie();
        $authMethod->create(
            $command->phoneNumber,
            $command->user
        );

        $this->persist($authMethod)->flush();

        return $authMethod;
    }

    public function findTokenForUser(User $user)
    {
        return $this->findTokenOfTypeForUser('Mollie', $user);
    }
}
