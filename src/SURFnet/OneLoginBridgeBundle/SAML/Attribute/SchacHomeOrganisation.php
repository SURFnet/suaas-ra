<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

/**
 * Class SchacHomeOrganisation
 * @package SURFnet\OneLoginBridgeBundle\SAML\Attribute
 *
 * Represents the schacHomeOrganisation SAML attribute
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SchacHomeOrganisation extends AbstractAttribute
{
    const NAME = 'homeOrganisation';

    protected $urnMace = 'urn:mace:terena.org:attribute-def:schacHomeOrganization';
    protected $urnOid = 'urn:oid:1.3.6.1.4.1.25178.1.2.9';
    protected $multiplicity = self::SINGLE;
}
