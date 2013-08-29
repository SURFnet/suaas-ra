<?php

namespace SURFnet\SuAAS\DomainBundle\Service\SAML;

use SURFnet\OneLoginBridgeBundle\SAML\Attribute\DisplayName;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\Mail;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\SchacHomeOrganisation;
use SURFnet\OneLoginBridgeBundle\SAML\Attribute\Uid;
use SURFnet\OneLoginBridgeBundle\Service\ResponseAdapter;
use SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity;

class IdentityResolver
{
    public function parse(ResponseAdapter $response)
    {
        return new SAMLIdentity(
            $response->getNameId(),
            $response->getAttribute(SchacHomeOrganisation::NAME),
            $response->getAttribute(DisplayName::NAME),
            $response->getAttribute(Mail::NAME)
        );
    }
}
