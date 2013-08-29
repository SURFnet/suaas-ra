<?php

namespace SURFnet\OneLoginBridgeBundle\Service;

use OneLogin_Saml_Response as SamlResponse;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\AttributeInterface;
use SURFnet\OneLoginBridgeBundle\SAML\Attributes;
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
     * @var Attributes
     */
    private $samlAttributes;

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
     * @param Settings   $settings
     * @param Attributes $samlAttributes
     */
    public function __construct(Settings $settings, Attributes $samlAttributes)
    {
        $this->settings = $settings;
        $this->samlAttributes = $samlAttributes;
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
     *
     * @throws \RangeException
     */
    public function getAttribute($name, $default = false)
    {
        $samlAttribute = $this->samlAttributes->getAttribute($name);
        $attributes = $this->getResponseAttributes();

        // @todo should be resolved internally, custom ParameterBag
        // try first by urn:mace, then by urn:oid
        if ($attributes->has($samlAttribute->getUrnMace())) {
            $attribute = $attributes->get($samlAttribute->getUrnMace());
        } elseif ($attributes->has($samlAttribute->getUrnOid())){
            $attribute = $attributes->get($samlAttribute->getUrnOid());
        } else {
            return $default;
        }

        if ($samlAttribute->getMultiplicity() === AttributeInterface::SINGLE) {
            $count = count($attribute);
            if ($count > 1) {
                throw new \RangeException(sprintf(
                    'Attribute "%s" has a single-value multiplicity, yet returned'
                    . ' "%d" values',
                    $samlAttribute->getName(),
                    count($attribute)
                ));
            } elseif ($count === 0) {
                $attribute = null;
            } else {
                $attribute = reset($attribute);
            }
        }

        return $attribute;
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
