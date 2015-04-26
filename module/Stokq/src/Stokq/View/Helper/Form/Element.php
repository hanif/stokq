<?php

namespace Stokq\View\Helper\Form;

use Stokq\View\Helper\Form\Renderer\RendererInterface;
use Zend\Form\Element as ZendFormElement;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;

/**
 * Class Element
 * @package Stokq\View\Helper\Form
 */
class Element
{
    /**
     * @var ZendFormElement
     */
    protected $element;

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param ZendFormElement $element
     * @param RendererInterface $renderer
     * @throws \InvalidArgumentException
     */
    public function __construct(ZendFormElement $element, RendererInterface $renderer)
    {
        if (($element instanceof FieldsetInterface) || ($element instanceof FormInterface)) {
            throw new \InvalidArgumentException('Forms and fieldsets should use more specific class.');
        }
        $this->element = $element;
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        try {
            return $this->row();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @return string
     */
    public function row()
    {
        return $this->renderer->render($this->element);
    }

    /**
     * @return ZendFormElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return $this
     */
    public function focus()
    {
        $this->element->setAttribute('autofocus', 'autofocus');
        return $this;
    }
}