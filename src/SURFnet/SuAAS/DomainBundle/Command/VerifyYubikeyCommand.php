<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyYubikeyCommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="32", max="48")
     */
    public $otp;
}
