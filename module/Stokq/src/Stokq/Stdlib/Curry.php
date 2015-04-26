<?php

namespace Stokq\Stdlib;

use InvalidArgumentException;
use ReflectionFunction;

/**
 * Class Curry
 * @package Stokq\Stdlib
 */
class Curry
{
    /**
     * @var ReflectionFunction
     */
    private $ref;

    /**
     * @var int
     */
    private $numParams;

    /**
     * @var array
     */
    private $args = [];

    /**
     * @param callable $func
     * @return Curry
     * @throws \DomainException
     */
    public static function create(callable $func)
    {
        $ref = new ReflectionFunction($func);
        $numParams = $ref->getNumberOfParameters();
        if ($numParams === 0) {
            throw new \DomainException('Function does not have parameters.');
        }
        return new self($ref, $numParams);
    }

    /**
     * @param ReflectionFunction $ref
     * @param $numParams
     * @param array $args
     */
    private function __construct(ReflectionFunction $ref, $numParams, array $args = [])
    {
        $this->ref = $ref;
        $this->numParams = $numParams;
        $this->args = $args;
    }

    /**
     * @param mixed $args
     * @return $this|mixed
     * @throws InvalidArgumentException
     */
    public function __invoke(...$args)
    {
        $merged = array_merge($this->args, $args);
        if ($this->numParams == count($merged)) {
            return $this->ref->invokeArgs($merged);
        } else if ($this->numParams > count($merged)) {
            return new self($this->ref, $this->numParams, $merged);
        } else {
            throw new InvalidArgumentException(
                sprintf('Function expected to have %d arguments, %d passed.', $this->numParams, count($merged)));
        }
    }
}