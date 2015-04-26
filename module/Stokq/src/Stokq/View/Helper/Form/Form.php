<?php

namespace Stokq\View\Helper\Form;

use Stokq\Stdlib\Attributes;
use Stokq\View\Helper\Form\Renderer\RendererInterface;
use Zend\Form\Element as ZendElement;
use Zend\Form\ElementInterface;
use Zend\Form\FieldsetInterface;
use Zend\Form\Form as ZendForm;
use Zend\Form\FormInterface;

/**
 * Class Form
 * @package Stokq\View\Helper\Form
 */
class Form
{
    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param FormInterface $form
     * @param RendererInterface $renderer
     */
    public function __construct(FormInterface $form, RendererInterface $renderer)
    {
        $this->form = $form;
        if ($form instanceof ZendForm) {
            $form->prepare();
        }
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $markup = [];
        foreach ($this->form->getElements() as $elem) {
            /** @var ElementInterface $elem */
            $markup[] = $this->get($elem->getname());
        }
        return join("\n", $markup);
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return Renderer\RendererInterface
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @return string
     */
    public function open()
    {
        $defaultAttributes = [
            'action' => '',
            'method' => 'post',
        ];

        $attributes = $this->form->getAttributes();
        if (!array_key_exists('id', $attributes) && array_key_exists('name', $attributes)) {
            $attributes['id'] = $attributes['name'];
        }
        $attributes = array_merge($defaultAttributes, $attributes);
        $tag = sprintf('<form %s>', new Attributes($attributes));
        return $tag;
    }

    /**
     * @param $name
     * @return Element|FieldSet
     */
    public function get($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $elem = $this->form->get($name);
        if ($elem instanceof FieldsetInterface) {
            $return = new FieldSet($elem, $this->renderer);
            $this->cache[$name] = $return;
            return $return;
        } else {
            return $this->row($name);
        }
    }

    /**
     * @param $name
     * @return Element
     */
    public function row($name)
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name];
        }

        $elem = $this->form->get($name);
        if ($elem instanceof ZendElement) {
            $return = new Element($elem, $this->renderer);
            $this->cache[$name] = $return;
            return $return;
        }

        throw new \DomainException('Invalid form element object.');
    }

    /**
     * @return string
     */
    public function close()
    {
        return '</form>';
    }
}