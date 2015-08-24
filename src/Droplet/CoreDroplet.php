<?php

namespace Framework\Droplet;

use Framework\Config\ConfigurationInterface;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
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
        return [
            'database' => [
                'driver'   => 'pdo_mysql',
                'host'     => 'localhost',
                'username' => 'maxmilhas'
            ],
            'routing' => [
                'route_1' => [
                    'path' => '/',
                    'defaults' => [
                        '_controller' => 'App\Controller\DefaultController::indexAction'
                    ]
                ]
            ],
            'templating' => [
                'paths' => 'teste'
            ]
        ];
    }

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('core');

        $this->addParametersSection($rootNode);
        $this->addDatabaseSection($rootNode);
        $this->addRoutingSection($rootNode);
        $this->addTemplatingSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @inheritDoc
     */
    public function buildContainer(Container $container, ConfigurationInterface $config)
    {
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

    private function addParametersSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('parameters')
                    ->useAttributeAsKey('name')
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }

    private function addDatabaseSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('database')
                    ->canBeDisabled()
                    ->isRequired()
                    ->children()
                        ->scalarNode('driver')->isRequired()->end()
                        ->scalarNode('host')->isRequired()->end()
                        ->scalarNode('username')->isRequired()->end()
                        ->scalarNode('password')->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }

    private function addRoutingSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('routing')
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
            ->end();

        return $rootNode;
    }

    private function addTemplatingSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('templating')
                    ->children()
                        ->arrayNode('paths')
                            ->example(['php'])
                            ->isRequired()
                            ->requiresAtLeastOneElement()
                            ->beforeNormalization()
                                ->ifTrue(function($v) { return !is_array($v); })
                                ->then(function ($v) { return array($v); })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $rootNode;
    }

    private function configureRouting(Container $container, ConfigurationInterface $config)
    {
        $container['routes'] = function () use ($config) {

            $routes = new RouteCollection();

            foreach ($config['routing']->toArray() as $name => $value) {
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

        $container['controller_resolver'] = function() {
            return new ControllerResolver();
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