<?php

namespace Mollie\SMSBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('mollie_sms');

        $rootNode
            ->children()
                ->arrayNode('credentials')
                ->info('Mollie SMS credentials')
                    ->children()
                        ->scalarNode('username')
                            ->info('username for the Mollie SMS service')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid Mollie Username specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('password')
                            ->info('Password for the Mollie SMS service')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid Mollie Password specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('originator')
                            ->info('Sender Name for the Mollie SMS service')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '' || strlen($value) > 11);
                            })
                                ->thenInvalid('Invalid Mollie Sender Name specified: "%s", may not exceed 11 characters')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
