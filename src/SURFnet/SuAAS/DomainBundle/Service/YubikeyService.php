<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\SuAAS\DomainBundle\Command\CreateYubikeyCommand;
use SURFnet\SuAAS\DomainBundle\Command\VerifyYubikeyCommand;
use SURFnet\SuAAS\DomainBundle\Entity\YubiKey;
use Yubico\YubikeyBundle\Service\ApiService;

class YubikeyService extends AuthenticationMethodService
{
    /**
     * @var ApiService
     */
    private $yubiService;

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

    public function setApiService(ApiService $service)
    {
        $this->yubiService = $service;
    }

    private function isValidOTP($otp)
    {
        return $this->yubiService->verify($otp);
    }
}
