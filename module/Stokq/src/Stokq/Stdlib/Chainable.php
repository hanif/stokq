<?php

namespace Stokq\Stdlib;

/**
 * Class Chainable
 * @package Stokq\Stdlib
 */
class Chainable
{
    /**
     * @param $any
     * @return Chainable
     */
    public static function mock($any)
    {
        if (is_object($any)) {
            return $any;
        }

        return new self();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return '';
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        return $this;
    }
}