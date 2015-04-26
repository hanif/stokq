<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\StorageType;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class StorageTypeInput
 * @package Stokq\InputFilter
 */
class StorageTypeInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var StorageType
     */
    protected $existingRecord;

    /**
     * @param StorageType $existingRecord
     */
    function __construct(StorageType $existingRecord = null)
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
                FormUtil::noRecordExistsValidator('name', StorageType::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [],
        ]);
    }
}