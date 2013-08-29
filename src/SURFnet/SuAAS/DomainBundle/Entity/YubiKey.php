<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class YubiKey
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class YubiKey extends AuthenticationMethod
{
}
