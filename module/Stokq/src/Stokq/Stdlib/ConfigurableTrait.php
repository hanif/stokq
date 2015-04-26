<?php

namespace Stokq\Stdlib;

/**
 * Class ConfigurableTrait
 * @package Stokq\Stdlib
 */
trait ConfigurableTrait
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @return array
     */
    function getConfig()
    {
        return $this->config;
    }

    /**
     * @param array $config
     * @return void
     */
    function setConfig(array $config)
    {
        $this->config = $config;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function getConfigItem($key, $default = null)
    {
        if (isset($this->config[$key])) {
            return $this->config[$key];
        }

        return $default;
    }

    /**
     * @param string $key
     * @param mixed|null $value
     * @return void
     */
    function setConfigItem($key, $value = null)
    {
        $this->config[$key] = $value;
    }

    /**
     * @param array $config
     * @return void
     */
    function mergeConfig(array $config)
    {
        $this->config = array_merge($this->config, $config);
    }
}