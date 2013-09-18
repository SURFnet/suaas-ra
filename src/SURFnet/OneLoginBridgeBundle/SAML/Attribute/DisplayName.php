<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

/**
 * Class DisplayName
 * @package SURFnet\OneLoginBridgeBundle\SAML\Attribute
 *
 * Represents the displayName SAML attribute
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class DisplayName extends AbstractAttribute
{
    const NAME = 'displayName';

    protected $urnMace = 'urn:mace:dir:attribute-def:displayName';
    protected $urnOid = 'urn:oid:2.16.840.1.113730.3.1.241';
    protected $multiplicity = self::SINGLE;
}
