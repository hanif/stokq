<?php

namespace Stokq\Stdlib;

/**
 * Class Collection
 * @package Stokq\Stdlib
 */
class Collection
{
    /**
     * @param array|\Traversable $arr
     * @param mixed $element
     * @return bool
     */
    public static function contains($arr, $element)
    {
        if (in_array($element, $arr) OR array_search($element, $arr)) {
            return true;
        }
        return false;
    }

    /**
     * @param array|\Traversable $arr
     * @param callable $callback
     * @return \Generator
     */
    public static function each($arr, callable $callback)
    {
        foreach ($arr as $key => $val) {
            yield $callback($val, $key);
        }
    }

    /**
     * @param array|\Traversable $arr
     * @param callable $callback
     */
    public static function apply($arr, callable $callback)
    {
        foreach ($arr as $key => $val) {
            $callback($val, $key, $arr);
        }
    }

    /**
     * @param array|\Traversable $arr
     * @param callable $callback
     * @return array
     */
    public static function collect($arr, callable $callback)
    {
        $data = [];
        $ref =& $data;
        foreach ($arr as $key => $val) {
            $result = $callback($val, $key, $ref);
            $data[] = $result;
        }
        return $data;
    }

    /**
     * @param array|\Traversable $arr
     * @param callable $callback
     * @return array
     */
    public static function process($arr, callable $callback)
    {
        $data = [];
        $ref =& $data;
        foreach ($arr as $key => $val) {
            $callback($val, $key, $ref);
        }
        return $data;
    }

    /**
     * @param array|\Traversable $arr
     * @param string $key
     * @param string $val
     * @return array
     */
    public static function assoc($arr, $key, $val)
    {
        $data = [];
        foreach ($arr as $elem) {
            if (is_array($elem) || ($elem instanceof \ArrayObject)) {
                if (isset($elem[$key]) && isset($elem[$val])) {
                    $data[$elem[$key]] = $elem[$val];
                }
            } else if (is_object($elem)) {
                $keyMethod = sprintf('get%s', $key);
                $valueMethod = sprintf('get%s', $val);
                if (method_exists($elem, $keyMethod) && method_exists($elem, $valueMethod)) {
                    $data[$elem->{$keyMethod}()] = $elem->{$valueMethod}();
                }
            }
        }
        return $data;
    }
}