<?php

namespace Framework\Controller;

use Framework\DependencyInjection\ContainerAwareInterface;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver as BaseControllerResolver;

/**
 * Class ControllerResolver
 * @package Framework
 */
class ControllerResolver extends BaseControllerResolver
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container            $container
     * @param LoggerInterface|null $logger
     */
    public function __construct(Container $container, LoggerInterface $logger = null)
    {
        parent::__construct($logger);
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function getController(Request $request)
    {
        if (null !== $service = $request->attributes->get('_controller')) {

            // controller as a service
            if (is_string($service) && '@' == $service[0] && strpos($service, '::') !== false) {

                list($service, $action) = explode('::', substr($service, 1));

                if (isset($this->container[ $service ])) {

                    // fetch the controller from the service container
                    $controller = $this->container[ $service ];

                    // inject the container into controller
                    if ($controller instanceof ContainerAwareInterface) {
                        $controller->setContainer($this->container);
                    }

                    // creates the callable and replace the controller on request attributes
                    $callable = [$controller, $action];
                    $request->attributes->set('_controller', $callable);

                } else {
                    throw new \InvalidArgumentException(
                        'The controller for route ' . $request->attributes->get('_route') . ' was defined as a service ' .
                        'but the service ' . $service . ' was not found'
                    );
                }
            }
        }

        return parent::getController($request);
    }


}