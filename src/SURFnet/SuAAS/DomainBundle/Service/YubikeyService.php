<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\CreateYubikeyCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyYubikeyCommand;
use SURFnet\SuAAS\DomainBundle\Entity\YubiKey;
use Yubico\YubikeyBundle\Service\ApiService;

/**
 * Class YubikeyService
 * @package SURFnet\SuAAS\DomainBundle\Service
 *
 * Yubikey service
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class YubikeyService extends AuthenticationMethodService
{
    /**
     * @var ApiService
     */
    private $yubiService;

    /**
     * Create a new Yubikey Token
     *
     * @param CreateYubikeyCommand $command
     * @return bool
     */
    public function createToken(CreateYubikeyCommand $command)
    {
        if (!$this->isValidOTP($command->otp)) {
            return false;
        }

        $token = new YubiKey();
        $token->create($command);

        $this->persist($token)->flush();

        return true;
    }

    /**
     * Confirm a token
     *
     * @param YubiKey $token
     * @param VerifyYubikeyCommand $command
     * @return bool
     */
    public function confirmToken(YubiKey $token, VerifyYubikeyCommand $command)
    {
        if (!$token->yubikeyMatches($command)) {
            return false;
        }

        if (!$this->isValidOTP($command->otp)) {
            return false;
        }

        $token->confirm();

        $this->persist($token)->flush();

        return true;
    }

    /**
     * Setter used by the Service Container
     *
     * @param ApiService $service
     */
    public function setApiService(ApiService $service)
    {
        $this->yubiService = $service;
    }

    /**
     * Helper function, remnant of mocking functionality
     *
     * @param $otp
     * @return bool
     */
    private function isValidOTP($otp)
    {
        return $this->yubiService->verify($otp);
    }
}
