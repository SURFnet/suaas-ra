<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyMollieTokenCommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="6", max="6")
     */
    public $password;
}
