<?php

namespace SURFnet\SuAAS\DomainBundle\Command\Mail;

use SURFnet\SuAAS\DomainBundle\Command\AbstractCommand;

class MailCommand extends AbstractCommand
{
    public $template = false;
    public $parameters = array();
    public $recepient;
    public $subject;
}
