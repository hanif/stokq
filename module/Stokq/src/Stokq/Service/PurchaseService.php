<?php

namespace Stokq\Service;

use Stokq\Entity\Purchase;
use Stokq\Entity\Supplier;

/**
 * Class PurchaseService
 * @package Stokq\Service
 */
class PurchaseService extends AbstractService
{
    /**
     * @param callable $callback
     * @return \Doctrine\ORM\Query
     */
    public function getPurchaseWithTotalQuery(callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select('p, new dto:PurchaseWithTotal(
            c.id, c.name,
            w.id, w.name,
            p.id, p.title, p.currency, p.status, p.ordered_at, p.delivered_at, p.canceled_at, p.created_at,
            COUNT(i.id), SUM(i.quantity), SUM(i.total)
        )');

        $builder->from(Purchase::class, 'p');
        $builder->leftJoin('p.creator', 'c');
        $builder->leftJoin('p.warehouse_purchase', 'wp');
        $builder->leftJoin('wp.warehouse', 'w');
        $builder->leftJoin('p.items', 'i');
        $builder->groupBy('p.id');

        if ($callback) {
            $callback($builder);
        }

        return $builder->getQuery();
    }

    /**
     * @param $purchaseId
     * @return Purchase
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getPurchaseWithItems($purchaseId)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select('p, c, u, wp, w, i, sp, s, si');

        $builder->from(Purchase::class, 'p');
        $builder->leftJoin('p.creator', 'c');
        $builder->leftJoin('p.supplier', 'u');
        $builder->leftJoin('p.warehouse_purchase', 'wp');
        $builder->leftJoin('wp.warehouse', 'w');
        $builder->leftJoin('p.items', 'i');
        $builder->leftJoin('i.stock_purchase', 'sp');
        $builder->leftJoin('sp.stock', 's');
        $builder->leftJoin('s.stock_item', 'si');
        $builder->where('p.id = :id');
        $builder->setParameter('id', $purchaseId);

        return $builder->getQuery()->getSingleResult();
    }

    /**
     * @param Purchase $purchase
     */
    public function removeItemsFromPurchase(Purchase $purchase)
    {
        $dql = 'delete' . ' from ent:PurchaseItem i where i.purchase = :purchase';
        $query = $this->em()->createQuery($dql);
        $query->setParameter('purchase', $purchase);
        $query->execute();
    }

    /**
     * @param callable $callback
     * @return \Doctrine\ORM\Query
     */
    public function getSupplierWithPurchaseQuery(callable $callback = null)
    {
        $builder = $this->em()->createQueryBuilder();
        $builder->select('s, new dto:SupplierWithPurchase(
            s.id, s.name, s.address, s.contact, s.email, s.website,
            s.note, COUNT(DISTINCT p.id), SUM(i.total), MAX(p.ordered_at)
        )');

        $builder->from(Supplier::class, 's');
        $builder->leftJoin('s.purchases', 'p');
        $builder->leftJoin('p.items', 'i');
        $builder->groupBy('s.id');

        if ($callback) {
            $callback($builder);
        }

        return $builder->getQuery();
    }
}