<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class DisplayName extends AbstractAttribute
{
    const NAME = 'displayName';

    public function getUrnMace()
    {
        return 'urn:mace:dir:attribute-def:displayName';
    }

    public function getUrnOid()
    {
        return 'urn:oid:2.16.840.1.113730.3.1.241';
    }

    public function getMultiplicity()
    {
        return self::SINGLE;
    }
}
