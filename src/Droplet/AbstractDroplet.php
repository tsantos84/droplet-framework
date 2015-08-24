<?php

namespace Framework\Droplet;

use Framework\Application;
use Framework\Config\ConfigurationInterface;
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
    public function loadConfiguration(ConfigurationInterface $config)
    {
    }

    /**
     * @param Container       $container
     * @param ConfigurationInterface $config
     */
    public function buildContainer(Container $container, ConfigurationInterface $config)
    {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }
}