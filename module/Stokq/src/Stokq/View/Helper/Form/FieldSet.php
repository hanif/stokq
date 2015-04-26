<?php

namespace Stokq\View\Helper\Form;

use Stokq\Stdlib\Attributes;
use Stokq\View\Helper\Form\Renderer\RendererInterface;
use Zend\Form\ElementInterface;
use Zend\Form\Fieldset as ZendFieldSet;
use Zend\Form\FieldsetInterface;

/**
 * Class FieldSet
 * @package Stokq\View\Helper\Form
 */
class FieldSet
{
    /**
     * @var FieldsetInterface
     */
    protected $fieldSet;

    /**
     * @var Renderer\RendererInterface
     */
    protected $renderer;

    /**
     * @var array
     */
    protected $cache = [];

    /**
     * @param FieldsetInterface $fieldSet
     * @param RendererInterface $renderer
     */
    public function __construct(FieldsetInterface $fieldSet, RendererInterface $renderer)
    {
        $this->fieldSet = $fieldSet;
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $markup = [];
        foreach ($this->fieldSet->getElements() as $elem) {
            /** @var ElementInterface $elem */
            $markup[] = $this->get($elem->getname());
        }
        return join("\n", $markup);
    }

    /**
     * @return FieldsetInterface
     */
    public function getFieldSet()
    {
        return $this->fieldSet;
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
        $attributes = $this->fieldSet->getAttributes();
        $legend = $this->fieldSet->getLabel();
        $tag = sprintf('<fieldset %s><legend>%s</legend>', new Attributes($attributes), $legend);
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

        $elem = $this->fieldSet->get($name);
        if ($elem instanceof FieldsetInterface) {
            $return = new static($elem, $this->renderer);
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

        $elem = $this->fieldSet->get($name);
        if ($elem instanceof ZendFieldSet) {
            $return = new Element($elem, $this->renderer);
            $this->cache[$name] = $return;
            return $return;
        }

        throw new \DomainException('Invalid form fieldset object');
    }

    /**
     * @return string
     */
    public function close()
    {
        return '</fieldset>';
    }
}