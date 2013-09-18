<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use Mollie\SMSBundle\SMS\Message;
use Mollie\SMSBundle\SMS\Service;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieOTPCommand;
use SURFnet\SuAAS\DomainBundle\Command\Mail\SendConfirmationCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand;
use SURFnet\SuAAS\DomainBundle\Entity\Mollie;
use SURFnet\SuAAS\DomainBundle\Entity\MollieOTP;
use SURFnet\SuAAS\DomainBundle\Entity\User;

class MollieService extends AuthenticationMethodService
{
    /**
     * @var Service
     */
    private $smsService;

    public function createMollieToken(CreateMollieCommand $command)
    {
        $token = new Mollie();
        $token->create(
            $command->phoneNumber,
            $command->user
        );

        $this->persist($token)->flush();

        $this->sendOTP($token);

        return $token;
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
        $userView = $user->getView();

        $command = new SendConfirmationCommand();
        $command->recepient = $userView->email;

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
        $userView = $user->getView();

        $command = new SendConfirmationCommand();
        $command->recepient = $userView->email;
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

    public function hasPendingOTP(Mollie $token)
    {
        return $this->getRepository()->hasPendingMollieOTP($token);
    }

    public function verifyToken(Mollie $token, VerifyMollieTokenCommand $command)
    {
        $otp = $this->getRepository()->findMollieOTP($token, $command->password);

        if ($otp === null) {
            return false;
        }

        $otp->confirm();

        $this->persist($otp)->flush();

        return true;
    }

    public function confirmToken(Mollie $token, VerifyMollieTokenCommand $command)
    {
        if (!$this->verifyToken($token, $command)) {
            return false;
        }

        $token->confirm();

        $this->persist($token)->flush();

        return true;
    }

    public function setSmsService(Service $service)
    {
        $this->smsService = $service;
    }

    public function sendOTP(Mollie $token)
    {
        $command = new CreateMollieOTPCommand(array('token' => $token));
        $otp = new MollieOTP();
        $otp->create($command);

        $this->persist($otp)->flush();
        $this->sendSMS($command);

        return $command;
    }

    private function sendSMS(CreateMollieOTPCommand $command)
    {
        $message = new Message();
        $message->recipient = $command->token->getPhoneNumber();
        $message->body = (string) $command->otp;

        if (!$this->smsService->send($message)) {
            throw new \RuntimeException(
                "Could not send SMS through Mollie. Something seems to have gone"
                . " wrong on their end."
            );
        }
    }
}
