<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SURFnet\SuAAS\DomainBundle\Command\CreateMollieOTPCommand;

/**
 * Class MollieOTP
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class MollieOTP
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Mollie
     *
     * @ORM\ManyToOne(targetEntity="Mollie")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $mollieToken;

    /**
     * @var string
     *
     * @ORM\Column(length=60, nullable=false)
     */
    private $otp;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $requestedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $confirmedAt;

    public function create(CreateMollieOTPCommand $command)
    {
        if ($this->id || $this->mollieToken || $this->otp) {
            throw new \DomainException(
                "OTP has already been created, cannot create OTP again."
            );
        }

        $this->mollieToken = $command->token;
        $this->otp = $this->generateOTP();
        $this->requestedAt = new \DateTime('now');

        $command->otp = $this;
    }

    private function generateOTP()
    {
        $alphabet = '123456789bcdfghjkmnpqrstvwxyz';
        $length = strlen($alphabet) - 1;
        $otp = '';

        do {
            $otp .= $alphabet[mt_rand(0, $length)];
        } while (strlen($otp) < 6);

        return $otp;
    }

    public function confirm()
    {
        $this->confirmedAt = new \DateTime('now');
    }

    public function __toString()
    {
        if (!$this->id) {
            throw new \LogicException(
                "Cannot convert OTP to string when it has not been saved yet"
            );
        }

        return $this->otp;
    }
}
