<?php

namespace SURFnet\OneLoginBridgeBundle\Service;

use OneLogin_Saml_Response as SamlResponse;
use SURFnet\OneLoginBridgeBundle\SAML\Settings;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * Class ResponseAdapter
 * @package SURFnet\OneLoginBridgeBundle\Service
 *
 * Adapter for the OneLogin_Saml_Response.
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class ResponseAdapter
{
    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var \OneLogin_Saml_Response
     */
    private $samlResponse;

    /**
     * @var ParameterBag
     */
    private $samlResponseAttributes;

    /**
     * Constructor
     *
     * @param Settings $settings
     */
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Sets the SAML response
     *
     * @param string $response
     * @return ResponseAdapter
     */
    public function setSAMLResponse($response)
    {
        $this->samlResponse = new SamlResponse(
            $this->settings,
            $response
        );

        return $this;
    }

    /**
     * Whether or not the response is valid (using the certificate).
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->getSamlResponse()->isValid();
    }

    /**
     * Get the NameID embedded in the response
     *
     * @return string
     */
    public function getNameId()
    {
        return $this->getSamlResponse()->getNameId();
    }

    /**
     * Get the expiration date-time for the user session as dictated by the IdP
     *
     * @return \DateTime
     */
    public function getSessionExpirationDate()
    {
        $epoch = $this->getSamlResponse()->getSessionNotOnOrAfter();

        // keep consistent interface
        if ($epoch === null) {
            return null;
        }

        // till we now for sure we can run 5.4 on remote servers, we are 5.3
        // compatible!
        $dateTime = new \DateTime();
        return $dateTime->setTimestamp($epoch);
    }

    /**
     * Get a specific attribute from the Saml Response
     *
     * @param string $name    name of the attribute to get
     * @param mixed  $default default value to return if the attribute does not exist
     *
     * @return mixed
     */
    public function getAttribute($name, $default = false)
    {
        return $this->getResponseAttributes()->get($name, $default);
    }

    /**
     * Internal getter for the OneLogin_Saml_Response object
     *
     * @return SamlResponse
     *
     * @throws \LogicException
     */
    private function getSamlResponse()
    {
        if (!isset($this->samlResponse)) {
            // @todo create named exception
            throw new \LogicException(
                'Cannot retrieve response message, it has not been set yet.'
            );
        }

        return $this->samlResponse;
    }

    /**
     * Internal getter for all the attributes. Uses internal cache to prevent
     * expensive reparsing of the XML-structured saml response
     *
     * @return ParameterBag
     */
    private function getResponseAttributes()
    {
        if (isset($this->samlResponseAttributes)) {
            return $this->samlResponseAttributes;
        }

        return $this->samlResponseAttributes = new ParameterBag(
            $this->getSamlResponse()->getAttributes()
        );
    }
}
