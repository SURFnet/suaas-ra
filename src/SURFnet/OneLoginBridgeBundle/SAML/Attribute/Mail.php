<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class Mail extends AbstractAttribute
{
    const NAME = 'mail';

    public function getUrnMace()
    {
        return 'urn:mace:dir:attribute-def:mail';
    }

    public function getUrnOid()
    {
        return 'urn:oid:0.9.2342.19200300.100.1.3';
    }

    public function getMultiplicity()
    {
        return self::SINGLE;
    }
}
