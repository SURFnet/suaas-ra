<?php

namespace SURFnet\OneLoginBridgeBundle\Service;

use OneLogin_Saml_Response as SamlResponse;
use SURFnet\OneLoginBridgeBundle\Saml\Settings;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private $request;

    /**
     * @var Settings
     */
    private $settings;

    /**
     * @var \OneLogin_Saml_Response
     */
    private $samlResponse;

    /**
     * @var array
     */
    private $samlResponseAttributes;

    /**
     * Constructor
     *
     * @param Settings $settings
     * @param Request $request
     */
    public function __construct(Settings $settings, Request $request)
    {
        $this->request = $request;
        $this->settings = $settings;
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
        $dateTime = new \DateTime();
        return $dateTime->setTimestamp(
            $this->getSamlResponse()->getSessionNotOnOrAfter()
        );
    }

    /**
     * Get all the attributes from the Saml Response
     *
     * @return array
     */
    public function getAttributes()
    {
        return $this->getResponseAttributes();
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
        $attributes = $this->getResponseAttributes();

        if (!array_key_exists($name, $attributes)) {
            return $default;
        }

        return $attributes[$name];
    }

    /**
     * Internal getter for the OneLogin_Saml_Response object
     *
     * @return SamlResponse
     *
     * @throws BadRequestHttpException
     */
    private function getSamlResponse()
    {
        if (isset($this->samlResponse)) {
            return $this->samlResponse;
        }

        $samlResponseBody = $this->request->request->get('SAMLResponse', false);
        if ($samlResponseBody === false) {
            throw new BadRequestHttpException(
                'No SAMLResponse in the request.'
            );
        }

        return $this->samlResponse = new SamlResponse(
            $this->settings,
            $samlResponseBody
        );
    }

    /**
     * Internal getter for all the attributes. Uses internal cache to prevent
     * expensive reparsing of the XML-structured saml response
     *
     * @return array
     */
    private function getResponseAttributes()
    {
        if (isset($this->samlResponseAttributes)) {
            return $this->samlResponseAttributes;
        }

        return $this->samlResponseAttributes = $this->getSamlResponse()->getAttributes();
    }
}
