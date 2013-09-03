<?php

namespace SURFnet\SuAAS\DomainBundle\Command;

abstract class AbstractCommand
{
    public function __construct(array $data = array())
    {
        foreach ($data as $key => $value) {
            if (!property_exists($this, $key )) {
                $parts   = explode("\\", get_class($this));
                $command = str_replace("Command", "", end($parts));

                throw new \RuntimeException(sprintf('Property "%s" is not a valid property on command "%s".', $key, $command));
            }

            $this->$key = $value;
        }
    }
}
