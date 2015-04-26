<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\User;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class UserInput
 * @package Stokq\InputFilter
 */
class UserInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var User
     */
    protected $existingRecord;

    /**
     * @param User $existingRecord
     */
    function __construct(User $existingRecord = null)
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
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'email',
            'required' => true,
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::emailValidator(),
                FormUtil::noRecordExistsValidator('email', User::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'contact_no',
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
    }
}