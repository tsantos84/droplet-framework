<?php

use Framework\Application;
use Framework\Droplet\Core\CoreDroplet;
use Framework\Droplet\Extension\DoctrineDroplet;

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
        $this->registerDroplet(new DoctrineDroplet());
    }
}