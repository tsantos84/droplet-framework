<?php

namespace App;

use App\Controller\DefaultController;
use Framework\Droplet\AbstractDroplet;
use Pimple\Container;

/**
 * Class AppDroplet
 * @package App
 */
class AppDroplet extends AbstractDroplet
{
    /**
     * @inheritDoc
     */
    public function buildContainer(array $configs, Container $container)
    {
        $container['app.default_controller'] = function() {
            return new DefaultController();
        };
    }

    public function getName()
    {
        return 'app';
    }
}