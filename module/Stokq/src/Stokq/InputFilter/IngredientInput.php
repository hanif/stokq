<?php

namespace Stokq\InputFilter;

use Doctrine\ORM\EntityManager;
use Stokq\Entity\IngredientType;
use Stokq\Entity\Menu;
use Stokq\Entity\StockItem;
use Stokq\Stdlib\FormUtil;
use Zend\InputFilter\InputFilter;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class IngredientInput
 * @package Stokq\InputFilter
 */
class IngredientInput extends InputFilter implements ServiceLocatorAwareInterface
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
            'name' => 'type',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', IngredientType::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), IngredientType::class),
            ],
        ]);

        $this->add([
            'name' => 'menu',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', Menu::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), Menu::class),
            ],
        ]);

        $this->add([
            'name' => 'stock_item',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::recordExistsValidator('id', StockItem::class, $this->em()),
            ],
            'filters' => [
                FormUtil::entityReferenceFilter($this->em(), StockItem::class),
            ],
        ]);

        $this->add([
            'name' => 'quantity',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::floatValidator(),
                FormUtil::greaterThanValidator(0, true),
            ],
            'filters' => [],
        ]);

        $this->add([
            'name' => 'qty_price',
            'validators' => [
                FormUtil::notEmptyValidator(),
                FormUtil::floatValidator(),
                FormUtil::greaterThanValidator(0, true),
            ],
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
