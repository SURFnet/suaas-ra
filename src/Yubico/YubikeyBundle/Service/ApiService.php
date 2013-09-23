<?php

namespace Yubico\YubikeyBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Yubico\YubikeyBundle\Entity\Response;

/**
 * Class ApiService
 * @package Yubico\YubikeyBundle\Service
 *
 * Yubikey API client
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class ApiService
{
    /**
     * API url
     */
    const URL = 'http://api2.yubico.com/wsapi/2.0/verify';

    /**
     * @var \Guzzle\Http\Client
     */
    private $client;

    /**
     * @var string Yubico client Id
     */
    private $clientId;

    /**
     * @var string Yubico secret
     */
    private $secret;

    /**
     * @param Client $client
     * @param string $clientId
     * @param string $secret
     */
    public function __construct(Client $client, $clientId, $secret)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->secret = base64_decode($secret);
    }

    /**
     * Verifies a Yubikey One Time Password. Currently the secret verification
     * is skipped since the signature algorithm given does not work (not in
     * the yubico reference implementation and not in here)
     *
     * @param  string $otp the full OTP to verify
     * @return bool
     */
    public function verify($otp)
    {
        if (!$this->isValidString($otp)) {
            return false;
        }

        $request = $this->client->get(self::URL);
        $nonce = $this->generateNonce($otp);

        $request
            ->getQuery()
            ->set('id', $this->clientId)
            ->set('otp', $otp)
            ->set('nonce', $nonce);
//            ->set('h', Response::hash($foo, $this->secret));

        $response = $request->send();

        return $this->isValid($response, $otp, $nonce);
    }

    /**
     * Validates the OTP against the API
     *
     * @param GuzzleResponse $apiResponse
     * @param string         $otp
     * @param string         $nonce
     * @return bool
     */
    private function isValid(GuzzleResponse $apiResponse, $otp, $nonce)
    {
        $response = Response::parse($apiResponse->getBody(true));

        if (!$response->isValid($otp, $nonce, $this->secret)) {
            if ($response->isReplay()) {
                // log & handle internally, replay means previously validated OTP
                // is being reused (attack detected)
                return false;
            }

            // invalid OTP
            // log and show message
            return false;
        }

        return true;
    }

    /**
     * Checks if the OTP given matchs the criteria of a possible OTP as
     * specified by Yubikey
     *
     * @param string $otp
     * @return bool
     */
    private function isValidString($otp)
    {
        if (!is_string($otp)) {
            return false;
        }

        $length = strlen($otp);
        if (!$length >= 32 && $length <= 48) {
            return false;
        }

        if (!preg_match('~^[cbdefghijklnrtuv]{32,48}$~i', $otp)) {
            return false;
        }

        return true;
    }

    /**
     * Resolves the yubikey ID from the OTP given
     *
     * @param string $otp
     * @return string
     */
    private function resolveKeyId($otp)
    {
        return substr($otp, 0, -32);
    }

    /**
     * Simple nonce generator, main purpose is to minimize collision risk
     *
     * @param string $otp
     * @return string
     */
    private function generateNonce($otp)
    {
        return md5($this->resolveKeyId($otp) . microtime(true));
    }
}
