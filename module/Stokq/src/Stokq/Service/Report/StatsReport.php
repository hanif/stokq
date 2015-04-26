<?php

namespace Stokq\Service\Report;

use Stokq\Service\AbstractService;

/**
 * Class StatsReport
 * @package Stokq\Service\Report
 */
class StatsReport extends AbstractService
{
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @return int
     */
    public function countTotalWarehouse()
    {
        $query = $this->em()->createQuery("select" . " count(w) from ent:Warehouse w");
        return $query->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countTotalOutlet()
    {
        $query = $this->em()->createQuery("select" . "  count(o) from ent:Outlet o");
        return $query->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countTotalUser()
    {
        $query = $this->em()->createQuery("select" . "  count(u) from ent:User u");
        return $query->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countTotalMenu()
    {
        $query = $this->em()->createQuery("select" . "  count(m) from ent:Menu m");
        return $query->getSingleScalarResult();
    }

    /**
     * @return int
     */
    public function countTotalStockItem()
    {
        $query = $this->em()->createQuery("select" . "  count(s) from ent:StockItem s");
        return $query->getSingleScalarResult();
    }

    /**
     * @param int $days
     * @return double
     */
    public function getIncomeLast($days = 30)
    {
        $this->assert(is_numeric($days));

        $dql = "select" . "
            sum(i.total)
            from ent:Sale s
            left join s.items i
            where s.status = 'paid'
                and (s.ordered_at between :from and :to)
        ";
        $query = $this->em()->createQuery($dql);
        $to = new \DateTime('tomorrow');
        $from = clone $to;
        $from->modify(sprintf('-%d days', $days));
        $query->setParameters(compact('from', 'to'));
        return number_format(doubleval($query->getSingleScalarResult()), 2);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getIncome(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.total)');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAvgIncomeByOrderDate(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.total) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->groupBy('s.ordered_at');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select avg(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMaxIncomeInSale(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.total) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->groupBy('s.id');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select max(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMinIncomeInSale(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.total) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->andHaving("sum(i.total) > 0");
        $builder->groupBy('s.id');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select min(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param int $days
     * @return double
     */
    public function getMenuSoldLast($days = 30)
    {
        $this->assert(is_numeric($days));

        $dql = "select" . "
            sum(i.quantity)
            from ent:Sale s
            left join s.items i
            where s.status = 'paid'
                and (s.ordered_at between :from and :to)
        ";
        $query = $this->em()->createQuery($dql);
        $to = new \DateTime('tomorrow');
        $from = clone $to;
        $from->modify(sprintf('-%d days', $days));
        $query->setParameters(compact('from', 'to'));
        return $query->getSingleScalarResult();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMenuSold(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.quantity)');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAvgMenuSoldByOrderDate(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.quantity) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->groupBy('s.ordered_at');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select avg(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMaxMenuSoldInSale(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.quantity) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->groupBy('s.id');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select max(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getMinMenuSoldInSale(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.quantity) as r');
        $builder->from('sales', 's');
        $builder->leftJoin('s', 'sale_items', 'i', 'i.sale_id = s.id');
        $builder->andWhere("s.status = 'paid'");
        $builder->andHaving("sum(i.quantity) > 0");
        $builder->groupBy('s.id');

        if ($from && $to) {
            $builder->andWhere('s.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('s.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('s.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare(sprintf("select min(sq.r) from (%s) sq", $sql));
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

    /**
     * @param int $days
     * @return double
     */
    public function getOutcomeLast($days = 30)
    {
        $this->assert(is_numeric($days));

        $dql = "select" . "
            sum(i.total)
            from ent:Purchase p
            left join p.items i
            where p.status in ('in progress', 'delivered')
                and (p.ordered_at between :from and :to)
        ";
        $query = $this->em()->createQuery($dql);
        $to = new \DateTime('tomorrow');
        $from = clone $to;
        $from->modify(sprintf('-%d days', $days));
        $query->setParameters(compact('from', 'to'));
        return number_format(doubleval($query->getSingleScalarResult()), 2);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return bool|string
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getOutcome(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('sum(i.total)');
        $builder->from('purchases', 'p');
        $builder->leftJoin('p', 'purchase_items', 'i', 'i.purchase_id = p.id');
        $builder->andWhere("p.status in ('in progress', 'delivered')");

        if ($from && $to) {
            $builder->andWhere('p.ordered_at BETWEEN :from AND :to');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        } else if ($from) {
            $builder->andWhere('p.ordered_at > :from');
            $builder->setParameter('from', $from->format(self::DB_DATETIME_FORMAT));
        } else if ($to) {
            $builder->andWhere('p.ordered_at < :to');
            $builder->setParameter('to', $to->format(self::DB_DATETIME_FORMAT));
        }

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchColumn();
    }

}