<?php

namespace Framework\Droplet\Core;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Templating\DelegatingEngine;
use Symfony\Component\Templating\Helper\SlotsHelper;
use Symfony\Component\Templating\Loader\FilesystemLoader;
use Symfony\Component\Templating\PhpEngine;
use Symfony\Component\Templating\TemplateNameParser;

/**
 * Class TemplatingDroplet
 * @package Framework\Droplet\Core
 */
class TemplatingDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('templating');

        $root
            ->children()
                ->arrayNode('paths')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->beforeNormalization()
                        ->ifArray()
                        ->then(function($v) {
                            $paths = [];
                            foreach ($v as $p) { $paths[] = sprintf('%s/%%name%%', $p); return $paths;}
                        })
                    ->end()
                    ->prototype('scalar')->end()
                ->end()
                ->arrayNode('globals')
                    ->prototype('array')->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(array $configs, Container $container)
    {
        $processor = new Processor();
        $config = $processor->processConfiguration($this, $configs);

        $container['templating.filesystem_loader'] = function() use ($config) {
            return new FilesystemLoader($config['paths']);
        };

        $container['templating.template_name_parser'] = function() {
            return new TemplateNameParser();
        };

        $container['templating.engine.php'] = function($c) use ($config) {

            $engine = new PhpEngine(
                $c['templating.template_name_parser'],
                $c['templating.filesystem_loader'],
                [new SlotsHelper()]
            );

            foreach ($config['globals'] as $name => $value) {
                $engine->addGlobal($name, $value);
            }

            return $engine;
        };

        $container['templating'] = function ($c) {
            return new DelegatingEngine([
                $c['templating.engine.php']
            ]);
        };
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'templating';
    }

}
