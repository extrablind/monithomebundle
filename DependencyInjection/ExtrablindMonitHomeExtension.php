<?php

namespace Extrablind\MonitHomeBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ExtrablindMonitHomeExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);
        $loader        = new YamlFileLoader(
      $container, new FileLocator(__DIR__.'/../Resources/config')
    );

        // Set alias for gateway depending on parameter set in config.
        $gateway = $container->getParameter('monithome.gateway.service');
        $alias   = $container->setAlias('monithome.gateway', $gateway);
        // Give access from $this->container->get('monithome.gateway')
        $alias->setPrivate(false);

        $container->setParameter('extrablind_monit_home', $config);

        $loader->load('services.yml');
        $loader->load('config.yml');
    }
}
