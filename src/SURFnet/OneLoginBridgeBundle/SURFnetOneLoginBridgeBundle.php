<?php

namespace SURFnet\OneLoginBridgeBundle;

use SURFnet\OneLoginBridgeBundle\DependencyInjection\AttributesCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SURFnetOneLoginBridgeBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new AttributesCompilerPass());
    }
}
