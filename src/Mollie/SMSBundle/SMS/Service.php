<?php

namespace Mollie\SMSBundle\SMS;

use Guzzle\Http\Client;

class Service
{
    CONST BASE_URL = "https://api.messagebird.com/xml/sms/";

    private $username;
    private $password;
    private $originator;
    private $client;

    public function __construct(Client $client, $username, $password, $originator)
    {
        $this->client = $client;
        $this->username = $username;
        $this->password = $password;
        $this->originator = $originator;
    }

    public function send(Message $message)
    {
        $request = $this->client->get(self::BASE_URL);

        $request
            ->getQuery()
            ->set('username', $this->username)
            ->set('password', $this->password)
            ->set('originator', $this->originator)
            ->set('recipients', array($message->recipient))
            ->set('message', (string) $message->body);

        $response = $request->send();

        $xml = $response->xml();
var_dump($xml);
        // handling should be improved 10-fold
        // see https://www.mollie.nl/support/documentatie/sms-diensten/sms/http/
        if (reset($xml->item->success) !== 'true') {
            return false;
        }

        return true;
    }
}
