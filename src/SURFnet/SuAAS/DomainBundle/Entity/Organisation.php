<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organisation
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="SURFnet\SuAAS\DomainBundle\Entity\OrganisationRepository")
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class Organisation
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
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    public function create($name)
    {
        if ($this->id) {
            throw new \LogicException(
                "Cannot create an Organisation when that Organisation already has an ID"
            );
        }

        if (!is_string($name)) {
            throw new \InvalidArgumentException(
                '$name must be a string'
            );
        }

        $this->name = $name;
        $this->createdAt = new \DateTime('now');
    }

    public function __toString()
    {
        return $this->name;
    }
}
