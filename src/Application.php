<?php

namespace Framework;

use Framework\Config\ConfigurationInterface;
use Framework\Config\FileLoader;
use Framework\Droplet\DropletInterface;
use Pimple\Container;
use Symfony\Component\Config\FileLocator;
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
        $this->droplets    = [];
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
     * @param DropletInterface $droplet
     *
     * @return Application
     */
    public function registerDroplet(DropletInterface $droplet)
    {
        $name = $droplet->getName();

        if (array_key_exists($name, $this->droplets)) {
            throw new \InvalidArgumentException('A droplet with name "' . $name . '" is already registered');
        }

        $droplet->setApplication($this);

        $this->droplets[ $name ] = $droplet;

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

            $configuration = $this->loadConfiguration();
            $container     = $this->getContainer();

            foreach ($this->droplets as $name => $droplet) {
                $configs = isset($configuration[ $name ]) ? $configuration[ $name ] : [];
                $droplet->buildContainer($configs, $container);
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
            $this->startContainer($this->container);
        }

        return $this->container;
    }

    /**
     * @return string
     */
    public function getFileConfigurationName()
    {
        return sprintf('config/config_%s.php', $this->getEnvironment());
    }

    /**
     * @return array
     */
    public function loadConfiguration()
    {
        $loader = new FileLoader(new FileLocator($this->getRootDir()));
        $config = $loader->load($this->getFileConfigurationName());

        return $config;
    }

    /**
     * @param Container $container
     */
    protected function startContainer(Container $container)
    {
        $container['app']          = $this;
        $container['app.root_dir'] = $this->getRootDir();
        $container['app.env']      = $this->getEnvironment();
    }
}