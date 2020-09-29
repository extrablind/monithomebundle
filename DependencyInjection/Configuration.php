<?php

namespace Extrablind\MonitHomeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements \Symfony\Component\Config\Definition\ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $builder = new TreeBuilder();
        $builder->root('extrablind_monit_home')
    ->children()
      ->arrayNode('settings')
        ->children()
          ->scalarNode('metric')->defaultValue(true)->end()
          ->scalarNode('auto_mode')->defaultValue(true)->end()
          ->scalarNode('timezone')->defaultValue('Europe/Paris')->end()
        ->end()
      ->end()

      ->arrayNode('gateway')
        ->children()
          // Usb
          ->arrayNode('usb')
            ->children()
              ->scalarNode('device')->defaultValue('/dev/ttyUSB0')->end()
              ->scalarNode('baudrate')->defaultValue(115200)->end()
              ->scalarNode('bits')->defaultValue(8)->end()
              ->scalarNode('stop')->defaultValue(1)->end()
              ->scalarNode('parity')->defaultValue(0)->end()
            ->end()
          ->end()
          // File
          ->arrayNode('file')
            ->children()
              ->scalarNode('path')->defaultValue("Extrablind\MonitHomeBundle\Services\MySensors\Gateways\FileGateway")->end()
            ->end()
          ->end()

          ->end()
    ->end()
    ;

        return $builder;
    }
}
