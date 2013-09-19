<?php

namespace Yubico\YubikeyBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('yubico_yubikey');

        $rootNode
            ->children()
                ->arrayNode('credentials')
                ->info('YubiKey API Credentials')
                    ->children()
                        ->scalarNode('client')
                            ->info('Client ID for the YubiKey API')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid YubiKey API Client ID specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('secret')
                            ->info('Secret for the YubiKey API')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid YubiKey API secret specified: "%s"')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
