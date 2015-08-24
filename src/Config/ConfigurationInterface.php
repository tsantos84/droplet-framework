<?php

namespace Framework\Config;

/**
 * Class ConfigurationInterface
 * @package Max\Component\Configuration
 */
interface ConfigurationInterface extends \ArrayAccess
{
    public function merge($config);

    public function toArray();

    public function dump();
}