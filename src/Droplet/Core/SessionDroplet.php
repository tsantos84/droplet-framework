<?php

namespace Framework\Droplet\Core;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\NativeSessionStorage;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class SessionDroplet
 * @package Framework\Droplet\Core
 */
class SessionDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('session');

        $storageKey = sprintf('_%s_', $this->getApplication()->getName());

        $rootNode
            ->children()
                ->scalarNode('storage_key')
                    ->defaultValue($storageKey)
                ->end()
                ->arrayNode('options')
                    ->prototype('array')
                    ->end()
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

        $container['session.storage_key'] = $config['storage_key'];

        $container['session.handler'] = function () use ($config) {
            return new NativeSessionStorage($config['options']);
        };

        $container['session.bag'] = function ($c) {
            return new AttributeBag($c['session.storage_key']);
        };

        $container['session'] = function ($c) {
            return new Session($c['session.handler'], $c['session.bag']);
        };

        // events to start and save the session
        if (isset($container['event_dispatcher'])) {
            $container->extend('event_dispatcher', function (EventDispatcherInterface $dispatcher, Container $container) {

                $session = $container['session'];

                // set the session in request
                $dispatcher->addListener(KernelEvents::REQUEST, function (GetResponseEvent $ev) use ($session) {

                    if (!$ev->isMasterRequest()) {
                        return;
                    }

                    $request = $ev->getRequest();

                    if (null === $request->getSession()) {
                        $request->setSession($session);
                        $session->start();
                    }

                });

                // save the session
                $dispatcher->addListener(KernelEvents::FINISH_REQUEST, function (FinishRequestEvent $ev) {

                    if (!$ev->isMasterRequest()) {
                        return;
                    }

                    if ($session = $ev->getRequest()->getSession()) {
                        $session->save();
                    }
                });

                return $dispatcher;
            });
        }

        // add the global variable to access the session from views
        if (isset($container['templating.engine.php'])) {
            $container->extend('templating.engine.php', function($engine, $c) {
                $engine->addGlobal('session', $c['session']);
                return $engine;
            });
        }
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'session';
    }
}