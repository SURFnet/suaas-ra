<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class SchacHomeOrganisation extends AbstractAttribute
{
    const NAME = 'homeOrganisation';

    public function getUrnMace()
    {
        return 'urn:mace:terena.org:attribute-def:schacHomeOrganization';
    }

    public function getUrnOid()
    {
        return 'urn:oid:1.3.6.1.4.1.25178.1.2.9';
    }

    public function getMultiplicity()
    {
        return self::SINGLE;
    }
}
