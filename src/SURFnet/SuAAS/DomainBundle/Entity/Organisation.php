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

    public function __construct(array $data = array())
    {
        foreach ($data as $property => $value) {
            if (!property_exists($this, $property)) {
                throw new \RuntimeException(sprintf(
                    'Property "%s" does not exist in class "%s"',
                    $property,
                    get_class($this)
                ));
            }

            $this->{$property} = $value;
        }
    }

    public function __toString()
    {
        return $this->name;
    }
}
