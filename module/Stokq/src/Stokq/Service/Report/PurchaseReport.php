<?php

namespace Stokq\Service\Report;

use Stokq\Entity\Purchase;
use Stokq\Service\AbstractService;

/**
 * Class PurchaseReport
 * @package Stokq\Service\Report
 */
class PurchaseReport extends AbstractService
{
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalPurchasePerMonthInRange(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('DATE_FORMAT(p.ordered_at, "%b, %Y") AS prd, SUM(i.total) AS total');
        $builder->from('purchases', 'p');
        $builder->leftJoin('p', 'purchase_items', 'i', 'p.id = i.purchase_id');
        $builder->andWhere("p.status IN (:in_progress, :delivered)");
        $builder->setParameter('in_progress', Purchase::STATUS_IN_PROGRESS);
        $builder->setParameter('delivered', Purchase::STATUS_DELIVERED);
        $builder->groupBy('prd');

        if ($from && $to) {
            $builder->andWhere('p.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalPurchaseByWarehouseInRange(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('w.name AS warehouse, SUM(i.total) AS total');
        $builder->from('purchases', 'p');
        $builder->leftJoin('p', 'purchase_items', 'i', 'p.id = i.purchase_id');
        $builder->leftJoin('p', 'warehouse_purchases', 'wp', 'p.id = wp.purchase_id');
        $builder->leftJoin('wp', 'warehouses', 'w', 'w.id = wp.warehouse_id');
        $builder->andWhere("p.status IN (:in_progress, :delivered)");
        $builder->setParameter('in_progress', Purchase::STATUS_IN_PROGRESS);
        $builder->setParameter('delivered', Purchase::STATUS_DELIVERED);
        $builder->groupBy('warehouse');

        if ($from && $to) {
            $builder->andWhere('p.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

}