<?php

namespace Stokq\View\Helper;

use Stokq\View\Helper\Form\Element as BootstrapElement;
use Stokq\View\Helper\Form\FieldSet as BootstrapFieldSet;
use Stokq\View\Helper\Form\Form as BootstrapForm;
use Stokq\View\Helper\Form\Renderer\Basic;
use Stokq\View\Helper\Form\Renderer\Horizontal;
use Stokq\View\Helper\Form\Renderer\Inline;
use Stokq\View\Helper\Form\Renderer\Plain;
use Stokq\View\Helper\Form\Renderer\RendererInterface;
use Zend\Form\Element;
use Zend\Form\FieldsetInterface;
use Zend\Form\FormInterface;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormElementErrors;
use Zend\View\Helper\AbstractHelper;

/**
 * Class Form
 * @package Stokq\View\Helper
 */
class Form extends AbstractHelper
{
    const FORM_PLAIN = 'plain';
    const FORM_BASIC = 'basic';
    const FORM_INLINE = 'inline';
    const FORM_HORIZONTAL = 'horizontal';

    /**
     * @var FormElement
     */
    protected $elementHelper;

    /**
     * @var FormElementErrors
     */
    protected $elementErrorsHelper;

    /**
     * @var RendererInterface[]
     */
    protected $bootstrapRenderer = [];

    /**
     * @param mixed $element
     * @param string $type
     * @param array $options
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function __invoke($element, $type = self::FORM_HORIZONTAL, array $options = [])
    {
        if ($element instanceof FormInterface) {
            return new BootstrapForm($element, $this->getBootstrapRendererFor($type, $options));
        } else if ($element instanceof FieldsetInterface) {
            return new BootstrapFieldSet($element, $this->getBootstrapRendererFor($type, $options));
        } else if ($element instanceof Element) {
            return new BootstrapElement($element, $this->getBootstrapRendererFor($type, $options));
        } else {
            throw new \InvalidArgumentException('Invalid type given, expected: Form, Fieldset, or Element instance.');
        }
    }

    /**
     * @param string $type
     * @param array $options
     * @return Basic|Horizontal|Inline
     * @throws \InvalidArgumentException
     */
    protected function getBootstrapRendererFor($type, array $options = [])
    {
        if ($type && isset($this->bootstrapRenderer[$type])) {
            return $this->bootstrapRenderer[$type];
        }

        switch (true) {
            case (strtolower($type) == self::FORM_HORIZONTAL):
                $this->bootstrapRenderer[$type] = new Horizontal($options, $this->getElementHelper(), $this->getElementErrorsHelper());
                return $this->bootstrapRenderer[$type];

            case (strtolower($type) == self::FORM_INLINE):
                $this->bootstrapRenderer[$type] = new Inline($options, $this->getElementHelper(), $this->getElementErrorsHelper());
                return $this->bootstrapRenderer[$type];

            case (strtolower($type) == self::FORM_BASIC):
                $this->bootstrapRenderer[$type] = new Basic($options, $this->getElementHelper(), $this->getElementErrorsHelper());
                return $this->bootstrapRenderer[$type];

            case (strtolower($type) == self::FORM_PLAIN):
                $this->bootstrapRenderer[$type] = new Plain($options, $this->getElementHelper(), $this->getElementErrorsHelper());
                return $this->bootstrapRenderer[$type];

            case ($type instanceof RendererInterface):
                return $type;

            default:
                throw new \InvalidArgumentException(sprintf('Invalid form type: `%s`.', $type));
        }
    }

    /**
     * @return FormElement
     */
    protected function getElementHelper()
    {
        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('form_element');
        }

        if (!$this->elementHelper instanceof FormElement) {
            $this->elementHelper = new FormElement();
        }

        return $this->elementHelper;
    }

    /**
     * @return FormElementErrors
     */
    protected function getElementErrorsHelper()
    {
        if ($this->elementErrorsHelper) {
            return $this->elementErrorsHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementErrorsHelper = $this->view->plugin('form_element_errors');
        }

        if (!$this->elementErrorsHelper instanceof FormElementErrors) {
            $this->elementErrorsHelper = new FormElementErrors();
        }

        return $this->elementErrorsHelper;
    }
}