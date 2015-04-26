<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Outlet;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class SaleInput
 * @package Stokq\InputFilter
 */
class SaleInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getServiceLocator()->get(EntityManager::class);
    }

    public function init()
    {
        $this->add([
            'name' => 'title',
            'validators' => [
                FormUtil::notEmptyValidator(),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'outlet',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', Outlet::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), Outlet::class),
            ],
        ]);

        $this->add([
            'name' => 'currency',
            'validators' => [
                FormUtil::notEmptyValidator(),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'type',
            'validators' => [
                FormUtil::notEmptyValidator(),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'note',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'ordered_at',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::dateValidator('M d, Y'),
            ],
            'filters' => [
                FormUtil::dateFilter('M d, Y'),
            ],
        ]);

        $this->add([
            'name' => 'raw_items',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::callbackValidator(function($input) {
                    if (!is_array($input)) {
                        return false;
                    }

                    foreach ($input as $spec) {
                        switch (true) {
                            case (!is_numeric($spec['quantity']) || $spec['quantity'] < 0):
                            case (!is_numeric($spec['subtotal']) || $spec['subtotal'] < 0):
                            case (!is_numeric($spec['unit_price']) || $spec['unit_price'] < 0):
                            case (!is_numeric($spec['total']) || $spec['total'] < 0):
                                return false;
                        }
                    }

                    return true;
                }, "Qty. dan total harus valid setiap baris item."),
            ],
            'filters' => [
                FormUtil::callbackFilter(function($input) {
                    $return = [];
                    if (is_array($input)) {
                        $defaults = [
                            'menu_price' => null,
                            'item_name' => '',
                            'quantity' => 0,
                            'unit_price' => 0,
                            'subtotal' => 0,
                            'total' => 0
                        ];

                        foreach ($input as $i => $spec) {
                            $spec += $defaults;
                            if ($spec['item_name']) {

                                // convert numbers
                                $spec['quantity']   = preg_replace('/[^0-9]/', null, $spec['quantity']);
                                $spec['unit_price'] = preg_replace('/[^0-9]/', null, $spec['unit_price']);
                                $spec['subtotal']   = preg_replace('/[^0-9]/', null, $spec['subtotal']);
                                $spec['total']      = preg_replace('/[^0-9]/', null, $spec['total']);

                                $return[] = $spec;
                            }
                        }
                    }

                    return $return;
                }),
            ],
        ]);

    }
}
