<?php

namespace Framework;

use Framework\Config\Configuration;
use Framework\Config\ConfigurationInterface;
use Framework\Droplet\DropletInterface;
use Pimple\Container;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;

/**
 * Class Application
 * @package Bubble\Component\Application
 */
class Application implements HttpKernelInterface
{
    /**
     * @var string
     */
    private $environment;

    /**
     * @var string
     */
    private $rootDir;

    /**
     * @var DropletInterface[]
     */
    private $droplets;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var Container
     */
    private $container;

    /**
     * @var bool
     */
    private $booted;

    /**
     * @param string $env
     */
    public function __construct($env = 'prod')
    {
        $this->environment = $env;
        $this->droplets  = [];
    }

    /**
     * @return string
     */
    public function getRootDir()
    {
        if (null === $this->rootDir) {
            $r             = new \ReflectionObject($this);
            $this->rootDir = dirname($r->getFileName());
        }

        return $this->rootDir;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @return $this
     */
    public function boot()
    {
        if ($this->booted) {
            return $this;
        }

        $this->registerDroplets();

        // process the droplets configurations
        $configuration = $this->getConfiguration();

        $container = $this->getContainer();

        // build the container
        foreach ($this->droplets as $droplet) {
            $droplet->buildContainer($container, $configuration);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->boot();
        $container = $this->getContainer();
        return $container['kernel']->handle($request, $type, $catch);
    }

    /**
     * @return DropletInterface[]
     */
    public function getDroplets()
    {
        return $this->droplets;
    }

    /**
     * Register an extension to application
     *
     * @param ExtensionInterface $extension
     *
     * @return Application
     */
    public function registerDroplet(DropletInterface $extension)
    {
        $name = $extension->getName();

        if (array_key_exists($name, $this->droplets)) {
            throw new \InvalidArgumentException('A droplet with name "' . $name . '" is already registered');
        }

        $extension->setApplication($this);

        $this->droplets[ $name ] = $extension;

        return $this;
    }

    /**
     * Register the extensions for this application
     */
    public function registerDroplets()
    {
    }

    /**
     * @return ConfigurationInterface
     */
    public function getConfiguration()
    {
        if (null === $this->configuration) {

            $this->configuration = new Configuration();

            $processor = new Processor();

            foreach ($this->droplets as $droplet) {
                $config = $droplet->loadConfiguration($this->configuration);
                $processed = $processor->processConfiguration($droplet, [$config]);
                $this->configuration->merge($processed);
            }

        }

        return $this->configuration;

    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        if (null === $this->container) {
            $this->container = new Container();
        }

        return $this->container;
    }
}