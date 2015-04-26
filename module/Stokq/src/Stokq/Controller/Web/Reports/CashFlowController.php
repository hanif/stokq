<?php

namespace Stokq\Controller\Web\Reports;

use Doctrine\DBAL\Query\QueryBuilder;
use Stokq\Controller\AuthenticatedController;

/**
 * Class CashFlowController
 * @package Stokq\Controller\Web\Reports
 */
class CashFlowController extends AuthenticatedController
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

        $totalPurchasePerMonth = $this->formatMonthlyData(
            $this->getPurchaseReportService()->getTotalPurchasePerMonthInRange($last6mo, $today),
            'prd', 'total', clone $last6mo, clone $today, 'intval'
        );

        $totalSalePerMonth = $this->formatMonthlyData(
            $this->getSaleReportService()->getTotalSalePerMonthInRange($last6mo, $today),
            'prd', 'total', clone $last6mo, clone $today, 'intval'
        );

        $salesAndPurchaseData = [['Period', 'Purchases', 'Sales']];
        foreach ($totalPurchasePerMonth as $idx => $row) {
            if (!isset($totalSalePerMonth[$idx][1])) {
                throw new \RuntimeException('Data not match.');
            }
            $saleData = $totalSalePerMonth[$idx][1];
            $salesAndPurchaseData[] = array_merge($row, [$saleData]);
        }

        $grossRatioData = $this->getMenuReportService()->getGrossRatio(function(QueryBuilder $builder) {
            $builder->orderBy('qty_price', 'desc');
        });

        $account = $this->account();

        return compact('salesAndPurchaseData', 'account', 'grossRatioData');
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