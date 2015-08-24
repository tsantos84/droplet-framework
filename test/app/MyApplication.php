<?php

use Framework\Application;
use Framework\Droplet\Core\CoreDroplet;

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
    }
}