<?php

namespace Stokq\Form;

use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class FirstRunForm
 * @package Stokq\Form
 */
class FirstRunForm extends Form implements InitializableInterface, InputFilterProviderInterface
{
    /**
     *
     */
    public function init()
    {
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Nama Bisnis',
            ],
            'attributes' => [
                'placeholder' => 'Nama Bisnis',
                'autofocus' => true
            ],
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'textarea',
            'options' => [
                'label' => 'Alamat',
            ],
            'attributes' => [
                'placeholder' => 'Alamat',
                'rows' => 11,
            ],
        ]);

        $this->add([
            'name' => 'phone',
            'type' => 'text',
            'options' => [
                'label' => 'Kontak/Telp.',
            ],
            'attributes' => [
                'placeholder' => 'Kontak/Telp.',
                'autofocus' => true
            ],
        ]);

        $this->add([
            'name' => 'fax',
            'type' => 'text',
            'options' => [
                'label' => 'Fax',
            ],
            'attributes' => [
                'placeholder' => 'Fax',
                'autofocus' => true
            ],
        ]);

        $this->add([
            'name' => 'email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'placeholder' => 'Email',
            ],
        ]);

        $this->add([
            'name' => 'website',
            'type' => 'text',
            'options' => [
                'label' => 'Website',
            ],
            'attributes' => [
                'placeholder' => 'Website',
            ],
        ]);

        $this->add([
            'name' => 'facebook',
            'type' => 'text',
            'options' => [
                'label' => 'Facebook',
            ],
            'attributes' => [
                'placeholder' => 'Facebook',
            ],
        ]);

        $this->add([
            'name' => 'twitter',
            'type' => 'text',
            'options' => [
                'label' => 'Twitter',
            ],
            'attributes' => [
                'placeholder' => 'Twitter',
            ],
        ]);

        $this->add([
            'name' => 'default_timezone',
            'type' => 'select',
            'options' => [
                'label' => 'Zona Waktu',
                'options' => FormUtil::getTimezoneOptions(),
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'default_currency',
            'type' => 'select',
            'options' => [
                'label' => 'Mata Uang',
                'options' => FormUtil::getCurrencyOptions(),
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'default_locale',
            'type' => 'select',
            'options' => [
                'label' => 'Lokal',
                'options' => FormUtil::getCountryOptions(),
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'max_users',
            'type' => 'number',
            'options' => [
                'label' => 'Max. User',
                'step' => 1,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'max_outlets',
            'type' => 'number',
            'options' => [
                'label' => 'Max. Outlet',
                'step' => 1,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'max_warehouses',
            'type' => 'number',
            'options' => [
                'label' => 'Max. Gudang',
                'step' => 1,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'max_menus',
            'type' => 'number',
            'options' => [
                'label' => 'Max. Menu',
                'step' => 1,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'max_stock_items',
            'type' => 'number',
            'options' => [
                'label' => 'Max. Item Stok',
                'step' => 1,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'next_due_date',
            'type' => 'date',
            'options' => [
                'label' => 'Tgl. Jatuh Tempo',
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'user_name',
            'type' => 'text',
            'options' => [
                'label' => 'Nama',
            ],
            'attributes' => [
                'placeholder' => 'Nama',
            ],
        ]);

        $this->add([
            'name' => 'user_email',
            'type' => 'email',
            'options' => [
                'label' => 'Email',
            ],
            'attributes' => [
                'placeholder' => 'Email',
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

        $this->add([
            'name' => 'confirm_password',
            'type' => 'password',
            'options' => [
                'label' => 'Konfirmasi Password',
            ],
            'attributes' => [
                'placeholder' => 'Konfirmasi Password'
            ],
        ]);
    }

    /**
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return [

            'name' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'address' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'phone' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'fax' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'email' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'website' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'facebook' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'twitter' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'default_timezone' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'default_currency' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'default_locale' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'max_users' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'max_warehouses' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'max_outlets' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'max_menus' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'max_stock_items' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'next_due_date' => [
                'required' => false,
                'validators' => [
                    FormUtil::dateValidator('Y-m-d'),
                ],
                'filters' => [],
            ],

            'user_name' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'user_email' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'confirm_password' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                    FormUtil::identicalValidator('password'),
                ],
                'filters' => [],
            ],

        ];
    }
}