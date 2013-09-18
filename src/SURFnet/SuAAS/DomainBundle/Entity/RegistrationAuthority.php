<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SURFnet\SuAAS\DomainBundle\Command\PromoteRACommand;
use SURFnet\SuAAS\DomainBundle\Entity\View\RegistrationAuthorityView;

/**
 * RegistrationAuthority
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Table()
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class RegistrationAuthority
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
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="registrationAuthority")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="contact_info", type="string", length=200, nullable=true)
     */
    private $contactInfo;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="string", length=200, nullable=true)
     */
    private $location;

    public function create(User $user, PromoteRACommand $command)
    {
        if ($this->id) {
            throw new \RuntimeException("Cannot create pre-existing RA");
        }

        $this->user = $user;
        $this->contactInfo = $command->contactInfo;
        $this->location = $command->location;

        return $this;
    }

    public function getView(RegistrationAuthorityView $view = null)
    {
        if ($view === null) {
            $view = new RegistrationAuthorityView();
        }

        $view->contactInfo = $this->contactInfo;
        $view->location = $this->location;

        return $view;
    }
}
