<?php

namespace Stokq\Service;

use Stokq\Entity\Sale;

/**
 * Class SaleService
 * @package Stokq\Service
 */
class SaleService extends AbstractService
{
    /**
     * @param callable $callback
     * @return \Doctrine\ORM\Query
     */
    public function getSaleWithTotalQuery(callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select('s, new dto:SaleWithTotal(
            c.id, c.name,
            o.id, o.name,
            s.id, s.title, s.currency, s.type, s.ordered_at, s.created_at,
            COUNT(i.id), SUM(i.quantity), SUM(i.total)
        )');

        $builder->from(Sale::class, 's');
        $builder->leftJoin('s.creator', 'c');
        $builder->leftJoin('s.outlet_sale', 'os');
        $builder->leftJoin('os.outlet', 'o');
        $builder->leftJoin('s.items', 'i');
        $builder->groupBy('s.id');

        if ($callback) {
            $callback($builder);
        }

        return $builder->getQuery();
    }

    /**
     * @param $saleId
     * @return Sale
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getSaleWithItems($saleId)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select('s, c, os, o, i, ms, mp, m');

        $builder->from(Sale::class, 's');
        $builder->leftJoin('s.creator', 'c');
        $builder->leftJoin('s.outlet_sale', 'os');
        $builder->leftJoin('os.outlet', 'o');
        $builder->leftJoin('s.items', 'i');
        $builder->leftJoin('i.menu_sale', 'ms');
        $builder->leftJoin('ms.menu_price', 'mp');
        $builder->leftJoin('mp.menu', 'm');
        $builder->where('s.id = :id');
        $builder->setParameter('id', $saleId);
        $builder->orderBy('m.name', 'asc');

        return $builder->getQuery()->getSingleResult();
    }

    /**
     * @param Sale $sale
     */
    public function removeItemsFromSale(Sale $sale)
    {
        $dql = 'delete' . ' from ent:SaleItem i where i.sale = :sale';
        $query = $this->em()->createQuery($dql);
        $query->setParameter('sale', $sale);
        $query->execute();
    }
}