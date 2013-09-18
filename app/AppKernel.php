<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
            new Mopa\Bundle\BootstrapBundle\MopaBootstrapBundle(),
            new SURFnet\SuAAS\RABundle\SURFnetSuAASRABundle(),
            new SURFnet\SuAAS\SelfServiceBundle\SURFnetSuAASSelfServiceBundle(),
            new SURFnet\SuAAS\DomainBundle\SURFnetSuAASDomainBundle(),
            new SURFnet\OneLoginBridgeBundle\SURFnetOneLoginBridgeBundle(),
            new SURFnet\SuAAS\SecurityBundle\SURFnetSuAASSecurityBundle(),
            new Mollie\SMSBundle\MollieSMSBundle(),
            new Yubico\YubikeyBundle\YubicoYubikeyBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
