<?php

namespace SURFnet\SuAAS\SecurityBundle\Security\Authentication\Token;

use SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * Class SAMLToken
 * @package SURFnet\SuAAS\SecurityBundle\Security\Authentication\Token
 *
 * Security Token
 *
 * @author Daan van Renterghem <dvrenterghem@gmail.com>
 */
class SAMLToken extends AbstractToken
{
    /**
     * @var SAMLIdentity
     */
    private $samlIdentity;

    /**
     * Constructor
     *
     * @param SAMLIdentity    $samlIdentity The samlIdentity of the current request
     * @param RoleInterface[] $roles        An array of roles
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(SAMLIdentity $samlIdentity, array $roles = array())
    {
        parent::__construct($roles);

        $this->samlIdentity = $samlIdentity;

        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * Getter for the samlIdentity
     *
     * @return mixed
     */
    public function getSAMLIdentity()
    {
        return $this->samlIdentity;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function setUser($user)
    {
        $roles = $user->getRoles();

        parent::setUser($user);
        parent::setAuthenticated(count($roles) > 0);
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $user = $this->getUser();

        if (!$user) {
            return array();
        } else {
            return $user->getRoles();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(array($this->samlIdentity, parent::serialize()));
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        list($this->samlIdentity, $parentStr) = unserialize($serialized);
        parent::unserialize($parentStr);
    }
}
