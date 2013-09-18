<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

/**
 * Class SAMLIdentity
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * Simple object that represents the identity a User has based on the SAML authn
 * request made. Once the identity has been build it must remain immutable.
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SAMLIdentity implements \Serializable
{
    /**
     * @var string the unique identifier of this entity, as dictated by the IdP
     */
    private $nameId;

    /**
     * @var string
     * @deprecated
     */
    private $uid;

    /**
     * @var string the identifier of the organisation the entity belongs to
     */
    private $schacHomeOrganisation;

    /**
     * @var string|null the name to show to the user
     */
    private $displayName;

    /**
     * @var string|null email
     */
    private $email;

    /**
     * @var string|null the given name of the entity
     */
    private $givenName;

    /**
     * @var string|null the surname of the entity
     */
    private $surname;

    public function __construct(
        $nameId,
        $schacHomeOrgansation,
        $displayName = null,
        $email = null,
        $givenName = null,
        $surname = null
    ) {
        $this->nameId = $nameId;
        $this->schacHomeOrganisation = $schacHomeOrgansation;
        $this->displayName = $displayName;
        $this->email = $email;
        $this->givenName = $givenName;
        $this->surname = $surname;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getNameId()
    {
        return $this->nameId;
    }

    /**
     * Getter
     *
     * @return string
     */
    public function getSchacHomeOrganisation()
    {
        return $this->schacHomeOrganisation;
    }

    /**
     * Getter
     *
     * @return null|string
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * Getter
     *
     * @return null|string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Getter
     *
     * @return null|string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Getter
     *
     * @return null|string
     */
    public function getGivenName()
    {
        return $this->givenName;
    }

    public function serialize()
    {
        return serialize(
            array(
                $this->nameId,
                $this->schacHomeOrganisation,
                $this->displayName,
                $this->email,
                $this->givenName,
                $this->surname
            )
        );
    }

    public function unserialize($serialized)
    {
       list(
           $this->nameId,
           $this->schacHomeOrganisation,
           $this->displayName,
           $this->email,
           $this->givenName,
           $this->surname
       ) = unserialize($serialized);
    }
}
