<?php

namespace Stokq\Controller\Web\Reports;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Outlet;
use Stokq\Entity\Type;

/**
 * Class OutletController
 * @package Stokq\Controller\Web\Reports
 */
class OutletController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        try {
            $outlet = $this->mapper(Outlet::class)->first();
            return $this->redirect()->toRoute(...$this->routeSpec('report.outlet.detail?id=' . $outlet->getId()));
        } catch (NoResultException $e) {
            $this->layout('layout/single-column');
            $this->getNavService()->setActive('report');

            return [];
        }
    }

    public function detailAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        try {
            $outlet = $this->mapper(Outlet::class)->one($this->getRequestQuery('id'));
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }


        $from   = $this->getRequestQuery('from');
        $to     = $this->getRequestQuery('to');
        $fromDT = null;
        $toDT   = null;

        if ($from && ($_ = \DateTime::createFromFormat('Y-m-d', $from)) && ($_ instanceof \DateTime)) {
            $fromDT = $_;
        }

        if ($to && ($_ = \DateTime::createFromFormat('Y-m-d', $to)) && ($_ instanceof \DateTime)) {
            $toDT = $_;
        }


        /** @var Type[] $types */
        $types = $this->mapper(Type::class)->all();

        $typeFilter = function(Type $type) use($outlet) {
            return function(QueryBuilder $builder) use($type, $outlet) {
                $builder->andWhere('t.id = :type');
                $builder->andWhere('mp.outlet_id = :outlet');
                $builder->setParameter('type', $type->getId());
                $builder->setParameter('outlet', $outlet->getId());
            };
        };

        $qtyRatioByTypes = [];
        $totalRatioByTypes = [];
        $qtySoldPerMonthByTypes = [];

        $timelineFromDT = $fromDT ?: $this->getMinOrderDate();
        $timelineToDT = $toDT ?: new \DateTime();

        foreach ($types as $type) {
            $qtyRatioByTypes[$type->getId()] = array_merge([['Menu', 'Qty.']], $this->assocToList(
                $this->getMenuReportService()->getRatioByQuantitySold($fromDT, $toDT, $typeFilter($type)), 'name', [
                    'key'    => 'quantity',
                    'filter' => 'intval'
                ]
            ));

            $totalRatioByTypes[$type->getId()] = array_merge([['Menu', 'Total']], $this->assocToList(
                $this->getMenuReportService()->getRatioByTotalSold($fromDT, $toDT, $typeFilter($type)), 'name', [
                    'key'    => 'total',
                    'filter' => 'intval'
                ]
            ));

            $qtySoldPerMonthByTypes[$type->getId()] = $this->formatMonthlyQuantitySoldData(
                $this->getMenuReportService()->getQuantitySoldPerMonth($timelineFromDT, $timelineToDT, function(QueryBuilder $builder) use ($type) {
                    $builder->andWhere('t.id = :type_id');
                    $builder->setParameter('type_id', $type->getId());
                }),
                clone $timelineFromDT,
                clone $timelineToDT
            );
        }

        $outlets = $this->mapper(Outlet::class)->all();
        $account = $this->account();

        $outletFilter = function() use($outlet) {
            return function(QueryBuilder $builder) use($outlet) {
                $builder->leftJoin('s', 'outlet_sales', 'os');
                $builder->andWhere('os.outlet = :outlet');
                $builder->setParameter('outlet', $outlet->getId());
            };
        };

        $totalIncome = $this->getStatsReportService()->getIncome($fromDT, $toDT, $outletFilter);
        $avgIncome = $this->getStatsReportService()->getAvgIncomeByOrderDate($fromDT, $toDT, $outletFilter);
        $maxIncome = $this->getStatsReportService()->getMaxIncomeInSale($fromDT, $toDT, $outletFilter);
        $minIncome = $this->getStatsReportService()->getMinIncomeInSale($fromDT, $toDT, $outletFilter);
        $totalMenuSold = $this->getStatsReportService()->getMenuSold($fromDT, $toDT, $outletFilter);
        $avgMenuSold = $this->getStatsReportService()->getAvgMenuSoldByOrderDate($fromDT, $toDT, $outletFilter);
        $maxMenuSold = $this->getStatsReportService()->getMaxMenuSoldInSale($fromDT, $toDT, $outletFilter);
        $minMenuSold = $this->getStatsReportService()->getMinMenuSoldInSale($fromDT, $toDT, $outletFilter);

        return compact(
            'outlet', 'account', 'types', 'outlets', 'from', 'to', 'fromDT', 'toDT',
            'qtyRatioByTypes', 'totalRatioByTypes', 'qtySoldPerMonthByTypes', 'totalIncome',
            'avgIncome', 'maxIncome', 'minIncome', 'totalMenuSold', 'avgMenuSold', 'maxMenuSold',
            'minMenuSold'
        );
    }

    /**
     * @return \DateTime
     */
    private function getMinOrderDate()
    {
        try {
            $minOrderDate = $this->em()->createQuery('select' . ' min(s.ordered_at) from ent:Sale s')->getSingleScalarResult();
            $minOrderDT = \DateTime::createFromFormat('Y-m-d H:i:s', $minOrderDate);
            if (!$minOrderDT instanceof \DateTime) {
                throw new \RuntimeException();
            }
            return $minOrderDT;
        } catch (\Exception $e) {
            return new \DateTime();
        }
    }

    /**
     * @param array $results
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    private function formatMonthlyQuantitySoldData(array $results, \DateTime $from, \DateTime $to)
    {
        $data = [];
        $xLabels = [];

        if ($from->format('M, Y') != $to->format('M, Y')) {
            do {
                $xLabels[] = $from->format('M, Y');
                $from->modify('+1 month');
            } while ($from->format('M, Y') != $to->format('M, Y'));
        } else {
            $xLabels[] = $to->format('M, Y');
        }

        $groupedData = $this->arrayGroupBy($results, 'name');
        $defaults = array_combine($xLabels, array_fill(0, count($xLabels), 0));

        foreach ($groupedData as $key => $val) {
            if (trim($key) === '') {
                $key = 'Other';
            }

            foreach ($val as $qs) {
                $data[$key][$qs['prd']] = $qs['quantity'];
            }
            $data[$key] += $defaults;
        }

        $dataTable = [];
        $dataTable[0] = array_merge(['Bulan'], array_keys($data));

        for ($i = 0; $i < count($xLabels); ++$i) {
            $row = [$xLabels[$i]];
            foreach ($data as $qData) {
                $value = (float)$qData[$xLabels[$i]];
                $row[] = $value;

            }
            $dataTable[$i+1] = $row;
        }

        return $dataTable;
    }

    /**
     * @param array $arr
     * @param $key
     * @return array
     */
    private function arrayGroupBy(array $arr, $key)
    {
        $copy = [];

        foreach ($arr as $row) {
            $row = (array) $row;
            if (array_key_exists($key, $row)) {
                $groupName = $row[$key];
                unset($row[$key]);
                $copy[$groupName][] = $row;
            }
        }

        return $copy;
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