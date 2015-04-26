<?php

namespace Stokq\View\Helper\Form\Renderer;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormElementErrors;

/**
 * Class Plain
 * @package Stokq\View\Helper\Form\Renderer
 */
class Plain implements RendererInterface
{
    /**
     * @var FormElement
     */
    protected $elementHelper;

    /**
     * @var FormElementErrors
     */
    protected $errorHelper;

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var array
     */
    protected $excludedFormControlClass = ['hidden', 'checkbox', 'multi_checkbox', 'radio'];

    /**
     * @var string
     */
    protected $template = '{{before}}{{elem_markup}}{{error_messages}}{{after}}';

    /**
     * @param array $options
     * @param FormElement $elementHelper
     * @param FormElementErrors $errorHelper
     */
    public function __construct(array $options = [], FormElement $elementHelper, FormElementErrors $errorHelper)
    {
        $this->options = $options;
        $this->elementHelper = $elementHelper;
        $this->errorHelper = $errorHelper;
    }

    /**
     * @param Element $element
     * @return string
     */
    public function render(Element $element)
    {
        if ($element instanceof Element\Hidden) {
            return $this->elementHelper->render($element);
        } else {
            $replaces = [
                '{{before}}' => '',
                '{{after}}' => '',
                '{{error_messages}}' => '',
                '{{elem_markup}}' => '',
            ];

            $elemClass = $element->getAttribute('class');
            $elemType = $element->getAttribute('type');
            $placeholder = $element->getAttribute('placeholder');
            if (!$placeholder) {
                $element->setAttribute('placeholder', $element->getLabel());
            }

            if (!in_array($elemType, $this->excludedFormControlClass)) {
                $element->setAttribute('class', join(' ', ['form-control', $elemClass]));
            }

            if (method_exists($element, 'getInputSpecification')) {
                $spec = $element->getInputSpecification();
                if (isset($spec['required']) && $spec['required']) {
                    $element->setAttribute('required', 'required');
                }
            }

            if ($errorMessages = $this->errorHelper->render($element)) {
                $replaces['{{error_messages}}'] = sprintf('<div class="error-messages">%s</div>', $errorMessages);
            }

            if ($before = $element->getOption('before')) {
                $replaces['{{before}}'] = $before;
            }

            if ($after = $element->getOption('after')) {
                $replaces['{{after}}'] = $after;
            }

            $replaces['{{elem_markup}}'] = $this->elementHelper->render($element);
            return strtr($this->template, $replaces);
        }
    }
}
