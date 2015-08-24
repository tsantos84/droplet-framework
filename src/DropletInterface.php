<?php

namespace Droplet;

use Droplet\Config\ConfigInterface;
use Pimple\Container;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Interface DropletInterface
 * @package Droplet\Extension
 */
interface DropletInterface extends ConfigurationInterface
{
    /**
     * @param Application $application
     *
     * @return void
     */
    public function setApplication(Application $application);

    /**
     * @param ConfigInterface $config
     *
     * @return mixed
     */
    public function loadConfiguration(ConfigInterface $config);

    /**
     * @param Container       $container
     * @param ConfigInterface $config
     *
     * @return void
     */
    public function buildContainer(Container $container, ConfigInterface $config);

    /**
     * @return string
     */
    public function getName();
}