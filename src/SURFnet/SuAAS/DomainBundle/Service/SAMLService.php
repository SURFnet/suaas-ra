<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use SURFnet\OneLoginBridgeBundle\Saml\Settings;
use SURFnet\OneLoginBridgeBundle\Service\ResponseAdapter;
use SURFnet\SuAAS\DomainBundle\Exception\SAMLInvalidException;
use SURFnet\SuAAS\DomainBundle\Service\SAML\IdentityResolver;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class SAMLService
 * @package SURFnet\SuAAS\DomainBundle\Service
 *
 * SAML Service, wrapper around the OneLoginBridgeBundle to abstract away the
 * SAML handling
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SAMLService
{
    /**
     * @var ResponseAdapter
     */
    private $samlResponse;

    /**
     * @var IdentityResolver
     */
    private $identityResolver;

    /**
     * @param ResponseAdapter $response
     * @param IdentityResolver $resolver
     */
    public function __construct(
        ResponseAdapter $response,
        IdentityResolver $resolver
    ) {
        $this->samlResponse = $response;
        $this->identityResolver = $resolver;
    }

    /**
     * Process a responsebody from a SALM post request
     *
     * @param $samlResponseBody
     * @return \SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity
     * @throws \SURFnet\SuAAS\DomainBundle\Exception\SAMLInvalidException
     */
    public function processResponse($samlResponseBody)
    {
        $this->samlResponse->setSAMLResponse($samlResponseBody);
        if (!$this->samlResponse->isValid()) {
            throw new SAMLInvalidException("SAML Request is invalid");
        }

        return $this->identityResolver->parse($this->samlResponse);
    }
}
