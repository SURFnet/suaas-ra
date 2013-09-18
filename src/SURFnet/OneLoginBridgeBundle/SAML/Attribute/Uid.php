<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

/**
 * Class Uid
 * @package SURFnet\OneLoginBridgeBundle\SAML\Attribute
 *
 * Represents the UID SAML Attribute
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Uid extends AbstractAttribute
{
    const NAME = 'uid';

    protected $urnMace = 'urn:mace:dir:attribute-def:uid';
    protected $urnOid = 'urn:oid:0.9.2342.19200300.100.1.1';
    protected $multiplicity = self::SINGLE;
}
