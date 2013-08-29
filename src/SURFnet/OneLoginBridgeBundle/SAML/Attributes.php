<?php

namespace SURFnet\OneLoginBridgeBundle\SAML;

use Doctrine\Common\Collections\ArrayCollection;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\AttributeInterface;

class Attributes
{
    const ATTR_UID = 'uid';
    const ATTR_SCHACORG = 'schacHomeOrganisation';
    const ATTR_NAME = 'displayName';
    const ATTR_EMAIL = 'mail';

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    private $attributes;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
    }

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

    public function hasAttribute($name)
    {
        return $this->attributes->containsKey($name);
    }

    /**
     *
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
