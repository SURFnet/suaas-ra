<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\Mail\SendConfirmationCommand;
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

    /**
     *
     *
     * @param User $user
     * @return Mollie
     */
    public function findTokenForUser(User $user)
    {
        return $this->findTokenOfTypeForUser('Mollie', $user);
    }

    public function createActivationEmail(User $user, Mollie $token)
    {
        $command = new SendConfirmationCommand();
        $command->recepient = $user->getEmail();

        $code = $token->generateEmailToken($command);
        $this->persist($token)->flush();

        $command->parameters = array(
            'user' => $user,
            'code' => $code,
            'token_description' => 'SMS One-Time-Password service'
        );

        $command->template = 'SURFnetSuAASSelfServiceBundle:Email:confirmationEmail.html.twig';
        $command->subject = 'Activate SMS One-Time-Password';

        return $command;
    }

    public function createRegistrationMail(User $user, Mollie $token)
    {
        $command = new SendConfirmationCommand();
        $command->recepient = $user->getEmail();

        $code = $token->generateRegistrationCode();
        $this->persist($token)->flush();

        $command->parameters = array(
            'user' => $user,
            'code' => $code,
            'token_description' => 'SMS One-Time-Password'
        );

        $command->template = 'SURFnetSuAASSelfServiceBundle:Email:registrationEmail.html.twig';
        $command->subject = 'Registration Code SMS One-Time-Password';

        return $command;
    }
}
