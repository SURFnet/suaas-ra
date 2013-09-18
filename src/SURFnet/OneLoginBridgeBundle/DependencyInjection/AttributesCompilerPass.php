<?php

namespace SURFnet\OneLoginBridgeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler pass that finds the SAML attributes by tag and adds them to the
 * saml attributes service by registering a method call
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
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
