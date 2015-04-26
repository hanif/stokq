<?php

namespace Stokq\View\Helper\Form\Renderer;

use Zend\Form\Element;
use Zend\Form\View\Helper\FormElement;
use Zend\Form\View\Helper\FormElementErrors;

/**
 * Class Basic
 * @package Stokq\View\Helper\Form\Renderer
 */
class Basic implements RendererInterface
{
    /**
     * @var int
     */
    protected static $suffix = 2;

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
    protected $template =
        '{{before}}
        <div class="form-group {{row_class}}">
            <label class="control-label {{label_class}}" for="{{elem_id}}">
                {{label_text}}
            </label>
            {{elem_markup}}
            {{feedback}}
            {{help_text}}
            {{error_messages}}
        </div>
        {{after}}';

    /**
     * @param array $options
     * @param FormElement $elementHelper
     * @param FormElementErrors $errorHelper
     */
    public function __construct(array $options = [], FormElement $elementHelper, FormElementErrors $errorHelper)
    {
        $defaultOptions = [
            'row_class' => '',
        ];
        $this->options = $defaultOptions + $options;
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
                '{{row_class}}' => $this->options['row_class'],
                '{{label_text}}' => $element->getLabel(),
                '{{label_class}}' => '',
                '{{elem_markup}}' => '',
                '{{error_messages}}' => '',
                '{{help_text}}' => '',
                '{{feedback}}' => '',
                '{{elem_id}}' => '',
            ];

            $elemClass = $element->getAttribute('class');
            $elemType = $element->getAttribute('type');

            if (!in_array($elemType, $this->excludedFormControlClass)) {
                $element->setAttribute('class', join(' ', ['form-control', $elemClass]));
            }

            if (method_exists($element, 'getInputSpecification')) {
                $spec = $element->getInputSpecification();
                if (isset($spec['required']) && $spec['required']) {
                    $replaces['{{label_class}}'] .= ' required';
                    $element->setAttribute('required', 'required');
                }
            }

            if ($errorMessages = $this->errorHelper->render($element)) {
                $replaces['{{error_messages}}'] = sprintf('<div class="error-messages">%s</div>', $errorMessages);
            }

            if ($replaces['{{error_messages}}']) {
                $replaces['{{row_class}}'] .= ' has-error';
            }

            if ($feedback = $element->getOption('feedback')) {
                $replaces['{{feedback}}'] = sprintf('<span class="fa fa-%s form-control-feedback"></span>', $feedback);
                $replaces['{{row_class}}'] .= ' has-feedback';
            }

            if ($helpText = $element->getOption('help')) {
                $replaces['{{help_text}}'] = sprintf('<div class="help-text help-block">%s</div>', $helpText);
            }

            if ($before = $element->getOption('before')) {
                $replaces['{{before}}'] = $before;
            }

            if ($after = $element->getOption('after')) {
                $replaces['{{after}}'] = $after;
            }

            if (!$element->getAttribute('id')) {
                $element->setAttribute('id', sprintf('input-id-%s', crc32(self::$suffix++)));
            }

            $replaces['{{elem_id}}'] = $element->getAttribute('id');
            $replaces['{{elem_markup}}'] = $this->elementHelper->render($element);
            return strtr($this->template, $replaces);
        }
    }
}
