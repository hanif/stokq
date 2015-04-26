<?php

namespace Stokq\Service;

use Stokq\Entity\Ingredient;
use Stokq\Entity\IngredientType;
use Stokq\Entity\Menu;
use Stokq\Entity\Outlet;
use Stokq\Entity\Type;
use Stokq\Stdlib\DataMapper;

/**
 * Class MenuService
 * @package Stokq\Service
 */
class MenuService extends AbstractService
{
    /**
     * @param DataMapper $mapper
     * @return Type[]
     */
    public function createDefaultMenuType(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == Type::class);
        return $mapper->createMany([
            ['name' => 'Makanan'],
            ['name' => 'Minuman'],
            ['name' => 'Penutup'],
        ]);
    }

    /**
     * @param DataMapper $mapper
     * @return IngredientType[]
     */
    public function createDefaultIngredientType(DataMapper $mapper)
    {
        $this->assert($mapper->getEntityClass() == IngredientType::class);
        return $mapper->createMany([
            ['name' => 'Bahan Makanan'],
            ['name' => 'Pengolahan'],
            ['name' => 'Pelengkap'],
            ['name' => 'Lainnya'],
        ]);
    }

    /**
     * @return Menu[]
     */
    public function findAllParentMenu()
    {
        $builder = $this->em()
            ->createQueryBuilder()
            ->select('m')
            ->from(Menu::class, 'm')
            ->where('m.parent is null');

        return $builder->getQuery()->getResult();
    }

    /**
     * @param Menu $menu
     * @throws \Doctrine\DBAL\DBALException
     */
    public function addToAllOutlet(Menu $menu)
    {
        $this->assert(intval($menu->getId()) > 0);

        $sql = "insert" . " into menu_prices (outlet_id, menu_id, price)
                select id, %d, '%s'
                from outlets";

        $this->db()->executeQuery(sprintf($sql, $menu->getId(), $menu->getDefaultPrice()));
    }

    /**
     * @param Menu $menu
     * @return Ingredient[]
     */
    public function getIngredientsOf(Menu $menu)
    {
        $this->assert(intval($menu->getId()) > 0);

        $dql = "select" . " i, m, si, uu
                from ent:Ingredient i
                left join i.menu m
                left join i.stock_item si
                left join si.usage_unit uu
                where m.id = :id";

        return $this->em()->createQuery($dql)
            ->setParameter('id', $menu->getId())
            ->getResult();
    }

    /**
     * @param Menu $menu
     * @return IngredientType[]
     */
    public function getIngredientsOfGroupedByType(Menu $menu)
    {
        $this->assert(intval($menu->getId()) > 0);

        $dql = "select" . " it, i, m, si, uu
                from ent:IngredientType it
                left join it.ingredients i
                left join i.menu m
                left join i.stock_item si
                left join si.usage_unit uu
                where m.id = :id
                order by it.name asc, i.qty_price desc
                ";

        return $this->em()->createQuery($dql)
            ->setParameter('id', $menu->getId())
            ->getResult();
    }

    /**
     * @param Menu $menu
     * @return array
     * @todo remove redundant join `LEFT JOIN menus m ON m.id = mp.menu_id`
     */
    public function getPricePerOutlet(Menu $menu)
    {
        $this->assert(intval($menu->getId()) > 0);

        $sql = "SELECT" . "
            o.id AS outlet_id,
            o.name AS outlet_name,
            mp.id AS price_id,
            mp.price AS price,
            mp.serving_unit AS serving_unit,
            SUM(ms.sale_quantity) AS sale_quantity,
            SUM(ms.sale_subtotal) AS sale_subtotal,
            SUM(ms.sale_total) AS sale_total
        FROM outlets o
        LEFT JOIN (
            SELECT mp.id, mp.outlet_id, mp.price, m.serving_unit
            FROM  menu_prices mp
            LEFT JOIN menus m ON m.id = mp.menu_id
            WHERE m.id = ?
            GROUP BY mp.id
        ) mp ON mp.outlet_id = o.id
        LEFT JOIN (
            SELECT ms.id AS menu_sale_id,
                ms.menu_price_id AS menu_price_id,
                si.quantity AS sale_quantity,
                si.subtotal AS sale_subtotal,
                si.total AS sale_total
            FROM  menu_sales ms
            LEFT JOIN sale_items si ON si.id = ms.sale_item_id
            LEFT JOIN sales s ON s.id = si.sale_id
            WHERE s.ordered_at BETWEEN ? AND ?
            GROUP BY ms.id
        ) ms ON ms.menu_price_id = mp.id
        GROUP BY o.id
        ";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $menu->getId());

        $today = new \DateTime();
        $last30days = clone $today;
        $last30days->modify('-30 days');
        $stmt->bindValue(2, $last30days->format('Y-m-d 00:00:00'));
        $stmt->bindValue(3, $today->format('Y-m-d 23:59:59'));

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param Outlet $outlet
     * @return array
     */
    public function getMenuInOutlet(Outlet $outlet)
    {
        $this->assert(intval($outlet->getId()) > 0);

        $sql = "SELECT" . "
            m.id AS menu_id,
            m.name AS menu_name,
            m.serving_unit AS serving_unit,
            mp.id AS price_id,
            mp.price AS price,
            SUM(ms.sale_quantity) AS sale_quantity,
            SUM(ms.sale_subtotal) AS sale_subtotal,
            SUM(ms.sale_total) AS sale_total
        FROM menus m
        LEFT JOIN (
            SELECT mp.id, mp.menu_id, mp.price
            FROM  menu_prices mp
            WHERE mp.outlet_id = ?
            GROUP BY mp.id
        ) mp ON mp.menu_id = m.id
        LEFT JOIN (
            SELECT ms.id AS menu_sale_id,
                ms.menu_price_id AS menu_price_id,
                si.quantity AS sale_quantity,
                si.subtotal AS sale_subtotal,
                si.total AS sale_total
            FROM  menu_sales ms
            LEFT JOIN sale_items si ON si.id = ms.sale_item_id
            LEFT JOIN sales s ON s.id = si.sale_id
            WHERE s.ordered_at BETWEEN ? AND ?
            GROUP BY ms.id
        ) ms ON ms.menu_price_id = mp.id
        GROUP BY m.id
        ";

        $stmt = $this->db()->prepare($sql);
        $stmt->bindValue(1, $outlet->getId());

        $today = new \DateTime();
        $last30days = clone $today;
        $last30days->modify('-30 days');
        $stmt->bindValue(2, $last30days->format('Y-m-d 00:00:00'));
        $stmt->bindValue(3, $today->format('Y-m-d 23:59:59'));

        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}