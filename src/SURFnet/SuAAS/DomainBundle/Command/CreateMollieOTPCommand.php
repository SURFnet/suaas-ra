<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

class CreateMollieOTPCommand extends AbstractCommand
{
    /**
     * @var \SURFnet\SuAAS\DomainBundle\Entity\Mollie
     */
    public $token;

    /**
     * @var \SURFnet\SuAAS\DomainBundle\Entity\MollieOTP
     */
    public $otp;
}
