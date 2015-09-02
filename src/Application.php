<?php

namespace Framework;

use Framework\Config\FileLoader;
use Framework\Droplet\DropletInterface;
use Pimple\Container;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class Application
 * @package Bubble\Component\Application
 */
class Application implements ApplicationInterface
{
    /**
     * @var string
     */
    private $name;

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
     * @var Container
     */
    private $container;

    /**
     * @var bool
     */
    private $configured;

    /**
     * @param string $env
     * @param string $name
     */
    public function __construct($env = null, $name = 'MyApp')
    {
        $this->configured  = false;
        $this->name        = $name;
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
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * @inheritDoc
     */
    public function isEnvironment($environment)
    {
        return $this->environment === $environment;
    }

    /**
     * @return $this
     */
    public function configure()
    {
        if ($this->isConfigured()) {
            return $this;
        }

        $this->registerDroplets();
        $this->resolveDroplet();
        $this->buildContainer();

        $this->configured = true;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function isConfigured()
    {
        return $this->configured;
    }

    /**
     * @inheritdoc
     */
    public function handle(Request $request, $type = self::MASTER_REQUEST, $catch = true)
    {
        $this->configure();

        return $this->container['kernel']->handle($request, $type, $catch);
    }

    /**
     * @return DropletInterface[]
     */
    public function getDroplets()
    {
        return $this->droplets;
    }

    /**
     * Register a droplet to application
     *
     * @param DropletInterface $droplet
     *
     * @return Application
     */
    public function registerDroplet(DropletInterface $droplet)
    {
        if ($this->configured) {
            throw new \RuntimeException('You cannot register droplet to this application as it already was configured');
        }

        $name = $droplet->getName();

        if (array_key_exists($name, $this->droplets)) {
            throw new \InvalidArgumentException('A droplet with name "' . $name . '" is already registered');
        }

        $droplet->setApplication($this);

        $this->droplets[ $name ] = $droplet;

        return $this;
    }

    /**
     * Register the droplet for this application
     */
    public function registerDroplets()
    {
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
    public function getConfigurationFilename()
    {
        if (null !== $this->environment) {
            $file = sprintf('config/config_%s.php', $this->getEnvironment());
        } else {
            $file = 'config/config.php';
        }

        return $file;
    }

    /**
     * @return array
     */
    private function loadConfiguration()
    {
        $loader = new FileLoader(new FileLocator($this->getRootDir()));
        $config = $loader->load($this->getConfigurationFilename());

        return $config;
    }

    /**
     * @param null $droplet
     */
    private function resolveDroplet($droplet = null)
    {
        static $resolved = [];

        if (null === $droplet) {

            foreach ($this->droplets as $droplet) {
                $this->resolveDroplet($droplet->getName());
            }

        } else {

            // droplet already resolved
            if (isset($resolved[ $droplet ])) {
                return;
            }

            // droplet not found
            if (!isset($this->droplets[ $droplet ])) {
                throw new \InvalidArgumentException(
                    'Trying to resolve the droplet ' . $droplet . ' that is not registered'
                );
            }

            $dependencies = $this->droplets[ $droplet ]->getDependencies();

            foreach ($dependencies as $dependency) {

                // dependency not registered
                if (!isset($this->droplets[ $dependency ])) {
                    throw new \RuntimeException(sprintf(
                        'The droplet %s has a dependency to %s which was not registered on application'
                    ));
                }

                $this->resolveDroplet($dependency);
            }

            $resolved[ $droplet ] = true;
        }
    }

    /**
     * Build the container
     */
    private function buildContainer()
    {
        $config        = $this->loadConfiguration();
        $unknownConfig = array_keys(array_diff_key($config, $this->droplets));

        if (count($unknownConfig)) {
            throw new \RuntimeException(
                'There is no droplet registered capable to handle the configurations for "' . current($unknownConfig) . '"'
            );
        }

        $container = $this->getContainer();

        foreach ($this->droplets as $name => $droplet) {
            $configs = isset($config[ $name ]) ? $config[ $name ] : [];
            $droplet->buildContainer($configs, $container);
        }
    }

    /**
     * @param Container $container
     */
    private function startContainer(Container $container)
    {
        $container['app']          = $this;
        $container['app.root_dir'] = $this->getRootDir();
        $container['app.env']      = $this->getEnvironment();
    }
}