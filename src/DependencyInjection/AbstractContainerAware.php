<?php

namespace Framework\DependencyInjection;

use Pimple\Container;

/**
 * Class AbstractContainerAware
 * @package Framework\DependencyInjection
 */
abstract class AbstractContainerAware implements ContainerAwareInterface
{
    /**
     * @var Container
     */
    protected $container;

    /**
     * @inheritdoc
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
    }
}