<?php

namespace Framework\DependencyInjection;

use Pimple\Container;

/**
 * Interface ContainerAwareInterface
 * @package Framework\DependencyInjection
 */
interface ContainerAwareInterface
{
    /**
     * @param Container $container
     *
     * @return mixed
     */
    public function setContainer(Container $container);
}