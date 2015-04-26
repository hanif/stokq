<?php

namespace Stokq\Service\Report;

use Stokq\Service\AbstractService;

/**
 * Class SaleReport
 * @package Stokq\Service\Report
 */
class SaleReport extends AbstractService
{
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getTotalSalePerMonthInRange(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('DATE_FORMAT(s.ordered_at, "%b, %Y") AS prd, SUM(i.total) AS total');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 's.id = i.sale_id');
        $builder->groupBy('prd');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
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
    public function getSaleQuantityPerMonthInRange(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('DATE_FORMAT(s.ordered_at, "%b, %Y") AS prd, SUM(i.quantity) AS quantity');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 's.id = i.sale_id');
        $builder->groupBy('prd');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
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
    public function getTotalSaleByOutletInRange(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('o.name AS outlet, SUM(i.total) AS total');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 's.id = i.sale_id');
        $builder->leftJoin('s', 'outlet_sales', 'os', 's.id = os.sale_id');
        $builder->leftJoin('os', 'outlets', 'o', 'o.id = os.outlet_id');
        $builder->groupBy('o.name');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
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