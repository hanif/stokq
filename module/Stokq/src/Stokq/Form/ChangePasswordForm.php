<?php

namespace Stokq\Form;

use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class ChangePasswordForm
 * @package Stokq\Form
 */
class ChangePasswordForm extends Form implements InitializableInterface, InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name' => 'old_password',
            'type' => 'password',
            'options' => [
                'label' => 'Password Saat Ini',
            ],
            'attributes' => [
                'placeholder' => 'Password Saat Ini'
            ],
        ]);

        $this->add([
            'name' => 'new_password',
            'type' => 'password',
            'options' => [
                'label' => 'Password Baru',
                'help' => 'Catatan: Password bersifat case sensitive.',
            ],
            'attributes' => [
                'placeholder' => 'Password Baru'
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

            'old_password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'new_password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::lengthValidator(6, 100),
                ],
                'filters' => [],
            ],

            'confirm_password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::identicalValidator('new_password', 'Password tidak sama dengan diatas.'),
                ],
                'filters' => [],
            ],

        ];
    }
}