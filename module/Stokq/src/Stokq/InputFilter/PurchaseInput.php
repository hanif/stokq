<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Purchase;
use Stokq\Entity\Warehouse;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class PurchaseInput
 * @package Stokq\InputFilter
 */
class PurchaseInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var array
     */
    private static $statusOptions = [
        Purchase::STATUS_PLANNED,
        Purchase::STATUS_IN_PROGRESS,
        Purchase::STATUS_CANCELED,
        Purchase::STATUS_DELIVERED,
        Purchase::STATUS_RETURNED,
    ];

    /**
     * @return array
     */
    public static function getStatusOptions()
    {
        return self::$statusOptions;
    }

    /**
     * @var Purchase
     */
    protected $existingRecord;

    /**
     * @param Purchase $existingRecord
     */
    function __construct(Purchase $existingRecord = null)
    {
        $this->existingRecord = $existingRecord;
    }

    /**
     * @return int|null
     */
    public function getRecordIdOrNull()
    {
        return $this->existingRecord ? $this->existingRecord->getId() : null;
    }

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
            'name' => 'warehouse',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', Warehouse::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), Warehouse::class),
            ],
        ]);

        $this->add([
            'name' => 'supplier_name',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'currency',
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
            'name' => 'po_number',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'status',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::inArrayValidator(self::getStatusOptions()),
                FormUtil::workflowValidator(self::getStatusOptions(), $this->getRecordIdOrNull(), true),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'ordered_at',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::dateValidator('M d, Y'),
            ],
            'filters' => [
                FormUtil::dateFilter('M d, Y', true),
            ],
        ]);

        $this->add([
            'name' => 'delivered_at',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::dateValidator('M d, Y'),
            ],
            'filters' => [
                FormUtil::dateFilter('M d, Y', true),
            ],
        ]);

        $this->add([
            'name' => 'raw_items',
            'validators' => [
                FormUtil::notEmptyValidator("Belum ada item yang ditambahkan."),
                FormUtil::callbackValidator(function($input) {
                    if (!is_array($input)) {
                        return false;
                    }

                    foreach ($input as $spec) {
                        switch (true) {
                            case (trim($spec['item_name']) == ''):
                            case (!is_numeric($spec['quantity']) || $spec['quantity'] < 0):
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
                            'stock'      => null,
                            'item_name'  => '',
                            'quantity'   => 0,
                            'unit'       => 0,
                            'unit_price' => 0,
                            'subtotal'   => 0,
                            'total'      => 0,
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
