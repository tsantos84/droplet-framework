<?php

namespace Framework\Droplet\Core;

use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
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

        $rootNode
            ->children()
            ->scalarNode('storage_key')
            ->defaultValue('_session_key')
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

        $container['session.handler'] = function () {
            return new NativeSessionHandler();
        };

        $container['session.bag'] = function ($c) {
            return new AttributeBag($c['session.storage_key']);
        };

        $container['session'] = function ($c) {
            return new Session($c['session.handler']);
        };

        // events to start and save the session
        $container->extend('event_dispatcher', function (EventDispatcherInterface $dispatcher, Container $container) {

            $session = $container['session'];

            // set the session in request
            $dispatcher->addListener(KernelEvents::REQUEST, function (GetResponseEvent $ev) use ($session) {

                if ($ev->isMasterRequest()) {
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

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'session';
    }
}