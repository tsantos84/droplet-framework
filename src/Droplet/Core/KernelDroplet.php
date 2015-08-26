<?php

namespace Framework\Droplet\Core;

use Framework\Controller\ControllerResolver;
use Framework\Droplet\AbstractDroplet;
use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpKernel\HttpKernel;

/**
 * Class KernelDroplet
 * @package Framework\Droplet\Core
 */
class KernelDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function buildContainer(array $configs, Container $container)
    {
        $container['event_dispatcher'] = function () {
            return new EventDispatcher();
        };

        $container['controller_resolver'] = function($c) {
            return new ControllerResolver($c);
        };

        $container['kernel'] = function($c) {
            return new HttpKernel($c['event_dispatcher'], $c['controller_resolver']);
        };
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'kernel';
    }

}