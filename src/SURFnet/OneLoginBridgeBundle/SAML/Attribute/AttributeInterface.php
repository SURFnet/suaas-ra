<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

interface AttributeInterface
{
    const SINGLE = 1;
    const MULTIPLE = 2;

    public function getName();
    public function getUrnMace();
    public function getUrnOid();
    public function getMultiplicity();
}
