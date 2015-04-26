<?php

namespace Stokq\Stdlib;

/**
 * Interface ConfigurableInterface
 * @package Stokq\Stdlib
 */
interface ConfigurableInterface
{
    /**
     * @return array
     */
    function getConfig();

    /**
     * @param array $config
     * @return void
     */
    function setConfig(array $config);

    /**
     * @param string $key
     * @param mixed|null $default
     * @return mixed
     */
    function getConfigItem($key, $default = null);

    /**
     * @param string $key
     * @param mixed|null $value
     * @return void
     */
    function setConfigItem($key, $value = null);

    /**
     * @param array $config
     * @return void
     */
    function mergeConfig(array $config);
}