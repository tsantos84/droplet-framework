<?php

namespace Framework\Droplet;

use Framework\ApplicationInterface;
use Pimple\Container;
use Symfony\Component\Config\Definition\ConfigurationInterface as SfConfiguration;

/**
 * Interface DropletInterface
 * @package Droplet\Extension
 */
interface DropletInterface extends SfConfiguration
{
    /**
     * @param ApplicationInterface $application
     *
     * @return void
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * @return ApplicationInterface
     */
    public function getApplication();

    /**
     * @param array     $configs
     * @param Container $container
     */
    public function buildContainer(array $configs, Container $container);

    /**
     * @return array
     */
    public function getDependencies();

    /**
     * @return string
     */
    public function getName();
}