<?php

namespace Stokq\Stdlib;

/**
 * Class Attributes
 * @package Stokq\Stdlib
 */
class Attributes implements \Serializable
{
    /**
     * @var array
     */
    protected $attributes = [];

    /**
     * @param $str
     * @return Attributes
     */
    public static function fromString($str)
    {
        $attributes = [];
        if (is_string($str) && trim($str)) {
            parse_str($str, $attributes);
        }
        return new self($attributes);
    }

    /**
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $str = '';
        foreach ($this->attributes as $key => $val) {
            if (!is_numeric($key)) {
                $str .= sprintf('%s="%s" ', $key, $val);
            } else {
                $str .= sprintf('%s="%s" ', $val, $val);
            }
        }

        return $str;
    }

    /**
     * @param string $name
     * @return string
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param string $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->attributes[$name]);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        unset($this->attributes[$name]);
    }

    /**
     * @return Attributes
     */
    public function __clone()
    {
        return new self($this->attributes);
    }


    /**
     * @param array $attributes
     */
    public function fromArray($attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->attributes;
    }

    /**
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param string $value
     */
    public function set($key, $value = null)
    {
        $this->attributes[$key] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize($this->attributes);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->attributes = &unserialize($serialized);
    }
}