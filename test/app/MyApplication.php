<?php

use Framework\Application;
use Framework\Droplet\Core\ParameterDroplet;
use Framework\Droplet\Core\RoutingDroplet;
use Framework\Droplet\Core\TemplatingDroplet;

/**
 * Class MyApplication
 */
class MyApplication extends Application
{
    /**
     *
     */
    public function registerDroplets()
    {
        $this->registerDroplet(new ParameterDroplet());
        $this->registerDroplet(new RoutingDroplet());
        $this->registerDroplet(new TemplatingDroplet());
        $this->registerDroplet(new App\AppDroplet());
    }
}