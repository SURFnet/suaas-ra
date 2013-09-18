<?php

namespace Mollie\SMSBundle\SMS;

/**
 * Class Message
 * @package Mollie\SMSBundle\SMS
 *
 * DTO that contains the required dynamic fields to send a text-message
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Message
{
    /**
     * @var string the body of the message. May not be longer than 160 characters
     */
    public $body;

    /**
     * @var string the 8 character phone-number of the recipient (Everything after 06-)
     */
    public $recipient;
}

