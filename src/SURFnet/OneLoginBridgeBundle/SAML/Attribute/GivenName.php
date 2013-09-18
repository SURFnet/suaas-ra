<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class GivenName extends AbstractAttribute
{
    const NAME = 'givenName';

    protected $urnMace = 'urn:mace:dir:attribute-def:givenName';
    protected $urnOid = 'urn:oid:2.5.4.42';
    protected $multiplicity = self::SINGLE;
}
