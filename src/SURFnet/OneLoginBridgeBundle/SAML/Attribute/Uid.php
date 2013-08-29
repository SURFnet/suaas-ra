<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class Uid extends AbstractAttribute
{
    const NAME = 'uid';

    public function getUrnMace()
    {
        return 'urn:mace:dir:attribute-def:uid';
    }

    public function getUrnOid()
    {
        return 'urn:oid:0.9.2342.19200300.100.1.1';
    }

    public function getMultiplicity()
    {
        return self::SINGLE;
    }
}
