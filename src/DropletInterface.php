<?php

namespace Droplet;

use Droplet\Config\ConfigurationInterface;
use Pimple\Container;
use Symfony\Component\Config\Definition\ConfigurationInterface as SfConfiguration;

/**
 * Interface DropletInterface
 * @package Droplet\Extension
 */
interface DropletInterface extends SfConfiguration
{
    /**
     * @param Application $application
     *
     * @return void
     */
    public function setApplication(Application $application);

    /**
     * @param ConfigurationInterface $config
     *
     * @return mixed
     */
    public function loadConfiguration(ConfigurationInterface $config);

    /**
     * @param Container       $container
     * @param ConfigurationInterface $config
     *
     * @return void
     */
    public function buildContainer(Container $container, ConfigurationInterface $config);

    /**
     * @return string
     */
    public function getName();
}