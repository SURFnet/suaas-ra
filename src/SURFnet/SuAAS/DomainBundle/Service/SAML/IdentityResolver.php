<?php

namespace SURFnet\SuAAS\DomainBundle\Service\SAML;

use SURFnet\OneLoginBridgeBundle\SAML\Attribute\DisplayName;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\GivenName;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\Mail;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\SchacHomeOrganisation;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\Surname;
use SURFnet\OneLoginBridgeBundle\Service\ResponseAdapter;
use SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity;

/**
 * Class IdentityResolver
 * @package SURFnet\SuAAS\DomainBundle\Service\SAML
 *
 * IdentityResolver, resolves a response to a SAMLIdentity
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class IdentityResolver
{
    public function parse(ResponseAdapter $response)
    {
        return new SAMLIdentity(
            $response->getNameId(),
            $response->getAttribute(SchacHomeOrganisation::NAME),
            $response->getAttribute(DisplayName::NAME),
            $response->getAttribute(Mail::NAME),
            $response->getAttribute(GivenName::NAME),
            $response->getAttribute(Surname::NAME)
        );
    }
}
