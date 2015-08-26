<?php

namespace Framework\Droplet\Core;

use Framework\Controller\ControllerResolver;
use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
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
class RoutingDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('routing');

        $rootNode
            ->isRequired()
            ->requiresAtLeastOneElement()
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('path')->isRequired()->end()
                    ->arrayNode('defaults')
                        ->prototype('scalar')
                        ->end()
                    ->end()
                    ->arrayNode('requirements')->end()
                    ->arrayNode('options')->end()
                    ->arrayNode('host')->end()
                    ->arrayNode('schemes')->end()
                    ->arrayNode('methods')->end()
                    ->arrayNode('conditions')->end()
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

        $container['event_dispatcher'] = function () {
            return new EventDispatcher();
        };

        $container['kernel'] = function($c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver']);
        };

        $container['routes'] = function () use ($config) {

            $routes = new RouteCollection();

            foreach ($config as $name => $value) {
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

    /**
     * @return string
     */
    public function getName()
    {
        return 'routing';
    }
}