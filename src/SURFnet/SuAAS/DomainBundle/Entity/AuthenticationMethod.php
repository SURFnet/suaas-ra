<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AuthenticationMethod
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SURFnet\SuAAS\DomainBundle\Entity\AuthenticationMethodRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"tiqr"="Tiqr", "mollie"="Mollie", "yubi"="YubiKey"})
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class AuthenticationMethod
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
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var string
     *
     * sha1(base64_encode(openssl_random_pseudo_bytes(50)))
     *
     * @ORM\Column(name="email_token", type="string", length=40, nullable=false)
     */
    private $emailToken;

    /**
     * @var string
     *
     * "The authors recommend a length of 8 characters from [0-9A-Ba-b]."
     *
     * @ORM\Column(name="registration_code", type="string", length=8, nullable=false)
     */
    private $registrationCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=false)
     */
    private $approvedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="approved_by", referencedColumnName="id", nullable=true)
     */
    private $approvedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revoked_at", type="datetime", nullable=true)
     */
    private $revokedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=false)
     */
    private $requestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_used_at", type="datetime", nullable=false)
     */
    private $lastUsedAt;
}
