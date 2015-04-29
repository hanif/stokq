<?php

namespace Stokq\Controller\Web;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\LevelChange;
use Stokq\Entity\Stock;
use Stokq\Entity\StockItem;
use Stokq\Entity\Warehouse;

/**
 * Class StockController
 * @package Stokq\Controller\Web
 */
class StockController extends AuthenticatedController
{
    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function createAction()
    {
        $this->ensure('post');

        $stockItemId = $this->getRequestQuery('stockItem');
        $warehouseId = $this->getRequestQuery('warehouse');

        if ($stockItemId && $warehouseId) {
            /** @var StockItem $stockItem */
            $stockItem = $this->em()->getReference(StockItem::class, $stockItemId);

            /** @var Warehouse $warehouse */
            $warehouse = $this->em()->getReference(Warehouse::class, $warehouseId);

            $stock = new Stock();
            $stock->setStockItem($stockItem);
            $stock->setWarehouse($warehouse);

            try {
                $this->persist($stock)->commit();
                return $this->jsonOk($this->stockToArray($stock), 201);
            } catch (ConstraintViolationException $e) {
                return $this->jsonError(['error_summary' => 'Item sudah pernah ditambahkan di gudang yang dipilih.'], 400);
            }
        }

        return $this->jsonError(['error_summary' => 'ID item atau ID gudang tidak valid.'], 400);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @todo optimize query when fetching storage unit
     */
    public function updateReorderLevelAction()
    {
        $this->ensure('post');

        try {
            /** @var Stock $stock */
            $stock = $this->mapper(Stock::class)->one($this->getRequestPost('id'));
            $reorderLevel = $this->getRequestPost('reorder_level', 0);
            if (!is_numeric($reorderLevel)) {
                throw new \InvalidArgumentException('Angka yang dimasukkan tidak valid.');
            }

            $storageUnit = $stock->getStockItem()->getStorageUnit();

            $stock->setReorderLevel(abs($reorderLevel) * $storageUnit->getRatio());
            $this->persist($stock)->commit();
            return $this->jsonOk([], 200);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError(['errorSummary' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @todo optimize query when fetching storage unit
     */
    public function updateCurrentLevelAction()
    {
        $this->ensure('post');

        try {
            /** @var Stock $stock */
            $stock = $this->mapper(Stock::class)->one($this->getRequestPost('id'));
            $newLevel = $this->getRequestPost('new_level', 0);
            if (!is_numeric($newLevel)) {
                throw new \InvalidArgumentException('Angka yang dimasukkan tidak valid.');
            }

            $storageUnit = $stock->getStockItem()->getStorageUnit();

            $type = $this->getRequestPost('type', LevelChange::TYPE_CORRECTION);
            $note = $this->getRequestPost('note', '');

            $levelChange = new LevelChange();
            $levelChange->setCorrector($this->user());
            $levelChange->setStock($stock);
            $levelChange->setCurrentLevel($stock->getCurrentLevel() * $storageUnit->getRatio());
            $levelChange->setDelta((abs($newLevel) - $stock->getCurrentLevel()) * $storageUnit->getRatio());
            $levelChange->setType($type);
            $levelChange->setNote($note);
            $levelChange->setAuto(false);

            $stock->setCurrentLevel(abs($newLevel) * $storageUnit->getRatio());
            $stock->setLastChange(new \DateTime());
            $this->persist($stock, $levelChange)->commit();
            return $this->jsonOk([], 200);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        } catch (\InvalidArgumentException $e) {
            return $this->jsonError(['errorSummary' => $e->getMessage()], 400);
        }
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listByWarehouseAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Stock::class)->filterAndCollect(function(QueryBuilder $builder) {
                $builder->addSelect('sti, w', 'ssu', 'suu');
                $builder->leftJoin('st.stock_item', 'sti');
                $builder->leftJoin('sti.storage_unit', 'ssu');
                $builder->leftJoin('sti.usage_unit', 'suu');
                $builder->leftJoin('st.warehouse', 'w');
                $builder->where('w.id = :id');
                $builder->setParameter('id', $this->getRequestQuery('id'));
            }, $this->stockToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete', 'post');
        $this->mapper(Stock::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function stockToArrayFunc()
    {
        return function(Stock $obj) {

            return [
                'id' => $obj->getId(),
                'currentUnitPrice' => $obj->getCurrentUnitPrice(),
                'currentLevel' => $obj->getCurrentLevel() / $obj->getStockItem()->getStorageUnit()->getRatio(),
                'reorderLevel' => $obj->getReorderLevel() / $obj->getStockItem()->getStorageUnit()->getRatio(),
                'lastChange' => $obj->getLastChange(),
                'lastPurchase' => $obj->getLastPurchase(),
                'stockItem' => [
                    'id' => $obj->getStockItem()->getId(),
                    'name' => $obj->getStockItem()->getName(),
                    'code' => $obj->getStockItem()->getCode(),
                    'storageUnit' => [
                        'id' => $obj->getStockItem()->getStorageUnit()->getId(),
                        'name' => $obj->getStockItem()->getStorageUnit()->getName(),
                        'ratio' => $obj->getStockItem()->getStorageUnit()->getRatio(),
                    ],
                    'usageUnit' => [
                        'id' => $obj->getStockItem()->getUsageUnit()->getId(),
                        'name' => $obj->getStockItem()->getUsageUnit()->getName(),
                        'ratio' => $obj->getStockItem()->getUsageUnit()->getRatio(),
                    ],
                ],
                'warehouse' => [
                    'id' => $obj->getWarehouse()->getId(),
                    'name' => $obj->getWarehouse()->getName(),
                ],
            ];
        };
    }

    /**
     * @param Stock $obj
     * @return array
     */
    private function stockToArray(Stock $obj)
    {
        $func = $this->stockToArrayFunc();
        return $func($obj);
    }
}