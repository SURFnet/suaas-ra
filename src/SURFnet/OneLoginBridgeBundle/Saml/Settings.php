<?php

namespace SURFnet\OneLoginBridgeBundle\Saml;

use \OneLogin_Saml_Settings as BaseSettings;

/**
 * Class Settings
 * @package SURFnet\OneLoginBridgeBundle\Service
 *
 * Extension of the OneLogin_Saml_Settings class that allows for injection of
 * the configuration.
 *
 * @author Daan van Renterghem <dvrenterghem@gmail.com>
 */
class Settings extends BaseSettings
{
    /**
     * Constructor
     *
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->idpSingleSignOnUrl = $configuration->getTargetUrl();
        $this->idpPublicCertificate = $configuration->getCertificate();
        $this->requestedNameIdFormat = $configuration->getNameIdentifierFormat();
        $this->spIssuer = $configuration->getIssuerName();
        $this->spReturnUrl = $configuration->getConsumerUrl();
    }

    /**
     * Used in the {@see SURFnet\OneLoginBridgeBundle\DependencyInjection\Configuration}
     * for the validation of the sur_fnet_one_login_bundle.saml_settings.name_id_format
     * parameter.
     *
     * @return array
     */
    public static function getValidNameIdFormats()
    {
        return array(
            self::NAMEID_EMAIL_ADDRESS,
            self::NAMEID_ENTITY,
            self::NAMEID_KERBEROS,
            self::NAMEID_PERSISTENT,
            self::NAMEID_TRANSIENT,
            self::NAMEID_WINDOWS_DOMAIN_QUALIFIED_NAME,
            self::NAMEID_X509_SUBJECT_NAME
        );
    }
}
