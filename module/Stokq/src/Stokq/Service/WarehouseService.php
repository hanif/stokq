<?php

namespace Stokq\Service;

use Stokq\Entity\LevelChange;
use Stokq\Entity\Stock;
use Stokq\Entity\StockItem;
use Stokq\Entity\StockUnit;
use Stokq\Entity\User;
use Stokq\Entity\Warehouse;

/**
 * Class WarehouseService
 * @package Stokq\Service
 */
class WarehouseService extends AbstractService
{
    /**
     * @param Warehouse $warehouse
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addAllStockItems(Warehouse $warehouse)
    {
        $this->assert(intval($warehouse->getId()) > 0);

        $sql = "insert" . " into stocks (warehouse_id, stock_item_id, current_level)
                select %d, id, 0
                from stock_items";

        $this->db()->executeQuery(sprintf($sql, $warehouse->getId()));
    }

    /**
     * @param Warehouse $warehouse
     * @param int $page
     * @param int $pageSize
     * @param callable $callback
     * @return \Zend\Paginator\Paginator
     */
    public function getPagedChangelog(Warehouse $warehouse, $page = 1, $pageSize = 100, callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select(LevelChange::ALIAS, Stock::ALIAS, StockItem::ALIAS, StockUnit::ALIAS, User::ALIAS)
            ->from(LevelChange::class, LevelChange::ALIAS)
            ->leftJoin(sprintf('%s.stock', LevelChange::ALIAS), Stock::ALIAS)
            ->leftJoin(sprintf('%s.stock_item', Stock::ALIAS), StockItem::ALIAS)
            ->leftJoin(sprintf('%s.storage_unit', StockItem::ALIAS), StockUnit::ALIAS)
            ->leftJoin(sprintf('%s.corrector', LevelChange::ALIAS), User::ALIAS)
            ->where(sprintf('%s.warehouse = :id', Stock::ALIAS))
            ->orderBy(sprintf('%s.created_at', LevelChange::ALIAS), 'desc')
            ->setParameter('id', $warehouse->getId());

        if ($callback) {
            $callback($builder);
        }

        return $this->paginateQueryBuilder($builder, $page, $pageSize);
    }

    /**
     * @param Warehouse $warehouse
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return LevelChange[]
     */
    public function getChangelogInRange(Warehouse $warehouse, \DateTime $from, \DateTime $to, callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select(LevelChange::ALIAS, Stock::ALIAS, StockItem::ALIAS, StockUnit::ALIAS, User::ALIAS)
            ->from(LevelChange::class, LevelChange::ALIAS)
            ->leftJoin(sprintf('%s.stock', LevelChange::ALIAS), Stock::ALIAS)
            ->leftJoin(sprintf('%s.stock_item', Stock::ALIAS), StockItem::ALIAS)
            ->leftJoin(sprintf('%s.storage_unit', StockItem::ALIAS), StockUnit::ALIAS)
            ->leftJoin(sprintf('%s.corrector', LevelChange::ALIAS), User::ALIAS)
            ->where(sprintf('%s.warehouse = :id', Stock::ALIAS))
            ->andWhere(sprintf('(%s.created_at between :from and :to)', LevelChange::ALIAS))
            ->orderBy(sprintf('%s.created_at', LevelChange::ALIAS), 'desc')
            ->setParameter('id', $warehouse->getId())
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        if ($callback) {
            $callback($builder);
        }

        return $builder->getQuery()->getResult();
    }

    /**
     * @param callable $callback
     * @return Stock[]
     */
    public function findLowStocks(callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select(['st', 'w', 'sti', 't', 'su', 'i'])
            ->from(Stock::class, 'st')
            ->leftJoin('st.warehouse', 'w')
            ->leftJoin('st.stock_item', 'sti')
            ->leftJoin('sti.type', 't')
            ->leftJoin('sti.ingredients', 'i')
            ->leftJoin('sti.storage_unit', 'su')
            ->andWhere('(st.current_level <= 0 or
                        (st.reorder_level is not null and st.current_level < st.reorder_level)
                        )');

        if ($callback) {
            $callback($builder);
        }

        $query = $builder->getQuery();
        return $query->getResult();
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findWarehouseWithIndicator()
    {
        $sql = "SELECT" . "
            w.id AS id,
            w.name AS name,
            w.address AS address,
            w.latitude AS latitude,
            w.longitude AS longitude,
            COUNT(DISTINCT st.id) AS stock_count,
            MAX(st.last_change) AS last_change,
            MAX(st.last_purchase) AS last_purchase,
            COUNT(DISTINCT st_e.id) AS empty_stocks,
            COUNT(DISTINCT st_l.id) AS low_stocks
        FROM warehouses w
        LEFT JOIN stocks st ON st.warehouse_id = w.id
        LEFT JOIN (
            SELECT  id,
                    warehouse_id,
                    current_level
            FROM stocks
            WHERE current_level <= 0
        ) st_e ON st_e.warehouse_id = w.id
        LEFT JOIN (
            SELECT  id,
                    warehouse_id,
                    current_level,
                    reorder_level
            FROM stocks
            WHERE reorder_level IS NOT NULL AND current_level <= reorder_level AND current_level > 0
        ) st_l ON st_l.warehouse_id = w.id
        GROUP BY w.id
        ";

        $stmt = $this->db()->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}