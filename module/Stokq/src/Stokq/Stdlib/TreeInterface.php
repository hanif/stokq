<?php

namespace Stokq\Stdlib;

/**
 * Interface TreeInterface
 * @package Stokq\Stdlib
 */
interface TreeInterface
{
    /**
     * @return bool
     */
    public function isRoot();

    /**
     * @return bool
     */
    public function getAncestor();

    /**
     * @return bool
     */
    public function getDescendants();
}