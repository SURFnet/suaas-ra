<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Mollie
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Mollie extends AuthenticationMethod
{
    /**
     * Required start of the phone-number (by Mollie)
     */
    const PHONE_START = '316';
    /**
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=24)
     */
    private $phoneNumber;

    public function getType()
    {
        return 'SMS';
    }

    public function create($phoneNumber, User $user)
    {
        if ($this->id) {
            throw new \LogicException(
                'Cannot create a Mollie Authentication Method if it already has an id'
            );
        }

        if ($this->owner) {
            throw new \LogicException(
                'Cannot create a Mollie Authentication Method if it already has a User assigned'
            );
        }

        $this->phoneNumber = $phoneNumber;
        $this->owner = $user;
        $this->lastUsedAt = new \DateTime('now');

        return $this;
    }

    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    public function getReadablePhoneNumber()
    {
        $number = $this->getPhoneNumber();
        return '+31 (0) 6 - ' . sprintf(
            '%3d %3d %2d',
            substr($number, 0, 3),
            substr($number, 3, 3),
            substr($number, 6, 2)
        );
    }
}
