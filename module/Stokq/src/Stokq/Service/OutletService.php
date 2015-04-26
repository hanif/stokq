<?php

namespace Stokq\Service;

use Stokq\Entity\Outlet;
use Stokq\Entity\Sale;

/**
 * Class OutletService
 * @package Stokq\Service
 */
class OutletService extends AbstractService
{
    /**
     * @param Outlet $outlet
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addAllMenu(Outlet $outlet)
    {
        $this->assert(intval($outlet->getId()) > 0);

        $sql = "insert" . " into menu_prices (outlet_id, menu_id, price)
                select %d, id, default_price
                from menus";

        $this->db()->executeQuery(sprintf($sql, $outlet->getId()));
    }

    /**
     * @return array
     * @throws \Doctrine\DBAL\DBALException
     */
    public function findOutletWithSales()
    {
        $sql = "SELECT" . "
            o.id AS id,
            o.name AS name,
            o.address AS address,
            o.latitude AS latitude,
            o.longitude AS longitude,
            w.id AS warehouse_id,
            w.name AS warehouse_name,
            SUM(s_7.q) AS quantity_sold_in_7d,
            SUM(s_7.total) AS income_in_7d,
            SUM(s_30.q) AS quantity_sold_in_30d,
            SUM(s_30.total) AS income_in_30d
        FROM outlets o
        LEFT JOIN warehouses w ON w.id = o.warehouse_id
        LEFT JOIN outlet_sales os ON os.outlet_id = o.id
        LEFT JOIN (
            SELECT  s.id,
                    SUM(i.quantity) AS q,
                    SUM(i.total) AS total
            FROM sales s
            LEFT JOIN sale_items i ON i.sale_id = s.id
            WHERE (s.ordered_at BETWEEN :last7days AND :now) AND s.status = :status
            GROUP BY s.id
        ) s_7 ON s_7.id = os.sale_id
        LEFT JOIN (
            SELECT  s.id,
                    SUM(i.quantity) AS q,
                    SUM(i.total) AS total
            FROM sales s
            LEFT JOIN sale_items i ON i.sale_id = s.id
            WHERE (s.ordered_at BETWEEN :last30days AND :now) AND s.status = :status
            GROUP BY s.id
        ) s_30 ON s_30.id = os.sale_id
        GROUP BY o.id
        ";

        $stmt = $this->db()->prepare($sql);

        $now = (new \DateTime())->setTime(23, 59, 59);
        $last7Days = clone $now;
        $last7Days->modify('-7 days')->setTime(0, 0, 0);
        $last30Days = clone $now;
        $last30Days->modify('-30 days')->setTime(0, 0, 0);

        $stmt->bindValue('now', $now->format('Y-m-d H:i:s'));
        $stmt->bindValue('last7days', $last7Days->format('Y-m-d H:i:s'));
        $stmt->bindValue('last30days', $last30Days->format('Y-m-d H:i:s'));
        $stmt->bindValue('status', Sale::STATUS_PAID);

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}