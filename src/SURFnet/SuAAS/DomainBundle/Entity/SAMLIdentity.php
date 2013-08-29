<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

class SAMLIdentity
{
    private $nameId;
    private $uid;
    private $schacHomeOrganisation;
    private $displayName;
    private $email;

    public function __construct(
        $nameId,
        $schacHomeOrgansation,
        $displayName = null,
        $email = null
    ) {
        $this->nameId = $nameId;
        $this->schacHomeOrganisation = $schacHomeOrgansation;
        $this->displayName = $displayName;
        $this->email = $email;
    }

    public function getNameId()
    {
        return $this->nameId;
    }

    public function getSchacHomeOrganisation()
    {
        return $this->schacHomeOrganisation;
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getEmail()
    {
        return $this->email;
    }
}
