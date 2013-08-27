<?php

namespace SURFnet\OneLoginBridgeBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('surfnet_onelogin_bridge');

        $rootNode
            ->children()
                ->arrayNode('saml_settings')
                    ->info('check out https://onelogin.zendesk.com/entries/268420-saml-toolkit-for-php')
                    ->children()
                        ->scalarNode('target_url')
                            ->info('The IdP url to use to authenticate against')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('consumer_url')
                            ->isRequired()
                        ->end()
                        ->scalarNode('issuer_name')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('name_identifier_format')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('certificate')
                            ->isRequired()
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
