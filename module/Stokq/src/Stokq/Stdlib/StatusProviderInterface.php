<?php

namespace Stokq\Stdlib;

/**
 * Interface StatusProviderInterface
 * @package Stokq\Stdlib
 */
interface StatusProviderInterface
{
    /**
     * @return string
     */
    public function getStatus();
}