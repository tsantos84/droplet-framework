<?php

namespace Framework;

use Framework\Droplet\DropletInterface;
use Pimple\Container;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class ApplicationInterface
 * @package Framework
 */
interface ApplicationInterface extends HttpKernelInterface
{
    public function getRootDir();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getEnvironment();

    /**
     * @param $environment
     *
     * @return boolean
     */
    public function isEnvironment($environment);

    /**
     * @return $this
     */
    public function configure();

    /**
     * @return boolean
     */
    public function isConfigured();

    /**
     * @return DropletInterface[]
     */
    public function getDroplets();

    /**
     * Register an extension to application
     *
     * @param DropletInterface $droplet
     *
     * @return Application
     */
    public function registerDroplet(DropletInterface $droplet);

    /**
     * Register the extensions for this application
     */
    public function registerDroplets();

    /**
     * @return Container
     */
    public function getContainer();

    /**
     * @return string
     */
    public function getConfigurationFilename();
}
