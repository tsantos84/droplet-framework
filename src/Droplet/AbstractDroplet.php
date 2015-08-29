<?php

namespace Framework\Droplet;

use Framework\Application;
use Pimple\Container;

/**
 * Class AbstractExtension
 * @package Droplet\Extension
 */
abstract class AbstractDroplet implements DropletInterface
{
    /**
     * @var
     */
    private $application;

    /**
     * @param Application $application
     */
    public function setApplication(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @return Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @inheritDoc
     */
    public function getConfigTreeBuilder()
    {
    }

    /**
     * @inheritdoc
     */
    public function buildContainer(array $configs, Container $container)
    {
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}