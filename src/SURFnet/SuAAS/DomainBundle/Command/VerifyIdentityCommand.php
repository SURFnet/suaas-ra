<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

class VerifyIdentityCommand extends AbstractCommand
{
    /**
     * @var \SURFnet\SuAAS\DomainBundle\Entity\User;
     */
    public $approvedBy;

    /**
     * @var bool
     */
    public $verified = false;
}
