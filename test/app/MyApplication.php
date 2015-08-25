<?php

use Framework\Application;
use Framework\Droplet\Core\CoreDroplet;
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
        $this->registerDroplet(new CoreDroplet());
        $this->registerDroplet(new TemplatingDroplet());
        $this->registerDroplet(new App\AppDroplet());
    }
}