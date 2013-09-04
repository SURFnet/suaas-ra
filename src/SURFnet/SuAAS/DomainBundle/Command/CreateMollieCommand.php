<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

use Symfony\Component\Validator\Constraints as Assert;

class CreateMollieCommand extends AbstractCommand
{
    /**
     * @var string
     *
     * @todo consider building validator that proxies validation annotations
     * @Assert\NotBlank()
     * @Assert\Regex(
     *      pattern="~^\d{8}$~",
     *      message="Please enter only the last 8 digits of your mobile phone number"
     * )
     */
    public $phoneNumber;

    /**
     * @var \SURFnet\SuAAS\DomainBundle\Entity\User
     */
    public $user;
}
