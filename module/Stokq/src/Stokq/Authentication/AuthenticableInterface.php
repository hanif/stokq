<?php

namespace Stokq\Authentication;

/**
 * Interface AuthenticableInterface
 * @package Stokq\Authentication
 */
interface AuthenticableInterface
{
    /**
     * @return string
     */
    public function getIdentity();

    /**
     * @return string
     */
    public function getCredentials();

    /**
     * @return bool
     */
    public function isEnabled();
}