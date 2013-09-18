<?php

namespace Mollie\SMSBundle\SMS;

use Guzzle\Http\Client;

/**
 * Class Service
 * @package Mollie\SMSBundle\SMS
 *
 * Service object that allows the sending of text-messages via Mollie SMS
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Service
{
    /**
     * Mollie URL to send texts via SMS. Choose to have const over config, since
     * this will never change - not unless they're willing to force all their
     * clients to update this.
     */
    CONST BASE_URL = "https://api.messagebird.com/xml/sms/";

    /**
     * @var string username to authenticate with at Mollie SMS
     */
    private $username;

    /**
     * @var string password to authenticate with at Mollie SMS
     */
    private $password;

    /**
     * @var string name (11 char max) displayed as sender
     */
    private $originator;

    /**
     * @var Client
     */
    private $client;

    /**
     * Constructor.
     *
     * @param Client $client
     * @param string $username
     * @param string $password
     * @param string $originator
     */
    public function __construct(Client $client, $username, $password, $originator)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
        $this->originator = $originator;
    }

    /**
     * Send a text via SMS
     *
     * @param Message $message
     * @return bool indicates whether or not the message has successfully been sent
     */
    public function send(Message $message)
    {
        $request = $this->client->get(self::BASE_URL);

        $request
            ->getQuery()
            ->set('username', $this->username)
            ->set('password', $this->password)
            ->set('originator', $this->originator)
            ->set('recipients', array('316' . $message->recipient))
            ->set('message', (string) $message->body);
return true;
        $response = $request->send();

        $xml = $response->xml();

        // handling should be improved 10-fold in production
        // see https://www.mollie.nl/support/documentatie/sms-diensten/sms/http/
        if (reset($xml->item->success) !== 'true') {
            return false;
        }

        return true;
    }
}
