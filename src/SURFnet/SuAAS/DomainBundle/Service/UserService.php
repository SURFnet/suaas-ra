<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use SURFnet\SuAAS\DomainBundle\Command\PromoteRACommand;
use SURFnet\SuAAS\DomainBundle\Entity\Organisation;
use SURFnet\SuAAS\DomainBundle\Entity\RegistrationAuthority;
use SURFnet\SuAAS\DomainBundle\Entity\SAMLIdentity;
use SURFnet\SuAAS\DomainBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserService extends ORMService implements UserProviderInterface
{
    protected $rootEntityClass = 'SURFnet\SuAAS\DomainBundle\Entity\User';

    public function findAll()
    {
        $users = new ArrayCollection($this->getRepository()->findAll());

        return $users->map(function(User $user){
            return $user->getView();
        });
    }

    public function findRAByOrganisation(Organisation $organisation)
    {
        return $this
            ->getRepository()
            ->findRAForOrganisation($organisation)
            ->map(function(User $user){
                return $user->getRegistrationAuthorityView();
            });
    }

    public function promoteRA(User $user, PromoteRACommand $command)
    {
        $ra = new RegistrationAuthority();
        $ra->create($user, $command);

        $this->persist($ra)->flush();
    }

    public function revokeRA(User $user)
    {
        $this->getRepository()->removeRAByUser($user);
    }

    public function loadUserByUsername($username)
    {
        $user = $this->getRepository()->findByUsername($username);

        if (!$user instanceof User) {
            throw new UsernameNotFoundException(sprintf(
                'Unable to find an active user object identified by "%s".',
                $username
            ));
        }

        return $user;
    }

    public function resolveBySamlIdentity(SAMLIdentity $identity)
    {
        $user = $this->getRepository()->findByUsername($identity->getNameId());

        if (!$user instanceof User) {

            $organisation = $this->resolveOrganisation($identity->getSchacHomeOrganisation());
            $user = new User();
            $user->create($identity, $organisation);

            $this->persist($user)->flush();
        }

        return $user;
    }

    public function resolveOrganisation($organisationName)
    {
        $organisationRepository = $this->doctrine->getRepository(
            'SURFnet\SuAAS\DomainBundle\Entity\Organisation'
        );

        $organisation = $organisationRepository->findByName($organisationName);

        if (!$organisation instanceof Organisation) {
            $organisation = new Organisation();
            $organisation->create($organisationName);

            $this->persist($organisation);
        }

        return $organisation;
    }

    /**
     * @param  UserInterface $user
     * @return UserInterface
     * @throws UnsupportedUserException
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $class));
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @param  string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        return $this->rootEntityClass === $class || is_subclass_of($class, $this->rootEntityClass);
    }
}
