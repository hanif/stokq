<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Category;
use Stokq\Entity\StockItem;
use Stokq\Entity\StockUnit;
use Stokq\Entity\StorageType;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class StockItemInput
 * @package Stokq\InputFilter
 */
class StockItemInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var StockItem
     */
    protected $existingRecord;

    /**
     * @param StockItem $existingRecord
     */
    function __construct(StockItem $existingRecord = null)
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
            'name' => 'storage_unit',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', StockUnit::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), StockUnit::class),
            ],
        ]);

        $this->add([
            'name' => 'usage_unit',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', StockUnit::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), StockUnit::class),
            ],
        ]);

        $this->add([
            'name' => 'categories',
            'required' => false,
            'validators' => [
                FormUtil::recordExistsValidator('id', Category::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceCollectionFilter($this->em(), Category::class),
            ],
        ]);

        $this->add([
            'name' => 'type',
            'required' => false,
            'validators' => [
                FormUtil::recordExistsValidator('id', StorageType::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), StorageType::class),
            ],
        ]);

        $this->add([
            'name' => 'code',
            'required' => false,
            'validators' => [
                FormUtil::noRecordExistsValidator('code', StockItem::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [
                FormUtil::callbackFilter(function($input) {
                    return trim($input) ? $input : null;
                })
            ],
        ]);

        $this->add([
            'name' => 'name',
            'validators' => [
                FormUtil::notEmptyValidator(),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'description',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'note',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);
    }
}