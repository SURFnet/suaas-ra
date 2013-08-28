<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SURFnet\SuAAS\DomainBundle\Entity\UserRepository")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class User
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
     * @var string
     *
     * the character sequence NameID from the SAMLIdentity. Since the spec does
     * not limit the length of this field, we hash it using hash('sha-256', $nameId) inside
     * the value object so that other objects (like this one) do not have to
     * worry about it
     *
     * @ORM\Column(name="name_id", type="string", length=64)
     */
    private $nameId;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=150)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=360)
     */
    private $email;

    /**
     * @var Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumn(name="organisation", referencedColumnName="id")
     */
    private $organisation;
}
