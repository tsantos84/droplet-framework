<?php

namespace Framework\Config;
use Symfony\Component\Config\FileLocator;

/**
 * Class Configuration
 * @package Max\Component
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @var
     */
    private $config;

    /**
     * @param array $config
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * @param $file
     *
     * @return static
     */
    static public function load($file)
    {
        $loader = new FileLoader(new FileLocator(dirname($file)));
        $config = new static($loader->load($file));

        return $config;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        return $this->config;
    }

    /**
     * @param mixed $config
     *
     * @return $this
     */
    public function merge($config)
    {
        $configs = func_get_args();
        foreach ($configs as $config) {
            $config       = $config instanceof self ? $config->toArray() : (array)$config;
            $this->config = array_replace_recursive($this->config, $config);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function dump()
    {
        return var_export($this->config, true);
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->config);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        $path = explode('/', trim($offset));

        // leading with single option
        if (count($path) == 1) {

            if (isset($this[ $offset ])) {
                $option = $this->config[ $offset ];

                return is_array($option) ? new static($option) : $option;
            } else {
                return $this->throwOptionNotExists($offset);
            }

        } else {

            // leading with namespaced option
            $option = $this;

            foreach ($path as $index) {
                $option = $option[ $index ];
            }

            return $option;
        }
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $value)
    {
        $parts = explode('/', trim($offset, '/'));
        $count = count($parts);

        // leading with single option
        if ($count == 1) {
            $this->config[ $offset ] = $value;
        } else {

            $node = &$this->config;

            foreach ($parts as $part) {
                if (array_key_exists($part, $node)) {
                    $node = &$node[ $part ];
                } else {
                    throw new \InvalidArgumentException('Invalid path provided');
                }
            }

            $node = $value;
        }

    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        if (isset($this[ $offset ])) {
            unset($this->config[ $offset ]);

            return;
        }

        $this->throwOptionNotExists($offset);
    }

    /**
     * @param $option
     */
    private function throwOptionNotExists($option)
    {
        throw new \InvalidArgumentException('The option "' . $option . '" does not exists"');
    }
}