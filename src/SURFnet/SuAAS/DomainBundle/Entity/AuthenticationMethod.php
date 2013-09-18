<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SURFnet\SuAAS\DomainBundle\Command\VerifyRegistrationCodeCommand;
use SURFnet\SuAAS\DomainBundle\Entity\View\AuthenticationMethodView;

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
abstract class AuthenticationMethod
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    protected $owner;

    /**
     * @var string
     *
     * sha1(base64_encode(openssl_random_pseudo_bytes(50)))
     *
     * @ORM\Column(name="email_token", type="string", length=40, nullable=true)
     */
    protected $emailToken;

    /**
     * @var string
     *
     * "The authors recommend a length of 8 characters from [0-9A-Ba-b]."
     *
     * @ORM\Column(name="registration_code", type="string", length=8, nullable=true)
     */
    protected $registrationCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="confirmed_at", type="datetime", nullable=true)
     */
    protected $registrationCodeConfirmedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="token_ownership_confirmed_at", type="datetime", nullable=true)
     */
    protected $tokenOwnershipConfirmedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_at", type="datetime", nullable=true)
     */
    protected $approvedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="approved_by", referencedColumnName="id", nullable=true)
     */
    protected $approvedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="revoked_at", type="datetime", nullable=true)
     */
    protected $revokedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_at", type="datetime", nullable=true)
     */
    protected $requestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_used_at", type="datetime", nullable=false)
     */
    protected $lastUsedAt;

    abstract public function getType();

    public function generateEmailToken()
    {
        $token = sha1(base64_encode(openssl_random_pseudo_bytes(64)));

        return $this->emailToken = $token;
    }

    public function isUserOwner(User $user)
    {
        return $this->owner->isEqualTo($user);
    }

    public function generateRegistrationCode()
    {
        $code = substr(sha1(base64_encode(openssl_random_pseudo_bytes(64))), 0, 8);
        $this->requestedAt = new \DateTime('now');

        return $this->registrationCode = $code;
    }

    public function hasRegistrationCode()
    {
        return (bool) $this->registrationCode;
    }

    public function matchRegistrationCode(VerifyRegistrationCodeCommand $command)
    {
        if ($this->registrationCode === $command->code) {
            $this->registrationCodeConfirmedAt = new \DateTime();
            return true;
        }

        return false;
    }

    public function canVerifyRegistrationCode()
    {
        return $this->registrationCode !== null
                && $this->registrationCodeConfirmedAt === null;
    }

    public function canConfirmToken()
    {
        return $this->registrationCodeConfirmedAt !== null
                && $this->tokenOwnershipConfirmedAt === null;
    }

    public function canConfirmIdentity()
    {
        return $this->tokenOwnershipConfirmedAt !== null
                && $this->approvedAt === null;
    }

    public function confirm()
    {
        $this->tokenOwnershipConfirmedAt = new \DateTime();
    }

    public function approve(User $user)
    {
        $this->approvedAt = new \DateTime();
        $this->approvedBy = $user;
    }

    public function decline()
    {
        $this->approvedBy = null;
        $this->approvedAt = null;
        $this->tokenOwnershipConfirmedAt = null;
        $this->registrationCodeConfirmedAt = null;
    }

    public function getView()
    {
        return new AuthenticationMethodView(
            array(
                'requestedAt' => $this->requestedAt,
                'owner' => $this->owner->getView(),
                'tokenType' => $this->getType(),
                'tokenId' => $this->id
            )
        );
    }
}
