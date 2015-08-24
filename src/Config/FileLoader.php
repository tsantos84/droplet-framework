<?php

namespace Framework\Config;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

/**
 * Class FileLoader
 * @package Droplet\Configuration
 */
class FileLoader extends BaseFileLoader
{
    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        $config = require $resource;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION);
    }

}