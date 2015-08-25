<?php

namespace Framework\Droplet\Core;

use Framework\Config\ConfigurationInterface;
use Framework\Config\FileLoader;
use Framework\Controller\ControllerResolver;
use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernel;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class CoreExtension
 * @package Droplet\Extension\CoreExtension
 */
class CoreDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function loadConfiguration(ConfigurationInterface $config)
    {
        $application = $this->getApplication();
        $loader      = new FileLoader(new FileLocator($application->getRootDir()));
        $config      = $loader->load($application->getFileConfigurationName());

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('core');

        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->useAttributeAsKey('name')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
            ->end()
            ->children()
                ->arrayNode('routing')
                    ->isRequired()
                    ->requiresAtLeastOneElement()
                    ->useAttributeAsKey('name')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('path')->isRequired()->end()
                            ->arrayNode('defaults')->end()
                            ->arrayNode('requirements')->end()
                            ->arrayNode('options')->end()
                            ->arrayNode('host')->end()
                            ->arrayNode('schemes')->end()
                            ->arrayNode('methods')->end()
                            ->arrayNode('conditions')->end()
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
        $config = $processor->processConfiguration($this, $configs);

        // set the parameters in the container
        foreach ($config['parameters'] as $name => $val) {
            $container[ $name ] = $val;
        }

        $container['event_dispatcher'] = function () {
            return new EventDispatcher();
        };

        $container['kernel'] = function($c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver']);
        };

        $this->configureRouting($container, $config);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'core';
    }

    private function configureRouting(Container $container, $config)
    {
        $container['routes'] = function () use ($config) {

            $routes = new RouteCollection();

            foreach ($config['routing'] as $name => $value) {
                $route = new Route(
                    $value['path'],
                    $value['defaults']
//                    $value['requirements'],
//                    $value['options'],
//                    $value['host'],
//                    $value['schemes'],
//                    $value['methods'],
//                    $value['conditions']
                );
                $routes->add($name, $route);
            }

            return $routes;
        };

        $container['request_context'] = function () {
            return new RequestContext();
        };

        $container['url_matcher'] = function($c) {
            return new UrlMatcher($c['routes'], $c['request_context']);
        };

        $container['controller_resolver'] = function($c) {
            return new ControllerResolver($c);
        };

        $container->extend('event_dispatcher', function(EventDispatcherInterface $dispatcher, $c) {

            $dispatcher->addListener(KernelEvents::REQUEST, function (GetResponseEvent $ev) use ($c) {
                $request = $ev->getRequest();
                $attribs = $c['url_matcher']->match($request->getPathInfo());
                $request->attributes->add($attribs);
            });

            return $dispatcher;
        });
    }
}