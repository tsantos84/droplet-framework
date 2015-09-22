<?php

namespace Framework\Droplet\Core\Routing;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
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
            ->children()
                ->arrayNode('providers')
                    ->isRequired()
                    ->prototype('variable')->end()
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
        $config    = $processor->processConfiguration($this, $configs);

        // register the routes
        $container['routes'] = $this->buildRoutes($config['providers']);

        // register the request context
        $container['request_context'] = function () {
            return new RequestContext();
        };

        // register the url matcher
        $container['url_matcher'] = function ($c) {
            return new UrlMatcher($c['routes'], $c['request_context']);
        };

        // register the route generator
        $container['route_generator'] = function($c) {
            return new UrlGenerator($c['routes'], $c['request_context']);
        };

        $container->extend('event_dispatcher', function (EventDispatcherInterface $dispatcher, $c) {

            // try to found a route for the give request
            $dispatcher->addListener(KernelEvents::REQUEST, function (GetResponseEvent $ev) use ($c) {
                $request = $ev->getRequest();
                $attribs = $c['url_matcher']->match($request->getPathInfo());
                $request->attributes->add($attribs);
            });

            // handle page not found exceptions
            $dispatcher->addListener(KernelEvents::EXCEPTION, function(GetResponseForExceptionEvent $ev) {

                $response = $ev->getResponse() ?: new Response();

                if ($ev->getException() instanceof ResourceNotFoundException) {
                    $response->setStatusCode(404);
                }

                $ev->setResponse($response);
            });

            return $dispatcher;
        });
    }

    /**
     * @param array $providers
     *
     * @return RouteCollection
     */
    private function buildRoutes(array $providers)
    {
        $builder = new RouteCollectionBuilder();

        // build the routes of each provider
        foreach ($providers as $provider) {
            call_user_func($provider, $builder);
        }

        $collection = $builder->getRouteCollection();

        return $collection;
    }

    /**
     * @return array
     */
    public function getDependencies()
    {
        return ['kernel'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'routing';
    }
}
