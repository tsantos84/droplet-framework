<?php

namespace Framework\Droplet;

use Framework\ApplicationInterface;
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
     * @param ApplicationInterface $application
     */
    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    /**
     * @return ApplicationInterface
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