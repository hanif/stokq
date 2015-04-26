<?php

namespace Stokq\Service;

use Doctrine\DBAL\Connection;
use Stokq\Entity\Category;
use Stokq\Entity\LevelChange;
use Stokq\Entity\MenuPrice;
use Stokq\Entity\OutletSale;
use Stokq\Entity\Purchase;
use Stokq\Entity\Sale;
use Stokq\Entity\Stock;
use Stokq\Entity\StockItem;
use Stokq\Entity\StockUnit;
use Stokq\Entity\StorageType;
use Stokq\Entity\UnitType;
use Stokq\Entity\User;
use Stokq\Entity\Warehouse;
use Stokq\Stdlib\DataMapper;

/**
 * Class StockService
 * @package Stokq\Service
 */
class StockService extends AbstractService
{
    /**
     * @param DataMapper $mapper
     * @return Category[]
     */
    public function createDefaultCategory(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == Category::class);
        return $mapper->createMany([
            ['name' => 'Buah/Sayur'],
            ['name' => 'Daging/Ikan'],
            ['name' => 'Bahan Baku'],
            ['name' => 'Bumbu/Penyedap'],
            ['name' => 'Lainnya'],
        ]);
    }

    /**
     * @param DataMapper $mapper
     * @return StorageType[]
     */
    public function createDefaultStorageType(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == StorageType::class);
        return $mapper->createMany([
            ['name' => 'Fresh'],
            ['name' => 'Kering'],
            ['name' => 'Setengah Kering'],
            ['name' => 'Beku'],
            ['name' => 'Kalengan/Botol'],
            ['name' => 'Lainnya'],
        ]);
    }

    /**
     * @param DataMapper $mapper
     * @return UnitType[]
     */
    public function createDefaultUnitType(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == UnitType::class);
        return $mapper->createMany([
            ['name' => 'Berat'],
            ['name' => 'Volume'],
            ['name' => 'Unit'],
        ]);
    }

    /**
     * @param DataMapper $mapper
     * @param UnitType[] $types
     * @return StockUnit[]
     */
    public function createDefaultUnit(DataMapper $mapper, array $types)
    {
        $this->assert($mapper->getEntityClass() == StockUnit::class);

        $unitGroup = [
            'Berat' => [
                ['name' => 'kg',      'description' => 'Kilogram',   'ratio' => 1],
                ['name' => 'g',       'description' => 'Gram',       'ratio' => 0.001],
                ['name' => 'mg',      'description' => 'Milligram',  'ratio' => 0.000001],
                ['name' => 'pon',     'description' => 'Pon',        'ratio' => 0.453592],
                ['name' => 'ons',     'description' => 'Ons',        'ratio' => 0.0283495],
                ['name' => 'kuintal', 'description' => 'Kuintal',    'ratio' => 100],
                ['name' => 'ton',     'description' => 'Ton',        'ratio' => 1000],
            ],
            'Volume' => [
                ['name' => 'l',       'description' => 'Liter',      'ratio' => 1],
                ['name' => 'ml',      'description' => 'Milliliter', 'ratio' => 0.001],
                ['name' => 'gallon',  'description' => 'Gallon',     'ratio' => 19],
            ],
            'Unit' => [
                ['name' => 'unit',    'description' => 'Unit',       'ratio' => 1],
                ['name' => 'lusin',   'description' => 'Lusin',      'ratio' => 12],
                ['name' => 'box',     'description' => 'Box',        'ratio' => 1],
                ['name' => 'btl',     'description' => 'Botol',      'ratio' => 1],
                ['name' => 'kaleng',  'description' => 'Kaleng',     'ratio' => 1],
                ['name' => 'bungkus', 'description' => 'Bungkus',    'ratio' => 1],
                ['name' => 'pak',     'description' => 'Pak',        'ratio' => 1],
                ['name' => 'sak',     'description' => 'Sak',        'ratio' => 1],
                ['name' => 'pcs',     'description' => 'Pcs',        'ratio' => 1],
                ['name' => 'saset',   'description' => 'Saset',      'ratio' => 1],
                ['name' => 'item',    'description' => 'Item',       'ratio' => 1],
                ['name' => 'buah',    'description' => 'Buah',       'ratio' => 1],
                ['name' => 'biji',    'description' => 'Biji',       'ratio' => 1],
                ['name' => 'sdm',     'description' => 'sdm',        'ratio' => 1],
                ['name' => 'sdt',     'description' => 'sdt',        'ratio' => 1],
            ],
        ];

        $units = [];
        foreach ($types as $type) {
            if (isset($unitGroup[$type->getName()])) {
                foreach ($unitGroup[$type->getName()] as $spec) {
                    $units[] = $mapper->create($spec + compact('type'));
                }
            }
        }
        return $units;
    }

    /**
     * @return UnitType[]
     */
    public function findAllStockUnitGroupedByType()
    {
        $query = $this->em()->createQuery("select" . " t, s from ent:UnitType t left join t.stock_units s");
        return $query->getResult();
    }

    /**
     * @param StockItem $item
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addToAllWarehouse(StockItem $item)
    {
        $this->assert(intval($item->getId()) > 0);

        $sql = "insert" . " into stocks (warehouse_id, stock_item_id, current_level)
                select id, %d, 0
                from warehouses";

        $this->db()->executeQuery(sprintf($sql, $item->getId()));
    }

    /**
     * @param DataMapper $mapper
     * @param callable $filter
     * @return \Doctrine\ORM\Query
     */
    public function queryStockItems(DataMapper $mapper, callable $filter = null)
    {
        $this->assert($mapper->getEntityClass() == StockItem::class);

        $builder = $mapper->select();
        $builder->addSelect('st, su, uu');
        $builder->leftJoin('sti.type', 'st');
        $builder->leftJoin('sti.storage_unit', 'su');
        $builder->leftJoin('sti.usage_unit', 'uu');

        if ($filter) {
            $filter($builder);
        }

        return $builder->getQuery();
    }

    /**
     * @param Warehouse $warehouse
     * @return array
     */
    public function getStockItemInWarehouse(Warehouse $warehouse)
    {
        $this->assert(intval($warehouse->getId()) > 0);

        $sql = "SELECT" . "
            sti.id AS item_id,
            sti.name AS item_name,
            sti.code AS item_code,
            sti.description AS item_description,
            su.id AS storage_unit_id,
            su.name AS storage_unit_name,
            su.description AS storage_unit_description,
            uu.id AS usage_unit_id,
            uu.name AS usage_unit_name,
            uu.description AS usage_unit_description,
            st.id AS stock_id,
            st.current_unit_price AS current_unit_price,
            (st.current_level / su.ratio) AS current_level,
            (st.reorder_level / su.ratio) AS reorder_level,
            st.last_change AS last_change,
            st.last_purchase AS last_purchase
        FROM stock_items sti
        INNER JOIN stock_units su ON su.id = sti.storage_unit_id
        INNER JOIN stock_units uu ON uu.id = sti.usage_unit_id
        LEFT JOIN (
            SELECT  st.id,
                    st.stock_item_id,
                    st.current_unit_price,
                    st.current_level,
                    st.reorder_level,
                    st.last_change,
                    st.last_purchase
            FROM stocks st
            WHERE st.warehouse_id = ?
            GROUP BY st.id
        ) st ON st.stock_item_id = sti.id
        GROUP BY sti.id
        ";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $warehouse->getId());
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param StockItem $stockItem
     * @param int $page
     * @param int $pageSize
     * @param callable $callback
     * @return \Zend\Paginator\Paginator
     */
    public function getPagedChangelog(StockItem $stockItem, $page = 1, $pageSize = 100, callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select(LevelChange::ALIAS, Stock::ALIAS, StockItem::ALIAS, StockUnit::ALIAS, User::ALIAS)
            ->from(LevelChange::class, LevelChange::ALIAS)
            ->leftJoin(sprintf('%s.stock', LevelChange::ALIAS), Stock::ALIAS)
            ->leftJoin(sprintf('%s.stock_item', Stock::ALIAS), StockItem::ALIAS)
            ->leftJoin(sprintf('%s.storage_unit', StockItem::ALIAS), StockUnit::ALIAS)
            ->leftJoin(sprintf('%s.corrector', LevelChange::ALIAS), User::ALIAS)
            ->where(sprintf('%s.id = :id', StockItem::ALIAS))
            ->orderBy(sprintf('%s.created_at', LevelChange::ALIAS), 'desc')
            ->setParameter('id', $stockItem->getId());

        if ($callback) {
            $callback($builder);
        }

        return $this->paginateQueryBuilder($builder, $page, $pageSize);
    }

    /**
     * @param int $saleId
     * @param int $correctorId
     * @param float $delta
     * @param int $stockItemId
     * @param string $itemUnit
     * @param int $warehouseId
     * @return string
     */
    public function createUsageChangelogSql($saleId, $correctorId, $delta, $stockItemId, $itemUnit, $warehouseId)
    {
        $type = LevelChange::TYPE_USAGE;
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $sql = "insert" . " into level_changes (
            `corrector_id`, `stock_id`, `current_level`, `delta`, `auto`, `type`, `note`, `created_at`
            ) SELECT %d, id, current_level, %s, 1, '%s', '#%s (-%s %s)', '%s' FROM stocks where stock_item_id = %d AND warehouse_id = %d";
        return sprintf($sql, $correctorId, -$delta, $type, $saleId, $delta, $itemUnit, $now, $stockItemId, $warehouseId);
    }

    /**
     * @param float $delta
     * @param int $stockItemId
     * @param int $warehouseId
     * @return string
     */
    public function createStockUpdateSql($delta, $stockItemId, $warehouseId)
    {
        $lastChange = (new \DateTime())->format('Y-m-d H:i:s');
        $sql = "update" . " stocks set current_level = current_level - %s, last_change = '%s' where stock_item_id = %d and warehouse_id = %d";
        return sprintf($sql, $delta, $lastChange, $stockItemId, $warehouseId);
    }

    /**
     * @param array $ids
     * @return MenuPrice[]
     */
    public function findMenuPricesInIds(array $ids)
    {
        $dql = "select" . " mp, m, i, sti, uu, su
            from ent:MenuPrice mp
            left join mp.menu m
            left join m.ingredients i
            left join i.stock_item sti
            left join sti.usage_unit uu
            left join sti.storage_unit su
            where mp.id in(:ids)
            ";

        $query = $this->em()->createQuery($dql);
        $query->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        return $query->getResult();
    }

    /**
     * @param array $ids
     * @return Stock[]
     */
    public function findStockInIds(array $ids)
    {
        $dql = "select" . " st, sti, uu, su
            from ent:Stock st
            left join st.stock_item sti
            left join sti.usage_unit uu
            left join sti.storage_unit su
            where st.id in(:ids)
            ";

        $query = $this->em()->createQuery($dql);
        $query->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
        return $query->getResult();
    }

    /**
     * @throws \Exception
     */
    public function normalizeStockLevel()
    {
        $this->executeSql("update" . " stocks set current_level = 0 where current_level < 0");
    }

    /**
     * @param Purchase $purchase
     * @param User $corrector
     * @param array $rawItems
     */
    public function increaseStock(Purchase $purchase, User $corrector, array $rawItems)
    {
        $getStockId = function($stock)
        {
            if ($stock instanceof Stock) {
                return $stock->getId();
            } else if (is_numeric($stock)) {
                return $stock;
            } else {
                throw new \InvalidArgumentException;
            }
        };

        $stocks = [];
        $unitPrices = [];
        foreach ($rawItems as $item) {
            // todo: change $items array to object? to prevent missing keys in array.
            if (isset($item['stock']) && $item['stock'] && isset($item['quantity']) && $item['quantity']) {
                try {
                    $stocks[$getStockId($item['stock'])] = $item['quantity'];
                    if (isset($item['total']) && $item['total'] && ($item['quantity'] > 0)) {
                        $unitPrices[$getStockId($item['stock'])] = $item['total']/$item['quantity'];
                    }
                } catch (\Exception $e) { continue; }
            }
        }

        /** @var Stock[] $stockEntities */
        $stockEntities = $this->findStockInIds(array_keys($stocks));

        foreach ($stockEntities as $stockEntity) {
            if (isset($stocks[$stockEntity->getId()])) {

                $quantity = $stocks[$stockEntity->getId()];

                $ratio = $stockEntity->getStockItem()->getStorageUnit()->getRatio();
                $unitQuantity = $quantity * $ratio;

                if ($unitQuantity) {
                    // create level change
                    $levelChange = new LevelChange();
                    $levelChange->setStock($stockEntity);
                    $levelChange->setCorrector($corrector);
                    $levelChange->setCurrentLevel($stockEntity->getCurrentLevel());
                    $levelChange->setType(LevelChange::TYPE_PURCHASE);
                    $levelChange->setDelta($unitQuantity);
                    $levelChange->setAuto(true);
                    $levelChange->setNote(sprintf("#%d (+%s %s)",
                        $purchase->getId(), $quantity, $stockEntity->getStockItem()->getStorageUnit()->getName()
                    ));

                    // update stock
                    $stockEntity->setCurrentLevel($stockEntity->getCurrentLevel() + $unitQuantity);
                    $stockEntity->setLastChange(new \DateTime());

                    if (isset($unitPrices[$stockEntity->getId()])) {
                        $stockEntity->setCurrentUnitPrice($unitPrices[$stockEntity->getId()]);
                    }

                    if ($purchase->getOrderedAt() instanceof \DateTime) {
                        $stockEntity->setLastPurchase($purchase->getOrderedAt());
                    }

                    $this->em()->persist($levelChange);
                    $this->em()->persist($stockEntity);
                }
            }
        }

        $this->em()->flush();
    }

    /**
     * @param Sale $sale
     * @param array $rawItems
     * @throws \Exception
     */
    public function reduceStock(Sale $sale, array $rawItems)
    {
        if (!$sale->getOutletSale() instanceof OutletSale) {
            return;
        }

        $creatorId  = ($sale->getCreator() instanceof User) ? $sale->getCreator()->getId() : null;
        $warehouse  = $sale->getOutletSale()->getOutlet()->getWarehouse();
        $getPriceId = function($price)
        {
            if ($price instanceof MenuPrice) {
                return $price->getId();
            } else if (is_numeric($price)) {
                return $price;
            } else {
                throw new \InvalidArgumentException;
            }
        };

        $menuPrices = [];
        foreach ($rawItems as $item) {
            if (isset($item['menu_price']) && $item['menu_price'] && isset($item['quantity'])) {
                try {
                    $menuPrices[$getPriceId($item['menu_price'])] = $item['quantity'];
                } catch (\Exception $e) { continue; }
            }
        }

        $menuPriceEntities = $this->findMenuPricesInIds(array_keys($menuPrices));

        $insertSql = [];
        $updateSql = [];
        foreach ($menuPriceEntities as $menuPriceEntity) {
            if (isset($menuPrices[$menuPriceEntity->getId()])) {
                $quantity = $menuPrices[$menuPriceEntity->getId()];
                foreach ($menuPriceEntity->getMenu()->getIngredients() as $ingredient) {

                    $ratio = $ingredient->getStockItem()->getUsageUnit()->getRatio();
                    $used = $ingredient->getQuantity() * $ratio * $quantity;
                    $stockItemId = $ingredient->getStockItem()->getId();
                    $warehouseId = $warehouse->getId();
                    $storageUnit = $ingredient->getStockItem()->getStorageUnit();

                    if ($used > 0) {
                        $updateSql[] = $this->createStockUpdateSql($used, $stockItemId, $warehouseId);
                        $insertSql[] = $this->createUsageChangelogSql(
                            $sale->getId(), $creatorId, $used, $stockItemId, $storageUnit->getName(), $warehouseId
                        );
                    }
                }
            }
        }

        // execute reduce stock & write changelog
        if (count($updateSql)) {
            $this->executeSql(join(';', $updateSql));
            $this->executeSql(join(';', $insertSql));
        }

        $this->normalizeStockLevel();
    }

}