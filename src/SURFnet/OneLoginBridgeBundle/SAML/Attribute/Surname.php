<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

class Surname extends AbstractAttribute
{
    const NAME = 'sn';

    protected $urnMace = 'urn:mace:dir:attribute-def:sn';
    protected $urnOid = 'urn:oid:2.5.4.4';
    protected $multiplicity = self::SINGLE;
}
