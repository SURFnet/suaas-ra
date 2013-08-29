<?php

namespace SURFnet\OneLoginBridgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class AttributesCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition('surfnet.saml.attributes')) {
            return;
        }

        $definition = $container->getDefinition('surfnet.saml.attributes');

        $taggedServices = $container->findTaggedServiceIds('saml.attribute');
        foreach ($taggedServices as $id => $attributes) {
            $definition->addMethodCall('addAttribute', array(new Reference($id)));
        }
    }
}
