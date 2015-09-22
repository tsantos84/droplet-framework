<?php

namespace Framework\Droplet\Core;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ParameterDroplet
 * @package Framework\Droplet\Core
 */
class ParameterDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('parameters');

        $rootNode
            ->useAttributeAsKey('name')
            ->defaultValue([])
            ->prototype('scalar')->end();

        return $treeBuilder;
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(array $configs, Container $container)
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration($this, $configs);

        // set the parameters in the container
        foreach ($config as $name => $val) {
            $container[ $name ] = $val;
        }
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'parameters';
    }
}
