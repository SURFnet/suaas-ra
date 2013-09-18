<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Tiqr
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Tiqr extends AuthenticationMethod
{
    public function getType()
    {
        return 'Tiqr';
    }
}
