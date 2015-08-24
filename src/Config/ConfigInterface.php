<?php

namespace Droplet\Config;

/**
 * Class ConfigurationInterface
 * @package Max\Component\Configuration
 */
interface ConfigInterface extends \ArrayAccess
{
    public function import($file);

    public function merge($config);

    public function toArray();

    public function dump();
}