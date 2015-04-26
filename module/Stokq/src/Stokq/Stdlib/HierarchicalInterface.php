<?php

namespace Stokq\Stdlib;

/**
 * Interface HierarchicalInterface
 * @package Stokq\Stdlib
 */
interface HierarchicalInterface
{
    /**
     * @return mixed
     */
    public function getParent();

    /**
     * @return mixed
     */
    public function getChildren();
}