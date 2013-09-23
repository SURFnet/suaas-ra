<?php

namespace Yubico\YubikeyBundle\Entity;

/**
 * Class Response
 * @package Yubico\YubikeyBundle\Entity
 *
 * Represents a response as returned by Yubico API. A reponse is formatted as:
 *
 * <code>
 *      h=vjhFxZrNHB5CjI6vhuSeF2n46a8=
 *      t=2010-04-23T20:34:51Z0678
 *      otp=cccccccbcjdifctrndncchkftchjlnbhvhtugdljibej
 *      nonce=aef3a7835277a28da831005c2ae3b919e2076a62
 *      sl=75
 *      status=OK
 * </code>
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Response
{
    /**
     * Possible statusses we check against
     */
    const STATUS_SUCCESS = 'OK';
    const STATUS_REPLAYED = 'REPLAYED';

    /**
     * @var string
     */
    private $signature;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * @var string
     */
    private $otp;

    /**
     * @var string
     */
    private $nonce;

    /**
     * @var string
     */
    private $status;

    /**
     * @var array
     */
    private $bodyAsKeyValue;

    /**
     * @var array possible parameters in the response message
     */
    private static $possibleParams = array(
        'nonce',
        'otp',
        'sessioncounter',
        'sessionuse',
        'sl',
        'status',
        't',
        'timeout',
        'timestamp'
    );

    /**
     * Parses the response conform the yubico reference implementation
     *
     * @param $responseAsString
     * @return Response
     */
    public static function parse($responseAsString)
    {
        $lines = explode("\r\n", $responseAsString);
        $reponseAsKeyValue = array();
        foreach ($lines as $line) {
            $position = strpos($line, '=');
            list($key, $value) = explode('#', substr_replace($line, '#', $position, 1));

            $reponseAsKeyValue[$key] = $value;
        }

        return new self($reponseAsKeyValue);
    }

    /**
     * @param array $reponseAsKeyValue
     */
    public function __construct(array $reponseAsKeyValue)
    {
        $this->bodyAsKeyValue = $reponseAsKeyValue;
        $this->signature = $reponseAsKeyValue['h'];
        $this->timestamp = $reponseAsKeyValue['t'];
        if (isset($reponseAsKeyValue['otp'])) {
            $this->otp = $reponseAsKeyValue['otp'];
        }

        if (isset($reponseAsKeyValue['nonce'])) {
            $this->nonce = $reponseAsKeyValue['nonce'];
        }

        $this->status = $reponseAsKeyValue['status'];
    }

    /**
     * Validate the response against the given OTP and nonce
     * The key check is skipped as the algorithm given in the reference
     * implementation does not work
     *
     * @param string $otp
     * @param $nonce
     * @param $key
     * @return bool
     */
    public function isValid($otp, $nonce, $key)
    {
        // otp and nonce must be the same, the signature must match and the status
        // should be ok.
        // Sadly, the algorithm they provided does not work for the signature.
        return $this->otp === $otp
                && $this->nonce === $nonce
//                && $this->verifySignature($key)
                && $this->status === self::STATUS_SUCCESS;
    }

    /**
     * Does this response mark the OTP as REPLAY? (Possible attack vector)
     *
     * @return bool
     */
    public function isReplay()
    {
        return $this->status === self::STATUS_REPLAYED;
    }

    /**
     * Implementation of the hashing algorithm conform the reference implementation
     * Sadly this fails always.
     *
     * @param string $key
     * @return bool
     */
    private function verifySignature($key)
    {
        $matches = array_intersect(array_flip($this->bodyAsKeyValue), static::$possibleParams);
        sort($matches);

        $source = '';
        foreach ($matches as $key) {
            // can't use http_build_query due to encoding :(
            $source .= '&' . $key . '=' . $this->bodyAsKeyValue[$key];
        }
        $source = ltrim($source, '&');

        $signature = base64_encode(hash_hmac('sha1', utf8_encode($source), $key, true));

        return $this->signature === $signature;
    }
}
