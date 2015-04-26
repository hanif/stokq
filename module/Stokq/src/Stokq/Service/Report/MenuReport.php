<?php

namespace Stokq\Service\Report;

use Stokq\Service\AbstractService;

/**
 * Class MenuReport
 * @package Stokq\Service\Report
 */
class MenuReport extends AbstractService
{
    const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRatioByQuantitySold(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('m.name, SUM(si.quantity) AS quantity');
        $builder->from('menus', 'm');
        $builder->leftJoin('m', 'menu_types', 'mt', 'mt.menu_id = m.id');
        $builder->leftJoin('mt', 'types', 't', 'mt.type_id = t.id');
        $builder->leftJoin('m', 'menu_prices', 'mp', 'm.id = mp.menu_id');
        $builder->leftJoin('mp', 'menu_sales', 'ms', 'mp.id = ms.menu_price_id');
        $builder->leftJoin('ms', 'sale_items', 'si', 'ms.sale_item_id = si.id');
        $builder->leftJoin('si', 'sales', 's', 'si.sale_id = s.id');
        $builder->groupBy('m.id');

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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getRatioByTotalSold(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('m.name, SUM(si.total) AS total');
        $builder->from('menus', 'm');
        $builder->leftJoin('m', 'menu_types', 'mt', 'mt.menu_id = m.id');
        $builder->leftJoin('mt', 'types', 't', 'mt.type_id = t.id');
        $builder->leftJoin('m', 'menu_prices', 'mp', 'm.id = mp.menu_id');
        $builder->leftJoin('mp', 'menu_sales', 'ms', 'mp.id = ms.menu_price_id');
        $builder->leftJoin('ms', 'sale_items', 'si', 'ms.sale_item_id = si.id');
        $builder->leftJoin('si', 'sales', 's', 'si.sale_id = s.id');
        $builder->groupBy('m.id');

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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     * @todo limit menu (e.g. only shows top 5 or top 10)
     */
    public function getQuantitySoldPerMonth(\DateTime $from = null, \DateTime $to = null, callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('m.name, SUM(si.quantity) AS quantity, DATE_FORMAT(s.ordered_at, "%b, %Y") AS prd');
        $builder->from('menus', 'm');
        $builder->leftJoin('m', 'menu_types', 'mt', 'mt.menu_id = m.id');
        $builder->leftJoin('mt', 'types', 't', 'mt.type_id = t.id');
        $builder->leftJoin('m', 'menu_prices', 'mp', 'm.id = mp.menu_id');
        $builder->leftJoin('mp', 'menu_sales', 'ms', 'mp.id = ms.menu_price_id');
        $builder->leftJoin('ms', 'sale_items', 'si', 'ms.sale_item_id = si.id');
        $builder->leftJoin('si', 'sales', 's', 'si.sale_id = s.id');
        $builder->groupBy('prd, m.id');

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
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param callable $callback
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getGrossRatio(callable $callback = null)
    {
        $builder = $this->db()->createQueryBuilder();
        $builder->select('m.id, m.name, m.default_price, SUM(i.qty_price) AS qty_price');
        $builder->from('menus', 'm');
        $builder->leftJoin('m', 'ingredients', 'i', 'i.menu_id = m.id');
        $builder->groupBy('m.id');

        if (is_callable($callback)) {
            $callback($builder);
        }

        $sql = $builder->getSQL();
        $stmt = $this->db()->prepare($sql);
        $stmt->execute($builder->getParameters());
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}