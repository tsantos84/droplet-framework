<?php

namespace Framework\Config;

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
        $this->import($this->getImports($config));
    }

    /**
     * @param $file
     *
     * @return static
     */
    static public function load($file)
    {
        $config = new static();
        $config->import($file);

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
            $config       = $this->extractArray($config);
            $this->config = array_replace_recursive($this->config, $config);
        }

        return $this;
    }

    /**
     * @param $file
     *
     * @return $this
     */
    public function import($file)
    {
        $config = $this->importFile($file);
        $this->merge($config);

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
     * @param      $file
     *
     * @param null $from
     *
     * @return array
     */
    private function importFile($file, $from = null)
    {
        $files  = (array)$file;
        $config = [];

        foreach ($files as $file) {

            if ($file[0] == '@') {
                if (is_string($from)) {
                    $file = dirname($from) . substr($file, 1);
                }
            }

            if (is_file($file)) {
                $config  = $this->extractArray(require $file);
                $imports = $this->getImports($config);
                $config  = array_replace_recursive($this->importFile($imports, $file), $config);
            } else {
                throw new \InvalidArgumentException('Cant import the file "' . $file . '" as it was not found');
            }
        }

        return $config;
    }

    /**
     * @param array $config
     *
     * @return array
     */
    private function getImports(array &$config)
    {
        $files = [];

        if (isset($config['@import'])) {
            $files = (array)$config['@import'];
            unset($config['@import']);
        }

        return $files;
    }

    /**
     * @param $subject
     *
     * @return array
     */
    private function extractArray($subject)
    {
        switch (true) {
            case is_array($subject):
                return $subject;
            case $subject instanceof \ArrayObject:
                return $subject->getArrayCopy();
            case $subject instanceof self:
                return $subject->toArray();
        }

        $expected = [
            'array', 'ArrayObject', 'Max\\Component\\Configuration'
        ];

        throw new \InvalidArgumentException(sprintf(
            'Cant extract the array as the expected types are %s, but %s given',
            join(', ', $expected),
            $this->getVarType($subject)
        ));
    }

    /**
     * @param $subject
     *
     * @return string
     */
    private function getVarType($subject)
    {
        switch ($subject) {
            case is_object($subject):
                return 'instance of ' . get_class($subject);
            case is_string($subject):
                return 'string';
            case is_numeric($subject):
                return 'numeric';
            default:
                return 'unknown type';
        }
    }

    /**
     * @param $option
     */
    private function throwOptionNotExists($option)
    {
        throw new \InvalidArgumentException('The option "' . $option . '" does not exists"');
    }
}