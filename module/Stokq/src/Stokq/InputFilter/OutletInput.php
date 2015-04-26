<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Outlet;
use Stokq\Entity\Warehouse;
use Stokq\Stdlib\FormUtil;
use Stokq\Validator\NoRecordExists;
use Stokq\Validator\RecordExists;
use Zend\Filter\Callback;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class OutletInput
 * @package Stokq\InputFilter
 */
class OutletInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var Outlet
     */
    protected $existingRecord;

    /**
     * @param Outlet $existingRecord
     */
    function __construct(Outlet $existingRecord = null)
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
            'name' => 'name',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::noRecordExistsValidator('name', Outlet::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
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
            'name' => 'address',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'latitude',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'longitude',
            'required' => false,
            'validators' => [],
            'filters' => [],
        ]);
    }
}
