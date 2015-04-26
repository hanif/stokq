<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Type;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class TypeInput
 * @package Stokq\InputFilter
 */
class TypeInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var Type
     */
    protected $existingRecord;

    /**
     * @param Type $existingRecord
     */
    function __construct(Type $existingRecord = null)
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
                FormUtil::noRecordExistsValidator('name', Type::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [],
        ]);
    }
}