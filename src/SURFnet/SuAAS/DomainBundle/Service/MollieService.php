<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use Mollie\SMSBundle\SMS\Message;
use Mollie\SMSBundle\SMS\Service;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieCommand;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieOTPCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyMollieTokenCommand;
use SURFnet\SuAAS\DomainBundle\Entity\Mollie;
use SURFnet\SuAAS\DomainBundle\Entity\MollieOTP;

/**
 * Class MollieService
 * @package SURFnet\SuAAS\DomainBundle\Service
 *
 * Mollie Service
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class MollieService extends AuthenticationMethodService
{
    /**
     * @var Service
     */
    private $smsService;

    /**
     * Creates a new Mollie token
     *
     * @param CreateMollieCommand $command
     * @return Mollie
     */
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
     * Test if the token has a pending OTP (SMS sent, code not yet used)
     *
     * @param Mollie $token
     * @return bool
     */
    public function hasPendingOTP(Mollie $token)
    {
        return $this->getRepository()->hasPendingMollieOTP($token);
    }

    /**
     * Verify the mollie token
     *
     * @param Mollie                   $token
     * @param VerifyMollieTokenCommand $command
     * @return bool
     */
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

    /**
     * Confirm a token
     *
     * @param Mollie                   $token
     * @param VerifyMollieTokenCommand $command
     * @return bool
     */
    public function confirmToken(Mollie $token, VerifyMollieTokenCommand $command)
    {
        if (!$this->verifyToken($token, $command)) {
            return false;
        }

        $token->confirm();

        $this->persist($token)->flush();

        return true;
    }

    /**
     * Setter used by the service container
     *
     * @param Service $service
     */
    public function setSmsService(Service $service)
    {
        $this->smsService = $service;
    }

    /**
     * Send a new OTP for the token given
     *
     * @param Mollie $token
     * @return CreateMollieOTPCommand
     */
    public function sendOTP(Mollie $token)
    {
        $command = new CreateMollieOTPCommand(array('token' => $token));
        $otp = new MollieOTP();
        $otp->create($command);

        $this->persist($otp)->flush();
        $this->sendSMS($command);

        return $command;
    }

    /**
     * Helper method for sending of an SMS
     *
     * @param CreateMollieOTPCommand $command
     * @throws \RuntimeException
     */
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
