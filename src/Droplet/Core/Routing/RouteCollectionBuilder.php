<?php

namespace Framework\Droplet\Core\Routing;

use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteCollectionBuilder
 * @package Framework\Droplet\Core\RoutingDroplet
 */
class RouteCollectionBuilder
{
    private $routes;

    private $collection;

    public function __construct()
    {
        $this->collection = new RouteCollection();
        $this->routes     = [];
    }

    public function get($path, $controller)
    {
        return $this->request('get', $path, $controller);
    }

    public function post($path, $controller)
    {
        return $this->request('post', $path, $controller);
    }

    public function put($path, $controller)
    {
        return $this->request('put', $path, $controller);
    }

    public function delete($path, $controller)
    {
        return $this->request('delete', $path, $controller);
    }

    public function request($method, $path, $controller)
    {
        return $this->routes[] = new RouteBuilder($method, $path, $controller);
    }

    public function prefix($prefix, \Closure $provider, array $defaults = [], array $requirements = [])
    {
        $builder = new static();

        $provider($builder);

        $collection = $builder->getRouteCollection();

        $collection->addPrefix($prefix, $defaults, $requirements);
        $this->collection->addCollection($collection);

        return $this;

    }

    /**
     * @return RouteCollection
     */
    public function getRouteCollection()
    {
        foreach ($this->routes as $route) {
            $name  = $route->getName();
            $route = $route->getRoute();
            $this->collection->add($name, $route);
        }

        return $this->collection;
    }
}
