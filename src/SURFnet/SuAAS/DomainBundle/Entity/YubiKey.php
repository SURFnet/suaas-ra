<?php

namespace SURFnet\SuAAS\DomainBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use SURFnet\SuAAS\DomainBundle\Command\CreateYubikeyCommand;

/**
 * Class YubiKey
 * @package SURFnet\SuAAS\DomainBundle\Entity
 *
 * @ORM\Entity()
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class YubiKey extends AuthenticationMethod
{
    /**
     * @var string
     *
     * @ORM\Column(length=16, nullable=true)
     */
    private $yubikeyId;

    public function getType()
    {
        return 'Yubikey';
    }

    public function create(CreateYubikeyCommand $command)
    {
        $this->yubikeyId = substr($command->otp, 0, -32);
        $this->owner = $command->owner;
        $this->lastUsedAt = new \DateTime('now');

        return $this;
    }
}
