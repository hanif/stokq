<?php

namespace Stokq\Controller\Web\Reports;

use Doctrine\DBAL\Query\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Outlet;

/**
 * Class MenuController
 * @package Stokq\Controller\Web\Reports
 */
class MenuController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        $today = new \DateTime();
        $nextMonth = new \DateTime('next month');
        $last6mo = clone $nextMonth;
        $last6mo->modify('-6 months');

        $ratioByQuantity = array_merge([['Menu', 'Qty.']], $this->assocToList(
            $this->getMenuReportService()->getRatioByQuantitySold($last6mo, $today), 'name', [
                'key' => 'quantity',
                'filter' => 'intval',
            ]
        ));

        $ratioByTotal = array_merge([['Menu', 'Total']], $this->assocToList(
            $this->getMenuReportService()->getRatioByTotalSold($last6mo, $today), 'name', [
                'key' => 'total',
                'filter' => 'intval',
            ]
        ));

        /** @var Outlet[] $outlets */
        $outlets = $this->mapper(Outlet::class)->all();

        $outletFilter = function(Outlet $outlet) {
            return function(QueryBuilder $builder) use($outlet) {
                $builder->andWhere('mp.outlet_id = :outlet');
                $builder->setParameter('outlet', $outlet->getId());
            };
        };

        $qtyRatioByOutlets = [];
        $totalRatioByOutlets = [];

        foreach ($outlets as $outlet) {
            $qtyRatioByOutlets[$outlet->getId()] = array_merge([['Menu', 'Qty.']], $this->assocToList(
                $this->getMenuReportService()->getRatioByQuantitySold($last6mo, $today, $outletFilter($outlet)), 'name', [
                    'key'    => 'quantity',
                    'filter' => 'intval'
                ]
            ));

            $totalRatioByOutlets[$outlet->getId()] = array_merge([['Menu', 'Total']], $this->assocToList(
                $this->getMenuReportService()->getRatioByTotalSold($last6mo, $today, $outletFilter($outlet)), 'name', [
                    'key'    => 'total',
                    'filter' => 'intval'
                ]
            ));
        }

        $account = $this->account();

        return compact(
            'ratioByQuantity', 'ratioByTotal', 'qtyRatioByOutlets',
            'totalRatioByOutlets', 'account', 'outlets'
        );
    }

    /**
     * @param array $data
     * @param $keys
     * @return array
     */
    private function assocToList(array $data, ...$keys)
    {
        $list = [];
        foreach ($data as $row) {
            $listRow = [];
            foreach ($keys as $spec) {
                if (is_string($spec)) {
                    $listRow[] = isset($row[$spec]) ? $row[$spec] : null;
                } else if (is_array($spec)) {
                    if (!isset($spec['key'])) {
                        continue;
                    }

                    $key = $spec['key'];
                    $filter = isset($spec['filter']) ? $spec['filter'] : null;

                    if (isset($row[$key])) {
                        if (is_callable($filter)) {
                            $listRow[] = $filter($row[$key]);
                        } else {
                            $listRow[] = $row[$key];
                        }
                    } else {
                        $listRow[] = null;
                    }
                } else {
                    continue;
                }
            }
            $list[] = $listRow;
        }

        return $list;
    }
}