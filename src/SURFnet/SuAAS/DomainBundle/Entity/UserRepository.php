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
    /**
     *
     *
     * @param string $username
     * @return User|null
     */
    public function findByUsername($username)
    {
        $dql = $this
            ->createQueryBuilder('u')
            ->select('u')
            ->where('u.nameId = :nameId')
            ->setParameter('nameId', $username)
            ->getQuery()
        ;

        $user = $dql->getOneOrNullResult();

        return $user;
    }
}
