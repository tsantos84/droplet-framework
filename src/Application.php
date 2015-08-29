<?php

namespace Framework;

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
    private $booted;

    /**
     * @param string $env
     * @param string $name
     */
    public function __construct($env = 'prod', $name = 'MyApp')
    {
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
     * @return $this
     */
    public function boot()
    {
        if ($this->booted) {
            return $this;
        }

        $this->registerDroplets();
        $this->resolveDroplet();
        $this->buildContainer();

        $this->booted = true;

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
        if ($this->booted) {
            throw new \RuntimeException('Tried to register a droplet after application booted');
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
     * Register the extensions for this application
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
     * @param null $droplet
     */
    protected function resolveDroplet($droplet = null)
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
    protected function buildContainer()
    {
        $configuration = $this->loadConfiguration();
        $container     = $this->getContainer();

        foreach ($this->droplets as $name => $droplet) {
            $configs = isset($configuration[ $name ]) ? $configuration[ $name ] : [];
            $droplet->buildContainer($configs, $container);
        }
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