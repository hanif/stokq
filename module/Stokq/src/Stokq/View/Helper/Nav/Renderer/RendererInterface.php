<?php

namespace Stokq\View\Helper\Nav\Renderer;

use Stokq\Stdlib\Nav;
use Zend\Form\Element;

/**
 * Interface RendererInterface
 * @package Stokq\View\Helper\Nav\Renderer
 */
interface RendererInterface
{
    /**
     * @param Nav $nav
     * @return string
     */
    public function render(Nav $nav);
}