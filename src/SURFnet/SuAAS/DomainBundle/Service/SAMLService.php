<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\OneLoginBridgeBundle\Saml\Settings;
use SURFnet\OneLoginBridgeBundle\Service\ResponseAdapter;
use SURFnet\SuAAS\DomainBundle\Exception\SAMLInvalidException;
use SURFnet\SuAAS\DomainBundle\Service\SAML\IdentityResolver;
use Symfony\Component\HttpFoundation\Request;

class SAMLService
{
    private $samlResponse;

    /**
     * @var IdentityResolver
     */
    private $identityResolver;

    public function __construct(
        ResponseAdapter $response,
        IdentityResolver $resolver
    ) {
        $this->samlResponse = $response;
        $this->identityResolver = $resolver;
    }

    public function processResponse($samlResponseBody)
    {
        $this->samlResponse->setSAMLResponse($samlResponseBody);
        if (!$this->samlResponse->isValid()) {
            throw new SAMLInvalidException("SAML Request is invalid");
        }

        return $this->identityResolver->parse($this->samlResponse);
    }

    public function login()
    {

    }
}
