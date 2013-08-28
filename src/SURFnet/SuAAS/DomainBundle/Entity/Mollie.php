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
     * @var string
     *
     * @ORM\Column(name="phone_number", type="string", length=24)
     */
    private $phoneNumber;
}
