<?php

namespace Stokq\View\Helper\Form\Renderer;

use Zend\Form\Element;

/**
 * Interface RendererInterface
 * @package Stokq\View\Helper\Form\Renderer
 */
interface RendererInterface
{
    /**
     * @param Element $element
     * @return string
     */
    public function render(Element $element);
}