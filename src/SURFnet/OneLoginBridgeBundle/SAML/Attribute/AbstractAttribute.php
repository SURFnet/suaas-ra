<?php

namespace SURFnet\OneLoginBridgeBundle\SAML\Attribute;

abstract class AbstractAttribute implements AttributeInterface
{
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
}
