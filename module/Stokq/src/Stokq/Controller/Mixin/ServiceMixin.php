<?php

namespace Stokq\Controller\Mixin;

use Stokq\Service\AccountService;
use Stokq\Service\LogService;
use Stokq\Service\MailerService;
use Stokq\Service\MenuService;
use Stokq\Service\OutletService;
use Stokq\Service\PurchaseService;
use Stokq\Service\Report\MenuReport;
use Stokq\Service\Report\OutletReport;
use Stokq\Service\Report\PurchaseReport;
use Stokq\Service\Report\SaleReport;
use Stokq\Service\Report\StatsReport;
use Stokq\Service\Report\StockReport;
use Stokq\Service\SaleService;
use Stokq\Service\StockService;
use Stokq\Service\UserService;
use Stokq\Service\WarehouseService;
use Stokq\Stdlib\Nav;

/**
 * Class ServiceMixin
 * @package Stokq\Controller\Mixin
 *
 * @method service
 */
trait ServiceMixin
{
    /**
     * @return AccountService
     */
    public function getAccountMService()
    {
        return $this->service(AccountService::class);
    }

    /**
     * @return LogService
     */
    public function getLogService()
    {
        return $this->service(LogService::class);
    }

    /**
     * @return MailerService
     */
    public function getMailerService()
    {
        return $this->service(MailerService::class);
    }

    /**
     * @return MenuService
     */
    public function getMenuService()
    {
        return $this->service(MenuService::class);
    }

    /**
     * @return OutletService
     */
    public function getOutletService()
    {
        return $this->service(OutletService::class);
    }

    /**
     * @return PurchaseService
     */
    public function getPurchaseService()
    {
        return $this->service(PurchaseService::class);
    }

    /**
     * @return SaleService
     */
    public function getSaleService()
    {
        return $this->service(SaleService::class);
    }

    /**
     * @return StockService
     */
    public function getStockService()
    {
        return $this->service(StockService::class);
    }

    /**
     * @return UserService
     */
    public function getUserService()
    {
        return $this->service(UserService::class);
    }

    /**
     * @return WarehouseService
     */
    public function getWarehouseService()
    {
        return $this->service(WarehouseService::class);
    }

    /**
     * @return StatsReport
     */
    public function getStatsReportService()
    {
        return $this->service(StatsReport::class);
    }

    /**
     * @return MenuReport
     */
    public function getMenuReportService()
    {
        return $this->service(MenuReport::class);
    }

    /**
     * @return OutletReport
     */
    public function getOutletReportService()
    {
        return $this->service(OutletReport::class);
    }

    /**
     * @return PurchaseReport
     */
    public function getPurchaseReportService()
    {
        return $this->service(PurchaseReport::class);
    }

    /**
     * @return SaleReport
     */
    public function getSaleReportService()
    {
        return $this->service(SaleReport::class);
    }

    /**
     * @return StockReport
     */
    public function getStockReportService()
    {
        return $this->service(StockReport::class);
    }

    /**
     * @return Nav
     */
    public function getNavService()
    {
        return $this->service('sidebar_nav');
    }
}