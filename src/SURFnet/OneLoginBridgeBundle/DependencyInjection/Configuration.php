<?php

namespace SURFnet\OneLoginBridgeBundle\DependencyInjection;

use SURFnet\OneLoginBridgeBundle\Saml\Settings;
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
                            ->example('https://engineblok.surfconext.nl/simplesaml/SSOservice.php')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid saml target url specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('consumer_url')
                            ->info('The name of the route to which the response for the authentication is sent')
                            ->example('saml_consume')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid saml consumer route specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('issuer_name')
                            ->info('The name of the service provider')
                            ->example('SuAAS')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid saml issuer name specified: "%s"')
                            ->end()
                        ->end()
                        ->scalarNode('name_identifier_format')
                            ->info('The format of the identifier for this user. '
                                    . 'Advised is to use "urn:oasis:names:tc:'
                                    . 'SAML:2.0:nameid-format:persistent" to '
                                    . 'have a unique identifier per user of the'
                                    . ' SAML authentication system across all '
                                    . 'requests. For possible options see '
                                    . 'OneLogin_Saml_Settings')
                            ->example('urn:oasis:names:tc:SAML:2.0:nameid-format:persistent')
                            ->validate()
                            ->ifNotInArray(Settings::getValidNameIdFormats())
                                ->thenInvalid('Invalid saml name identifier format specified: "%s"')
                            ->end()
                            ->isRequired()
                        ->end()
                        ->scalarNode('certificate')
                            ->isRequired()
                            ->validate()
                            ->ifTrue(function ($value) {
                                return (!is_string($value) || trim($value) === '');
                            })
                                ->thenInvalid('Invalid saml certificate specified: "%s"')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
