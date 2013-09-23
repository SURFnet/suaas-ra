<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * Class ORMService
 * @package SURFnet\SuAAS\DomainBundle\Service
 *
 * Base ORM service providing some utilities that would outside of a pilot be
 * abstracted away
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class ORMService
{
    /**
     * @var ManagerRegistry
     */
    protected $doctrine;

    /**
     * @var string
     */
    protected $rootEntityClass;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    /**
     * Helper method to get the repo of the aggregate root the service pertains to
     *
     * @return \Doctrine\Common\Persistence\ObjectRepository
     */
    protected function getRepository()
    {
        return $this->doctrine->getRepository($this->rootEntityClass);
    }

    /**
     * Persis an Entity
     *
     * @param mixed $entity
     * @return ORMService
     */
    protected function persist($entity)
    {
        $em = $this->doctrine->getManager();
        $em->persist($entity);

        return $this;
    }

    /**
     * Flush the entity-manager
     *
     * @return ORMService
     */
    protected function flush()
    {
        $em = $this->doctrine->getManager();
        $em->flush();
    }
}
