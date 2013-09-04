<?php

namespace SURFnet\SuAAS\SecurityBundle\Security\Factory;

use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\Factory\SecurityFactoryInterface;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\DefinitionDecorator;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class SAMLFactory
 * @package SURFnet\SuAAS\SecurityBundle\Security\Factory
 *
 * Factory for the SAML security provider. Allows to reuse the provider in
 * multiple firewalls
 *
 * @author Daan van Renterghem <dvrenterghem@ibuildings.nl>
 */
class SAMLFactory implements SecurityFactoryInterface
{
    /**
     * {@inhertidoc}
     */
    public function create(ContainerBuilder $container, $id, $config, $userProvider, $defaultEntryPoint)
    {
        $providerId = 'security.authentication.provider.saml_secured.'.$id;
        $container
            ->setDefinition($providerId, new DefinitionDecorator('suaas_security.authentication.provider'))
            ->replaceArgument(0, new Reference('suaas.service.user'))
        ;

        $listenerId = 'security.authentication.listener.saml_secured.'.$id;
        $listener = $container->setDefinition($listenerId, new DefinitionDecorator('suaas_security.authentication.listener'));

        return array($providerId, $listenerId, $defaultEntryPoint);
    }

    /**
     * {@inheritdoc}
     */
    public function getPosition()
    {
        return 'pre_auth';
    }

    /**
     * {@inheritdoc}
     */
    public function getKey()
    {
        return 'saml';
    }

    /**
     * {@inheritdoc}
     */
    public function addConfiguration(NodeDefinition $node)
    {
    }
}
