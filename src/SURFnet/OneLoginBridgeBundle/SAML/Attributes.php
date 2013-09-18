<?php

namespace SURFnet\OneLoginBridgeBundle\SAML;

use Doctrine\Common\Collections\ArrayCollection;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\AttributeInterface;

/**
 * Class Attributes
 * @package SURFnet\OneLoginBridgeBundle\SAML
 *
 * Essentially a bag of possible attributes.
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Attributes
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $attributes;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

    /**
     * Called by the Service Container.
     *
     * @param AttributeInterface $attribute
     * @throws \RuntimeException
     */
    public function addAttribute(AttributeInterface $attribute)
    {
        if ($this->attributes->contains($attribute)) {
            throw new \RuntimeException(sprintf(
                'Cannot add the same attribute twice, attribute "%s" has '
                . 'already been added before. Please check you service config.',
                $attribute->getName()
            ));
        }

        $this->attributes[$attribute->getName()] = $attribute;
    }

    /**
     * Allows to test whether or not a particular attribute exists
     *
     * @param $name
     * @return bool
     */
    public function hasAttribute($name)
    {
        return $this->attributes->containsKey($name);
    }

    /**
     * Retrieve an attribute if possible.
     *
     * @param string $name
     * @return AttributeInterface
     * @throws \InvalidArgumentException
     */
    public function getAttribute($name)
    {
        if (!$this->hasAttribute($name)) {
            throw new \InvalidArgumentException(sprintf(
                'Attribute "%s" does not exist or has not been added to the '
                . 'SAML/Attributes through the service containter',
                $name
            ));
        }

        return $this->attributes->get($name);
    }
}
