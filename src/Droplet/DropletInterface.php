<?php

namespace Framework\Droplet;

use Framework\Application;
use Framework\Config\ConfigurationInterface;
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
     * @return Application
     */
    public function getApplication();

    /**
     * @param ConfigurationInterface $config
     *
     * @return mixed
     */
    public function loadConfiguration(ConfigurationInterface $config);

    /**
     * @param array     $configs
     * @param Container $container
     */
    public function buildContainer(array $configs, Container $container);

    /**
     * @return string
     */
    public function getName();
}