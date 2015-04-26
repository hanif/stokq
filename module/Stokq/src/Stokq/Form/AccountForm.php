<?php

namespace Stokq\Form;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\User;
use Stokq\Stdlib\FormUtil;
use Zend\Form\Form;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use Zend\Stdlib\InitializableInterface;

/**
 * Class AccountForm
 * @package Stokq\Form
 */
class AccountForm extends Form implements InitializableInterface, InputFilterProviderInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var User[]
     */
    private $users;

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getServiceLocator()->get(EntityManager::class);
    }

    /**
     * @return \Stokq\Entity\User[]
     */
    public function getUserOptions()
    {
        if (!$this->users) {
            /** @var User[] $users */
            $users = $this->em()->getRepository(User::class)->findAll();

            foreach ($users as $user) {
                $this->users[$user->getId()] = $user->getName();
            }
        }

        return $this->users;
    }

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
            'name' => 'billing_user',
            'type' => 'select',
            'options' => [
                'label' => 'Billing User',
                'options' => $this->getUserOptions(),
                'disable_inarray_validator' => true,
            ],
            'attributes' => [],
        ]);

        $this->add([
            'name' => 'address',
            'type' => 'textarea',
            'options' => [
                'label' => 'Alamat',
            ],
            'attributes' => [
                'placeholder' => 'Alamat',
                'rows' => 4,
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

            'billing_user' => [
                'validators' => [
                    FormUtil::notEmptyValidator(),
                ],
                'filters' => [
                    FormUtil::entityReferenceFilter($this->em(), User::class),
                ],
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
        ];
    }
}