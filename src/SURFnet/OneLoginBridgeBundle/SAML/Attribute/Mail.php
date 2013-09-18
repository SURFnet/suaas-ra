<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

/**
 * Class Mail
 * @package SURFnet\OneLoginBridgeBundle\SAML\Attribute
 *
 * Represents the mail SAML attribute
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Mail extends AbstractAttribute
{
    const NAME = 'mail';

    protected $urnMace = 'urn:mace:dir:attribute-def:mail';
    protected $urnOid = 'urn:oid:0.9.2342.19200300.100.1.3';
    protected $multiplicity = self::SINGLE;
}
