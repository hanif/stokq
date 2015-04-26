<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\Menu;
use Stokq\Entity\Type;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class MenuInput
 * @package Stokq\InputFilter
 */
class MenuInput extends InputFilter implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var Menu
     */
    protected $existingRecord;

    /**
     * @param Menu $existingRecord
     */
    function __construct(Menu $existingRecord = null)
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

    /**
     * @return array
     */
    public function getParentMenuOptions()
    {
        $builder = $this->em()
            ->createQueryBuilder()
            ->select('m')
            ->from(Menu::class, 'm')
            ->where('m.parent is null');

        if ($this->existingRecord) {
            $builder->andWhere('m.id <> :id');
            $builder->setParameter('id', $this->existingRecord->getId());
        }

        /** @var Menu[] $results */
        $results = $builder->getQuery()->getResult();
        $options = [];

        foreach ($results as $result) {
            $options[] = $result->getId();
        }

        return $options;
    }

    public function init()
    {
        $this->add([
            'name' => 'name',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::noRecordExistsValidator('name', Menu::class, $this->em(), ['id' => $this->getRecordIdOrNull()]),
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
            'name' => 'serving_unit',
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
            'name' => 'default_price',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::floatValidator(),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'types',
            'required' => false,
            'validators' => [
                FormUtil::recordExistsValidator('id', Type::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceCollectionFilter($this->em(), Type::class),
            ],
        ]);

        $this->add([
            'name' => 'parent',
            'required' => false,
            'validators' => [
                FormUtil::noRecordExistsValidator('id', Menu::class, $this->em()),
                FormUtil::callbackValidator(function($input) {
                    $id = ($input instanceof Menu) ? $input->getId() : $input;
                    return in_array($id, $this->getParentMenuOptions());
                }),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), Menu::class),
            ],
        ]);
    }
}
