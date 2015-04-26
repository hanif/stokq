<?php

namespace Stokq\Form;

use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\Stdlib\InitializableInterface;

/**
 * Class HelpForm
 * @package Stokq\Form
 */
class HelpForm extends Form implements InitializableInterface, InputFilterProviderInterface
{
    public function init()
    {
        $this->add([
            'name' => 'name',
            'type' => 'text',
            'options' => [
                'label' => 'Nama',
            ],
            'attributes' => [
                'placeholder' => 'Nama',
                'readonly' => true,
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
                'readonly' => true,
            ],
        ]);

        $this->add([
            'name' => 'contact_no',
            'type' => 'text',
            'options' => [
                'label' => 'Kontak/Telp.',
            ],
            'attributes' => [
                'placeholder' => 'Kontak/Telp.',
                'readonly' => true,
            ],
        ]);

        $this->add([
            'name' => 'type',
            'type' => 'select',
            'options' => [
                'label' => 'Jenis Pesan',
                'options' => [
                    'Report an Error' => 'Laporan Error',
                    'Feature Request' => 'Request Fitur Baru',
                    'Need Help' => 'Butuh Bantuan',
                    'Other' => 'Lain-lain',
                ],
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'severity',
            'type' => 'select',
            'options' => [
                'label' => 'Level',
                'options' => [
                    'Low' => 'Rendah',
                    'Medium' => 'Medium',
                    'High' => 'Tinggi',
                    'Critical' => 'Kritis',
                ],
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'need_reply',
            'type' => 'select',
            'options' => [
                'label' => 'Butuh Jawaban?',
                'options' => [
                    'No' => 'Tidak',
                    'Yes, By Email' => 'Ya, Lewat Email',
                    'Yes, By Phone (anytime)' => 'Ya, Lewat Telpon (kapan saja)',
                    'Yes, By Phone (office hours)' => 'Ya, Lewat Telpon (jam kantor)',
                ],
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'message',
            'type' => 'textarea',
            'options' => [
                'label' => 'Pesan',
            ],
            'attributes' => [
                'placeholder' => 'Pesan',
                'rows' => 7
            ],
        ]);
    }

    public function getInputFilterSpecification()
    {
        return [

            'name' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'email' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'contact_no' => [
                'required' => false,
                'validators' => [],
                'filters' => [],
            ],

            'type' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'severity' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'need_reply' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

            'message' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [],
            ],

        ];
    }
}