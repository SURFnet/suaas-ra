<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

/**
 * UserRepository
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class UserRepository extends EntityRepository
{
    public function removeRAByUSer(User $user)
    {
        $dql = "
            DELETE FROM
                SURFnetSuAASDomainBundle:RegistrationAuthority ra
            WHERE
                ra.user = :user
        ";

        $this
            ->getEntityManager()
            ->createQuery($dql)
            ->setParameter('user', $user)
            ->execute();
    }

    /**
     *
     *
     * @param string $username
     * @return User|null
     */
    public function findByUsername($username)
    {
        return $this
            ->createQueryBuilder('u')
            ->select('u')
            ->where('u.nameId = :nameId')
            ->setParameter('nameId', $username)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
