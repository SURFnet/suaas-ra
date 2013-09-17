<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyRegistrationCodeCommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="8", max="8")
     */
    public $code;
}
