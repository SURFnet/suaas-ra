<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class PromoteRACommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $contactInfo;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    public $location;
}
