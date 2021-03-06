<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SURFnet\SuAAS\DomainBundle\Entity\View\RegistrationAuthorityView;
use SURFnet\SuAAS\DomainBundle\Entity\View\UserView;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SURFnet\SuAAS\DomainBundle\Entity\UserRepository")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class User implements UserInterface, EquatableInterface, \Serializable
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * the character sequence NameID from the SAMLIdentity. Since the spec does
     * not limit the length of this field, we hash it using hash('sha-256', $nameId) inside
     * the value object so that other objects (like this one) do not have to
     * worry about it
     *
     * @ORM\Column(name="name_id", type="string", length=64)
     */
    private $nameId;

    /**
     * @var string
     *
     * @ORM\Column(name="display_name", type="string", length=150)
     */
    private $displayName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=360)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $givenName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $surname;

    /**
     * @var Organisation
     *
     * @ORM\ManyToOne(targetEntity="Organisation")
     * @ORM\JoinColumn(name="organisation", referencedColumnName="id")
     */
    private $organisation;

    /**
     * @var RegistrationAuthority|null
     *
     * @ORM\OneToOne(targetEntity="RegistrationAuthority", mappedBy="user")
     */
    private $registrationAuthority;

    public function create(SAMLIdentity $identity, Organisation $organisation)
    {
        if ($this->id) {
            throw new \LogicException(
                "Cannot create a User form a user that already has an ID"
            );
        }

        $this->nameId = $identity->getNameId();
        $this->organisation = $organisation;
        $this->displayName = $identity->getDisplayName();
        $this->email = $identity->getEmail();
        $this->givenName = $identity->getGivenName();
        $this->surname = $identity->getSurname();

        return $this;
    }

    public function getView()
    {
        return new UserView(array(
            'id' => $this->id,
            'name' => $this->displayName,
            'email' => $this->email,
            'firstName' => $this->givenName,
            'surname' => $this->surname,
            'isRa' => $this->isRA(),
        ));
    }

    public function getRegistrationAuthorityView()
    {
        $view = new RegistrationAuthorityView(array('name' => $this->displayName));

        return $this->registrationAuthority->getView($view);
    }

    public function getDisplayName()
    {
        return $this->displayName;
    }

    public function getUsername()
    {
        return $this->nameId;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getOrganisation()
    {
        return $this->organisation;
    }

    public function populateFromSAMLIdentity(SAMLIdentity $identity)
    {
        if ($this->id) {
            throw new \LogicException(
                "Cannot populate an Entity that has already been saved "
            );
        }

        $this->nameId = $identity->getNameId();
        $this->displayName = $identity->getDisplayName();

    }

    public function isRA()
    {
        return (bool) $this->registrationAuthority;
    }

    public function getRoles()
    {
        $roles = array(new Role('ROLE_USER'));

        if ($this->isRA()) {
            $roles[] = new Role('ROLE_ADMIN');
        }

        return $roles;
    }

    public function getPassword()
    {
        return '';
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
    }

    public function isEqualTo(UserInterface $user)
    {
        if (!$user instanceof User) {
            return false;
        }

        // nameId is the unique identifier of the user
        if ($this->nameId !== $user->getUsername()) {
            return false;
        }

        return true;
    }

    public function serialize()
    {
        return serialize(
            array(
                $this->id,
                $this->nameId,
                $this->displayName,
                $this->email
            )
        );
    }

    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->nameId,
            $this->displayName,
            $this->email
        ) = unserialize($serialized);
    }
}
