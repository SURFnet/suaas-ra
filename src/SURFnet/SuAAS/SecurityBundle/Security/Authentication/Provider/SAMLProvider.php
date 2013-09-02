<?php

namespace SURFnet\SuAAS\SecurityBundle\Security\Authentication\Provider;

use SURFnet\SuAAS\DomainBundle\Service\UserService;
use SURFnet\SuAAS\SecurityBundle\Security\Authentication\Token\SAMLToken;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

/**
 * Class SAMLProvider
 * @package SURFnet\SuAAS\SecurityBundle\Security\Authentication\Provider
 *
 * Authentication provider
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SAMLProvider implements AuthenticationProviderInterface
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Attempts to authenticate a user based on the token
     *
     * @param TokenInterface $token
     * @return TokenInterface
     * @throws AuthenticationException
     */
    public function authenticate(TokenInterface $token)
    {
        $user = $this->userService->resolveBySamlIdentity($token->getSAMLIdentity());

        if (!$user) {
            throw new AuthenticationException('SAML Authentication failed.');
        }

        $token->setUser($user);
        return $token;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof SAMLToken;
    }
}
