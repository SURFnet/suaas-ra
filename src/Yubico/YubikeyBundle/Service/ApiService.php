<?php

namespace Yubico\YubikeyBundle\Service;

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response as GuzzleResponse;
use Yubico\YubikeyBundle\Entity\Response;

class ApiService
{
    const URL = 'http://api2.yubico.com/wsapi/2.0/verify';

    private $client;
    private $clientId;
    private $secret;

    public function __construct(Client $client, $clientId, $secret)
    {
        $this->client = $client;
        $this->clientId = $clientId;
        $this->secret = base64_decode($secret);
    }

    public function verify($otp)
    {
        if (!$this->isValidString($otp)) {
            return false;
        }

        $request = $this->client->get(self::URL);
        $nonce = $this->generateNonce($otp);
//
//        $foo = array('id' => $this->clientId, 'nonce' => $nonce, 'otp' => $otp);
//        ksort($foo);
//        var_dump(Response::hash($foo, $this->secret));
        $request
            ->getQuery()
            ->set('id', $this->clientId)
            ->set('otp', $otp)
            ->set('nonce', $nonce);
//            ->set('h', Response::hash($foo, $this->secret));

        $response = $request->send();

        return $this->isValid($response, $otp, $nonce);
    }

    private function isValid(GuzzleResponse $apiResponse, $otp, $nonce)
    {
        $response = Response::parse($apiResponse->getBody(true));

        if (!$response->isValid($otp, $nonce, $this->secret)) {
            if ($response->isReplay()) {
                // log & handle internally, replay means previously validated OTP
                // is being reused (attack detected)
                return false;
            }

            // log and show message
            return false;
        }

        return true;
    }

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

    private function resolveKeyId($otp)
    {
        return substr($otp, 0, -32);
    }

    private function generateNonce($otp)
    {
        return md5($this->resolveKeyId($otp) . microtime(true));
    }
}
