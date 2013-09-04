<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class VerifyMollieTokenCommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $password;
}
