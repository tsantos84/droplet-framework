<?php

namespace Framework\Droplet\Core\Routing;

use Symfony\Component\Routing\Route;

/**
 * Class RouteBuilder
 * @package Framework\Droplet\Core\RoutingDroplet
 */
class RouteBuilder
{
    private $name;

    private $path;

    private $defaults;

    private $requirements;

    private $options;

    private $host;

    private $schemes;

    private $methods;

    private $conditions;

    public function __construct($methods, $path, $controller)
    {
        $this->methods  = $methods;
        $this->path     = $path;
        $this->defaults = [
            '_controller' => $controller
        ];
    }

    public function assign($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function defaults(array $defaults)
    {
        $this->defaults = array_replace($this->defaults, $defaults);

        return $this;
    }

    public function requirements(array $requirements)
    {
        $this->requirements = $requirements;

        return $this;
    }

    public function options(array $options)
    {
        $this->options = $options;

        return $this;
    }

    public function host($host)
    {
        $this->host = $host;

        return $this;
    }

    public function schemes($schemes)
    {
        $this->schemes = $schemes;

        return $this;
    }

    public function methods($method)
    {
        $this->methods = $method;

        return $this;
    }

    public function conditions($conditions)
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function getRoute()
    {
        $route = new Route(
            $this->path,
            $this->defaults,
            $this->requirements ?: [],
            $this->options ?: [],
            $this->host ?: '',
            $this->schemes ?: [],
            $this->methods ?: [],
            $this->conditions ?: ''
        );

        return $route;
    }
}
