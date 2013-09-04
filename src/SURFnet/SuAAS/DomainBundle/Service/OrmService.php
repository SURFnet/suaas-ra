<?php

namespace SURFnet\SuAAS\DomainBundle\Service;

use Doctrine\Common\Persistence\ManagerRegistry;

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

    public function __construct(ManagerRegistry $registry)
    {
        $this->doctrine = $registry;
    }

    protected function getRepository()
    {
        return $this->doctrine->getRepository($this->rootEntityClass);
    }

    protected function persist($entity)
    {
        $em = $this->doctrine->getManager();
        $em->persist($entity);

        return $this;
    }

    protected function flush()
    {
        $em = $this->doctrine->getManager();
        $em->flush();
    }
}
