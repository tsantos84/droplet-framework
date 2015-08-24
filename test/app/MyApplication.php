<?php

use Droplet\Application;


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
        $this->registerDroplet(new \Droplet\Core\CoreDroplet());
    }
}