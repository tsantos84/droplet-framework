<?php

namespace Droplet;

use Droplet\Config\ConfigInterface;
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
    public function loadConfiguration(ConfigInterface $config)
    {
    }

    /**
     * @param Container       $container
     * @param ConfigInterface $config
     */
    public function buildContainer(Container $container, ConfigInterface $config)
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