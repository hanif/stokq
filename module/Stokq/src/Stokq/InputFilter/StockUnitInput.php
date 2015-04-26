<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\StockUnit;
use Stokq\Entity\UnitType;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class StockUnitInput
 * @package Stokq\InputFilter
 */
class StockUnitInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var StockUnit
     */
    protected $existingRecord;

    /**
     * @param StockUnit $existingRecord
     */
    function __construct(StockUnit $existingRecord = null)
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
            'name' => 'name',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::noRecordExistsValidator('name', StockUnit::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'type',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::noRecordExistsValidator('id', UnitType::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), UnitType::class),
            ],
        ]);

        $this->add([
            'name' => 'description',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'ratio',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::floatValidator(),
                FormUtil::greaterThanValidator(0, false),
            ],
            'filters' => [],
        ]);
    }
}