<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

/**
 * Base class, enforcing the definition of the NAME constant in child classes
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
abstract class AbstractAttribute implements AttributeInterface
{
    /**
     * @var string the urn:mace identifier of this attribute
     */
    protected $urnMace;

    /**
     * @var string the urn:oid identifier of this attribute
     */
    protected $urnOid;

    /**
     * @var int the multiplicity of this attribute
     */
    protected $multiplicity;

    /**
     * Enforces the existence of the NAME constant
     *
     * @return mixed
     * @throws \DomainException
     */
    public function getName()
    {
        $class = get_class($this);
        if (!defined($class . '::NAME')) {
            throw new \DomainException(sprintf(
                'Class "%s" must have defined the constant NAME',
                get_class($this)
            ));
        }

        return $class::NAME;
    }

    /**
     * Enforce that urnMace contains a string value
     *
     * @return string
     * @throws \DomainException
     */
    public function getUrnMace()
    {
        if (!isset($this->urnMace) || !is_string($this->urnOid)) {
            throw new \DomainException(sprintf(
                'Class "%s" must have the property "%s" defined.',
                get_class($this),
                'urnMace'
            ));
        }

        return $this->urnMace;
    }

    /**
     * Enforces that the urnOid is set and contains a string
     *
     * @return string
     * @throws \DomainException
     */
    public function getUrnOid()
    {
        if (!isset($this->urnOid) || !is_string($this->urnOid)) {
            throw new \DomainException(sprintf(
                'Class "%s" must have the property "%s" defined as string.',
                get_class($this),
                'urnMace'
            ));
        }

        return $this->urnOid;
    }

    /**
     * Enforces that the multiplicity is set and contains a valid value
     *
     * @return int
     * @throws \DomainException
     */
    public function getMultiplicity()
    {
        if (!isset($this->multiplicity)
            || !in_array($this->multiplicity, array(self::SINGLE, self::MULTIPLE))
        ) {
            throw new \DomainException(sprintf(
                'Class "%s" must have the property "%s" defined as either "%s".',
                get_class($this),
                'urnMace',
                implode('" or "', array(self::SINGLE, self::MULTIPLE))
            ));
        }

        return $this->multiplicity;
    }

}
