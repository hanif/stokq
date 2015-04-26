<?php

namespace Stokq\Stdlib;

/**
 * Class ExceptionHandler
 * @package Stokq\Stdlib
 */
class ExceptionHandler
{
    /**
     * @var array|callable[]
     */
    protected $exceptionHandlers = [];

    /**
     * @param $exception
     * @param callable $callback
     */
    public function setHandler($exception, callable $callback)
    {
        $this->exceptionHandlers[$exception] = $callback;
    }

    /**
     * @param $exception
     * @return bool
     */
    public function canHandle($exception)
    {
        if ($exception instanceof \Exception) {
            $exception = get_class($exception);
        }
        return isset($this->exceptionHandlers[$exception]);
    }

    /**
     * @param \Exception $exception
     * @return mixed
     */
    public function handleException(\Exception $exception)
    {
        if ($this->canHandle(get_class($exception))) {
            $handler = $this->exceptionHandlers[get_class($exception)];
            return $handler($exception);
        }

        throw new \RuntimeException(sprintf('Could not handle exception with type of: ', get_class($exception)));
    }

    /**
     * @param \Exception $exception
     * @return mixed
     * @throws \Exception
     */
    public function tryHandle(\Exception $exception)
    {
        if ($this->canHandle($exception)) {
            return $this->handleException($exception);
        }

        throw $exception;
    }
} 