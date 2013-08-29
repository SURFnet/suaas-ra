<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RegistrationAuthority
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SURFnet\SuAAS\DomainBundle\Entity\RegistrationAuthorityRepository")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class RegistrationAuthority
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_info", type="string", length=200, nullable=true)
     */
    private $contactInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=200, nullable=true)
     */
    private $location;
}
