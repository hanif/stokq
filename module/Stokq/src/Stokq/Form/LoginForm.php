<?php

namespace Stokq\Form;

use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class LoginForm
 * @package Stokq\Form
 */
class LoginForm extends Form implements InitializableInterface, InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name' => 'email',
            'type' => 'text',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'type' => 'email',
                'placeholder' => 'Email',
                'autofocus' => true
            ],
        ]);

        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
            ],
            'attributes' => [
                'placeholder' => 'Password'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [

            'email' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::emailValidator(),
                ],
                'filters' => [],
            ],

            'password' => [
                'required' => true,
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::lengthValidator(6),
                ],
                'filters' => [
                    FormUtil::callbackFilter(function($input) {
                        // trim if greater than 100 chars to prevent heavy processing
                        return substr($input, 0, 100);
                    }),
                ],
            ],

        ];
    }
}