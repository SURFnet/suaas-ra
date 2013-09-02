<?php

namespace SURFnet\SuAAS\SecurityBundle;

use SURFnet\SuAAS\SecurityBundle\Security\Factory\SAMLFactory;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SURFnetSuAASSecurityBundle extends Bundle
{
    /**
     * {@inheritdoc} Also registers the SAMLFactory for the SAML Auth Provider
     *
     * @param ContainerBuilder $container
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $extension = $container->getExtension('security');
        $extension->addSecurityListenerFactory(new SAMLFactory());
    }
}
