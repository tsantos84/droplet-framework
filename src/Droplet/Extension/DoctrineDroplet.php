<?php

namespace Framework\Droplet\Extension;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class DatabaseDroplet
 * @package Framework\Droplet
 */
class DoctrineDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('doctrine');

        $rootNode
            ->canBeDisabled()
            ->children()
                ->arrayNode('orm')
                    ->children()
                        ->arrayNode('manager')
                            ->prototype('array')
                                ->children()
                                    ->enumNode('driver')
                                        ->values(['pdo_mysql', 'pdo_sqlite'])
                                    ->end()
                                    ->scalarNode('path')->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('mapping')
                            ->children()
                                ->enumNode('loader')
                                    ->values(['annotation', 'xml', 'yaml'])
                                ->end()
                                ->arrayNode('path')
                                    ->prototype('scalar')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(array $configs, Container $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration($this, $configs);

        $this->configureManager($config, $container);
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'doctrine';
    }

    private function configureManager(array $config, Container $container)
    {
        if (!$config['enabled']) {
            return;
        }
    }
}