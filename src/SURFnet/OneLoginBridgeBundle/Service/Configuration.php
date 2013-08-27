<?php

namespace SURFnet\OneLoginBridgeBundle\Service;

/**
 * Class Configuration
 * @package SURFnet\OneLoginBridgeBundle\Service
 *
 * DTO for the container parameters, to be injected into the Settings.
 *
 * @author Daan van Renterghem <dvrenterghem@gmail.com>
 */
class Configuration
{
    /**
     * @var string
     */
    private $targetUrl;

    /**
     * @var string
     */
    private $consumerUrl;

    /**
     * @var string
     */
    private $issuerName;

    /**
     * @var string
     */
    private $nameIdentifierFormat;

    /**
     * @var string
     */
    private $certificate;

    public function __construct(
        $targetUrl,
        $consumerUrl,
        $issuerName,
        $nameIdentifierFormat,
        $certificate
    )
    {
        $this->targetUrl = $targetUrl;
        $this->consumerUrl = $consumerUrl;
        $this->issuerName = $issuerName;
        $this->nameIdentifierFormat = $nameIdentifierFormat;
        $this->certificate = $certificate;
    }

    /**
     * @return string
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @return string
     */
    public function getConsumerUrl()
    {
        return $this->consumerUrl;
    }

    /**
     * @return string
     */
    public function getIssuerName()
    {
        return $this->issuerName;
    }

    /**
     * @return string
     */
    public function getNameIdentifierFormat()
    {
        return $this->nameIdentifierFormat;
    }

    /**
     * @return string
     */
    public function getTargetUrl()
    {
        return $this->targetUrl;
    }
}
