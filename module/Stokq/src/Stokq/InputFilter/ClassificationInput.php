<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Classification;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class ClassificationInput
 * @package Stokq\InputFilter
 */
class ClassificationInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var Classification
     */
    protected $existingRecord;

    /**
     * @param Classification $existingRecord
     */
    function __construct(Classification $existingRecord = null)
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
                FormUtil::noRecordExistsValidator('name', Classification::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [],
        ]);
    }
}
