<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Category;
use Stokq\Entity\StockItem;
use Stokq\Entity\StorageType;
use Stokq\Entity\UnitType;
use Stokq\Entity\Warehouse;
use Stokq\InputFilter\StockItemInput;

/**
 * Class StockItemController
 * @package Stokq\Controller\Web
 */
class StockItemController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('stock');

        list($unitTypes, $groupedUnits, $flattenUnits) =
            $this->unitTypesToGroupedAndFlattenArray($this->getStockService()->findAllStockUnitGroupedByType());

        $storageTypes = $this->storageTypesToArray($this->mapper(StorageType::class)->all());
        $categories = $this->categoriesToArray($this->mapper(Category::class)->all());
        $warehouses = $this->mapper(Warehouse::class)->all();

        return compact('unitTypes', 'groupedUnits', 'flattenUnits', 'storageTypes', 'categories', 'warehouses');
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function detailAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('stock');
        return [];
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(StockItem::class)->collect($this->stockItemToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(StockItemInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(StockItem::class);
            $stockItem = $mapper->insert($input->getValues());

            if ($this->getRequestPost('add_to_all')) {
                $this->getStockService()->addToAllWarehouse($stockItem);
            }

            return $this->jsonOk($this->stockItemToArray($stockItem), 201);
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateAction()
    {
        $this->ensure('post');

        try {
            /** @var StockItem $stockItem */
            $stockItem = $this->mapper(StockItem::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(StockItemInput::class, $stockItem);
            if ($input->isValid()) {

                // avoid integrity constraint violation
                $stockItem->removeAttachedCategories($this->em());

                $stockItem = $this->mapper(StockItem::class)->getHydrator()->hydrate($input->getValues(), $stockItem);

                // bug: usage unit & storage unit could not be saved
                $stockItem->setUsageUnit($input->getValue('usage_unit'));
                $stockItem->setStorageUnit($input->getValue('storage_unit'));

                $this->persist($stockItem)->commit();
                return $this->jsonOk($this->stockItemToArray($stockItem), 200);
            }
            return $this->jsonError($input->getMessages(), 400);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete');
        $this->mapper(StockItem::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function stockItemToArrayFunc()
    {
        return function(StockItem $obj) {

            $categories = [];
            $stockCategories = [];
            foreach ($obj->getStockCategories() as $stockCategory) {
                $categories[] = $stockCategory->getCategory()->getId();
                $stockCategories[] = [
                    'id' => $stockCategory->getId(),
                    'category' => [
                        'id' => $stockCategory->getCategory()->getId(),
                        'name' => $stockCategory->getCategory()->getName(),
                    ]
                ];
            }

            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'code' => $obj->getCode(),
                'description' => $obj->getDescription(),
                'note' => $obj->getNote(),
                'createdAt' => $obj->getCreatedAt()->format(DATE_ISO8601),
                'storageUnit' => [
                    'id' => $obj->getStorageUnit()->getId(),
                    'name' => $obj->getStorageUnit()->getName(),
                    'type' => $obj->getStorageUnit()->getType()->getId(),
                ],
                'usageUnit' => [
                    'id' => $obj->getUsageUnit()->getId(),
                    'name' => $obj->getUsageUnit()->getName(),
                    'type' => $obj->getUsageUnit()->getType()->getId(),
                ],
                'storageType' => [
                    'id' => $obj->getType() ? $obj->getType()->getId() : null,
                    'name' => $obj->getType() ? $obj->getType()->getName() : null,
                ],
                'stockCategories' => $stockCategories,
                'categories' => $categories,
            ];
        };
    }

    /**
     * @param StockItem $obj
     * @return array
     */
    private function stockItemToArray(StockItem $obj)
    {
        $func = $this->stockItemToArrayFunc();
        return $func($obj);
    }

    /**
     * @param UnitType[] $unitTypes
     * @return array
     */
    private function unitTypesToGroupedAndFlattenArray(array $unitTypes)
    {
        $types = [];
        $grouped = [];
        $flatten = [];

        foreach ($unitTypes as $type) {
            $typeData = [
                'id' => $type->getId(),
                'name' => $type->getName()
            ];

            $types[] = $typeData;
            $grouped[$type->getId()] = $typeData + ['units' => [], ];

            foreach ($type->getStockUnits() as $unit) {
                $unitData = [
                    'id' => $unit->getId(),
                    'name' => $unit->getName(),
                    'description' => $unit->getDescription(),
                    'type' => [
                        'id' => $type->getId(),
                        'name' => $type->getName(),
                    ],
                    'ratio' => $unit->getRatio(),
                ];

                $grouped[$type->getId()]['units'][] = $unitData;
                $flatten[] = $unitData;
            }
        }

        return [$types, $grouped, $flatten];
    }

    /**
     * @param StorageType[] $storageTypes
     * @return array
     */
    private function storageTypesToArray(array $storageTypes)
    {
        $arr = [];
        foreach ($storageTypes as $storageType) {
            $arr[] = [
                'id' => $storageType->getId(),
                'name' => $storageType->getName(),
            ];
        }
        return $arr;
    }

    /**
     * @param Category[] $categories
     * @return array
     */
    private function categoriesToArray(array $categories)
    {
        $arr = [];
        foreach ($categories as $category) {
            $arr[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'color' => $category->getColor(),
            ];
        }
        return $arr;
    }
}