<?php

namespace SURFnet\SuAAS\DomainBundle\Entity\View;

class AuthenticationMethodView extends AbstractView
{
    public $owner;
    public $requestedAt;

    /** @deprecated */
    public $name;
    /** @deprecated */
    public $email;


    public $tokenType;
    public $tokenId;
}
