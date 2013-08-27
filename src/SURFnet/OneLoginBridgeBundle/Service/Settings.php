<?php

namespace SURFnet\OneLoginBridgeBundle\Service;

use \OneLogin_Saml_Settings as BaseSettings;

class Settings extends BaseSettings
{
    public function __construct(Configuration $configuration)
    {
        $this->idpSingleSignOnUrl = $configuration->getTargetUrl();
        $this->idpPublicCertificate = $configuration->getCertificate();
        $this->requestedNameIdFormat = $configuration->getNameIdentifierFormat();
        $this->spIssuer = $configuration->getIssuerName();
        $this->spReturnUrl = $configuration->getConsumerUrl();
    }
}
