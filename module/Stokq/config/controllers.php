<?php

use Stokq\Controller\Web;

return [
    'invokables' => [
        'Stokq\Controller\Web\Access' => Web\AccessController::class,
        'Stokq\Controller\Web\Category' => Web\CategoryController::class,
        'Stokq\Controller\Web\Classification' => Web\ClassificationController::class,
        'Stokq\Controller\Web\Index' => Web\IndexController::class,
        'Stokq\Controller\Web\Ingredient' => Web\IngredientController::class,
        'Stokq\Controller\Web\Menu' => Web\MenuController::class,
        'Stokq\Controller\Web\MenuPrice' => Web\MenuPriceController::class,
        'Stokq\Controller\Web\Outlet' => Web\OutletController::class,
        'Stokq\Controller\Web\Purchase' => Web\PurchaseController::class,
        'Stokq\Controller\Web\Report' => Web\ReportController::class,
        'Stokq\Controller\Web\Sale' => Web\SaleController::class,
        'Stokq\Controller\Web\Setting' => Web\SettingController::class,
        'Stokq\Controller\Web\Stock' => Web\StockController::class,
        'Stokq\Controller\Web\StockItem' => Web\StockItemController::class,
        'Stokq\Controller\Web\StockUnit' => Web\StockUnitController::class,
        'Stokq\Controller\Web\StorageType' => Web\StorageTypeController::class,
        'Stokq\Controller\Web\Supplier' => Web\SupplierController::class,
        'Stokq\Controller\Web\Type' => Web\TypeController::class,
        'Stokq\Controller\Web\User' => Web\UserController::class,
        'Stokq\Controller\Web\Warehouse' => Web\WarehouseController::class,

        'Stokq\Controller\Web\Reports\CashFlow' => Web\Reports\CashFlowController::class,
        'Stokq\Controller\Web\Reports\Menu' => Web\Reports\MenuController::class,
        'Stokq\Controller\Web\Reports\Outlet' => Web\Reports\OutletController::class,
        'Stokq\Controller\Web\Reports\Overview' => Web\Reports\OverviewController::class,
        'Stokq\Controller\Web\Reports\Purchase' => Web\Reports\PurchaseController::class,
        'Stokq\Controller\Web\Reports\Sale' => Web\Reports\SaleController::class,
    ],
];