<?php

namespace Stokq\Form;

use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class CreatePasswordForm
 * @package Stokq\Form
 */
class CreatePasswordForm extends Form implements InitializableInterface, InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name' => 'password',
            'type' => 'password',
            'options' => [
                'label' => 'Password',
                'help' => 'Catatan: Password bersifat case sensitive.',
            ],
            'attributes' => [
                'placeholder' => 'Password'
            ],
        ]);

        $this->add([
            'name' => 'confirm_password',
            'type' => 'password',
            'options' => [
                'label' => 'Konfirmasi',
            ],
            'attributes' => [
                'placeholder' => 'Konfirmasi Password'
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [

            'password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::lengthValidator(6, 100),
                ],
                'filters' => [],
            ],

            'confirm_password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::identicalValidator('password', 'Password tidak sama dengan diatas.'),
                ],
                'filters' => [],
            ],

        ];
    }
}