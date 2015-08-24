<?php

namespace Framework\Config;

use Symfony\Component\Config\Loader\FileLoader as BaseFileLoader;

/**
 * Class FileLoader
 * @package Droplet\Configuration
 */
class FileLoader extends BaseFileLoader
{
    /**
     * @inheritDoc
     */
    public function load($resource, $type = null)
    {
        $file    = $this->locator->locate($resource);
        $config  = $this->getConfiguration($file);
        $imports = $this->getImports($config);
        $this->setCurrentDir(dirname($file));

        foreach ($imports as $import) {
            $config = array_replace_recursive($this->import($import), $config);
        }

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && 'php' === pathinfo($resource, PATHINFO_EXTENSION);
    }

    private function getConfiguration($resource)
    {
        $config = include $resource;

        if (!is_array($config)) {
            throw new \InvalidArgumentException('The configuration file "' . $resource . '" must return an array');
        }

        return $config;
    }

    private function getImports(array &$config)
    {
        if (array_key_exists('@import', $config)) {
            $imports = $config['@import'];

            if (!is_array($imports)) {
                $imports = [$imports];
            }

            unset($config['@import']);

            return $imports;
        }

        return [];
    }
}