<?php

namespace Stokq\Controller\Web\Reports;

use Stokq\Controller\AuthenticatedController;

/**
 * Class SaleController
 * @package Stokq\Controller\Web\Reports
 */
class SaleController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        return $this->redirect()->toRoute(...$this->routeSpec('report.sale.overview'));
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function tableAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        return [];
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function overviewAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('report');

        $today = new \DateTime();
        $nextMonth = new \DateTime('next month');
        $last6mo = clone $nextMonth;
        $last6mo->modify('-6 months');

        $totalSalePerMonth = array_merge([['Period', 'Total']], $this->formatMonthlyData(
            $this->getSaleReportService()->getTotalSalePerMonthInRange($last6mo, $today),
            'prd', 'total', clone $last6mo, clone $today, 'intval'
        ));

        $saleQuantityPerMonth = array_merge([['Period', 'Quantity']], $this->formatMonthlyData(
            $this->getSaleReportService()->getSaleQuantityPerMonthInRange($last6mo, $today),
            'prd', 'quantity', clone $last6mo, clone $today, 'intval'
        ));

        $totalSaleByOutlet = array_merge([['Outlet', 'Total']], $this->formatTupleData(
            $this->getSaleReportService()->getTotalSaleByOutletInRange($last6mo, $nextMonth),
            'outlet', 'total', 'intval'
        ));

        $account = $this->account();

        return compact('totalSalePerMonth', 'saleQuantityPerMonth', 'totalSaleByOutlet', 'account');
    }

    /**
     * @param array $results
     * @param $groupKey
     * @param $valueKey
     * @param \DateTime $from
     * @param \DateTime $to
     * @param callable $filter
     * @return array
     */
    private function formatMonthlyData(array $results, $groupKey, $valueKey, \DateTime $from, \DateTime $to, callable $filter = null)
    {
        $xLabels = [];

        do {
            $xLabels[] = $from->format('M, Y');
            $from->modify('first day of next month');
        } while ((int)$from->format('Ym') <= (int)$to->format('Ym'));

        $groupedData = $this->arrayGroupBy($results, $groupKey);

        $data = [];
        foreach ($xLabels as $period) {
            $value = isset($groupedData[$period][0][$valueKey]) ? $groupedData[$period][0][$valueKey] : 0;
            if (is_callable($filter)) {
                $value = $filter($value);
            }
            $data[] = [$period, $value];
        }

        return $data;
    }

    /**
     * @param array $results
     * @param $first
     * @param $second
     * @param callable $filter
     * @return array
     */
    private function formatTupleData(array $results, $first, $second, callable $filter = null)
    {
        $data = [];
        foreach ($results as $row) {
            $snd = isset($row[$second]) ? $row[$second] : '';
            if (is_callable($filter)) {
                $snd = $filter($snd);
            }
            $data[] = [isset($row[$first]) ? $row[$first] : '', $snd];
        }

        return $data;
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
}