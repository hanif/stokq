<?php

namespace Stokq\Controller\Web;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Controller\Mixin\ExcelDocUtilsMixin;
use Stokq\Entity\Purchase;
use Stokq\Entity\PurchaseItem;
use Stokq\Entity\Stock;
use Stokq\Entity\StockItem;
use Stokq\Entity\StockPurchase;
use Stokq\Entity\StockUnit;
use Stokq\Entity\Supplier;
use Stokq\Entity\User;
use Stokq\Entity\Warehouse;
use Stokq\Entity\WarehousePurchase;
use Stokq\Flow\PurchaseFlow;
use Stokq\InputFilter\PurchaseInput;
use Zend\EventManager\Event;

/**
 * Class PurchaseController
 * @package Stokq\Controller\Web
 */
class PurchaseController extends AuthenticatedController
{
    use ExcelDocUtilsMixin;

    /**
     * @var StockUnit[]
     */
    private $stockUnits = [];

    /**
     * Display list of purchase.
     *
     * User can search by title, filter by status, warehouse, and by date range.
     *
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('purchase');

        $keywordInp    = $this->getRequestQuery('q');
        $warehousesInp = $this->getRequestQuery('warehouses');
        $fromInp       = $this->getRequestQuery('from');
        $toInp         = $this->getRequestQuery('to');
        $statusInp     = $this->getRequestQuery('status');
        $cbFilter      = $this->filterList($keywordInp, $warehousesInp, $fromInp, $toInp, $statusInp);
        $query         = $this->getPurchaseService()->getPurchaseWithTotalQuery($cbFilter);
        $page          = $this->getRequestQuery('page', 1);
        $pageSize      = $this->getRequestQuery('pageSize', 20);
        $pages         = $this->mapper(Purchase::class)->paginate($query, $page, $pageSize);
        $user          = $this->user();
        $account       = $this->account();
        $warehouses    = $this->mapper(Warehouse::class)->all();

        return compact('warehouses', 'pages', 'page', 'user', 'account') +
        [
            'selectedWarehouses' => $warehousesInp,
            'keyword'            => $keywordInp,
            'status'             => $statusInp,
            'from'               => $fromInp,
            'to'                 => $toInp
        ];
    }

    /**
     * Display new purchase form
     *
     * User must select warehouse first, before navigating to this page.
     * This page will display a set of form inputs to enable user write down the item line.
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function newAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('purchase');

        try {
            $warehouse  = $this->mapper(Warehouse::class)->one($this->getRequestQuery('id'));
            $items      = $this->findStockItemInWarehouse($warehouse);
            $unitTypes  = $this->getStockService()->findAllStockUnitGroupedByType();
            $jsonItems  = json_encode($items);
            $account    = $this->account();
            $user       = $this->user();

            return compact('warehouse', 'user', 'account', 'items', 'jsonItems', 'unitTypes');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * Create new purchase
     *
     * After submitting form from previous page (@see self::newAction()), user will hit
     * this page via ajax request. All inputs are validate here, if it is valid, insert
     * the data into database.
     *
     * If the purchase status is `delivered` and user check the `update_stock_level` checkbox,
     * increase the stock in the selected warehouse as well based on the item quantity that
     * have just purchased.
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');

        $input = $this->autoFilledInputFilter(PurchaseInput::class);

        if ($input->isValid()) {

            $data           = $input->getValues();
            $purchase       = $this->mapper(Purchase::class)->create($data + ['creator' => $this->user()]);
            $supplierName   = $data['supplier_name'];

            if (trim($supplierName)) {
                $supplier = $this->em()->getRepository(Supplier::class)->findOneBy(['name' => $supplierName]);
                if (!$supplier) {
                    $supplier = new Supplier();
                    $supplier->setName($supplierName);
                    $this->em()->persist($supplier);
                }
                $purchase->setSupplier($supplier);
            }

            $this->insertRawItems($purchase, $data['raw_items']);
            $this->commit();

            $this->getEventManager()->trigger('items_inserted', $this, $data);

            // increase stock in warehouse
            if ($this->getRequestPost('update_stock_level') && $data['status'] == Purchase::STATUS_DELIVERED) {
                $this->getStockService()->increaseStock($purchase, $this->user(), $data['raw_items']);
            }

            // log all changes
            $this->getLogService()->logCreatePurchase($this->user(), $purchase, $data);

            return $this->jsonOk([], 201);
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * Print purchase
     */
    public function printAction()
    {
        $this->ensure('get');

        try {
            $purchase = $this->getPurchaseService()->getPurchaseWithItems($this->getRequestQuery('id'));
            $account = $this->account();
            return compact('purchase', 'account');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * Download purchase detail
     */
    public function downloadExcelAction()
    {
        try {
            $purchase = $this->getPurchaseService()->getPurchaseWithItems($this->getRequestQuery('id'));
            $account = $this->account();

            // set excel working directory
            $config = $this->getServiceManager()->get('ApplicationConfig');
            if (isset($config['cache_dir'])) {
                $this->setExcelDocsWorkingDir($config['cache_dir']);
            } else {
                $this->setExcelDocsWorkingDir(sys_get_temp_dir());
            }

            $excel = $this->createExcelDoc(function(\PHPExcel $excel) use($purchase, $account) {
                $ws = $excel->getActiveSheet();

                $ws->mergeCells('A2:F2');
                $ws->setCellValue('A2', $purchase->getTitle());
                $ws->getRowDimension(2)->setRowHeight(30);
                $ws->getStyle('A2')->getFont()->setBold(true);
                $ws->getStyle('A2')->getFont()->setSize(18);

                $ws->mergeCells('A3:F3');
                $ws->setCellValue('A3', sprintf('Status: %s', Purchase::statusToLocaleID($purchase->getStatus())));
                $ws->getStyle('A3')->getFont()->setSize(13);
                $ws->getRowDimension(3)->setRowHeight(22);

                $thRowNum = 5;
                if ($purchase->getCreator() instanceof User) {
                    $ws->mergeCells('A5:F5');
                    $ws->setCellValue('A5', sprintf('Dibuat Oleh: %s', $purchase->getCreator()->getName()));
                    $ws->getStyle('A5')->getFont()->setSize(13);
                    $ws->getRowDimension(5)->setRowHeight(22);
                    $thRowNum += 1;
                }

                if ($purchase->getCreatedAt() instanceof \DateTime) {
                    $ws->mergeCells('A6:F6');
                    $ws->setCellValue('A6', sprintf('Tgl. Dibuat: %s', $purchase->getCreatedAt()->format('d M Y')));
                    $ws->getStyle('A6')->getFont()->setSize(13);
                    $ws->getRowDimension(6)->setRowHeight(22);
                    $thRowNum += 1;
                }

                if ($purchase->getOrderedAt() instanceof \DateTime) {
                    $ws->mergeCells('A7:F7');
                    $ws->setCellValue('A7', sprintf('Tgl. Order: %s', $purchase->getOrderedAt()->format('d M Y')));
                    $ws->getStyle('A7')->getFont()->setSize(13);
                    $ws->getRowDimension(7)->setRowHeight(22);
                    $thRowNum += 2;
                }

                if ($purchase->getDeliveredAt() instanceof \DateTime) {
                    $ws->mergeCells('A8:F8');
                    $ws->setCellValue('A8', sprintf('Tgl. Diterima: %s', $purchase->getDeliveredAt()->format('d M Y')));
                    $ws->getStyle('A8')->getFont()->setSize(13);
                    $ws->getRowDimension(8)->setRowHeight(22);
                    $thRowNum += 1;
                }

                $ws->getColumnDimension('A')->setWidth(5);
                $ws->getColumnDimension('B')->setWidth(32);
                $ws->getColumnDimension('C')->setWidth(14);
                $ws->getColumnDimension('D')->setWidth(20);
                $ws->getColumnDimension('E')->setWidth(20);
                $ws->getColumnDimension('F')->setWidth(20);

                $ws->setCellValue('A' . $thRowNum, 'No');
                $ws->setCellValue('B' . $thRowNum, 'Item');
                $ws->setCellValue('C' . $thRowNum, 'Qty');
                $ws->setCellValue('D' . $thRowNum, 'Harga/Unit');
                $ws->setCellValue('E' . $thRowNum, 'Subtotal');
                $ws->setCellValue('F' . $thRowNum, 'Total');

                $ws->getRowDimension($thRowNum)->setRowHeight(22);
                $ws->getStyle(sprintf('A%d:F%d', $thRowNum, $thRowNum))->getFont()->setBold(true);

                $itemStartRow = $thRowNum + 1;
                $itemEndRow = $itemStartRow;
                foreach ($purchase->getItems() as $index => $item) {
                    $ws->setCellValue('A' . $itemEndRow, $index + 1);
                    $ws->setCellValue('B' . $itemEndRow, $item->getItemName());
                    $ws->setCellValue('C' . $itemEndRow, sprintf('%s %s', number_format($item->getQuantity(), 0), $item->getUnit()));
                    $ws->setCellValue('D' . $itemEndRow, $item->getUnitPrice());
                    $ws->setCellValue('E' . $itemEndRow, $item->getSubtotal());
                    $ws->setCellValue('F' . $itemEndRow, $item->getTotal());

                    $ws->getRowDimension($itemEndRow)->setRowHeight(22);
                    $itemEndRow++;;
                }

                $rollUpRowNum = $itemEndRow;
                $ws->getRowDimension($rollUpRowNum)->setRowHeight(22);
                $ws->mergeCells(sprintf('A%d:D%d', $rollUpRowNum, $rollUpRowNum));
                $ws->setCellValue('A' . $rollUpRowNum, 'Total');
                $ws->setCellValue('E' . $rollUpRowNum, sprintf('=SUM(E%d:E%d)', $itemStartRow, $itemEndRow - 1));
                $ws->setCellValue('F' . $rollUpRowNum, sprintf('=SUM(F%d:F%d)', $itemStartRow, $itemEndRow - 1));

                $noteRowNum = $rollUpRowNum + 2;
                if ($purchase->getNote()) {
                    $ws->mergeCells(sprintf('A%d:F%d', $noteRowNum, $noteRowNum));
                    $ws->getStyle('A' . $noteRowNum)->getFont()->setSize(13);
                    $ws->setCellValue('A' . $noteRowNum, 'Catatan:');

                    $ws->mergeCells(sprintf('A%d:F%d', $noteRowNum + 1, $noteRowNum + 1));
                    $ws->getStyle('A' . ($noteRowNum + 1))->getFont()->setSize(13);
                    $ws->setCellValue('A' . ($noteRowNum + 1), $purchase->getNote());
                }

                $ws->getStyle(sprintf('A%d:F%d', $thRowNum, $rollUpRowNum))->getBorders()->getAllBorders()
                    ->setBorderStyle(\PHPExcel_Style_Border::BORDER_THIN);

                $ws->getStyle(sprintf('D%d:D%d', $itemStartRow, $rollUpRowNum - 1))->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $ws->getStyle(sprintf('E%d:E%d', $itemStartRow, $rollUpRowNum))->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $ws->getStyle(sprintf('F%d:F%d', $itemStartRow, $rollUpRowNum))->getAlignment()
                    ->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

                $ws->getStyle(sprintf('D%d:D%d', $itemStartRow, $rollUpRowNum - 1))->getNumberFormat()
                    ->setFormatCode(sprintf('"%s" #,##0.00_-', $account->getDefaultCurrency()));

                $ws->getStyle(sprintf('E%d:E%d', $itemStartRow, $rollUpRowNum))->getNumberFormat()
                    ->setFormatCode(sprintf('"%s" #,##0.00_-', $account->getDefaultCurrency()));

                $ws->getStyle(sprintf('F%d:F%d', $itemStartRow, $rollUpRowNum))->getNumberFormat()
                    ->setFormatCode(sprintf('"%s" #,##0.00_-', $account->getDefaultCurrency()));

                $ws->getStyle(sprintf('A%d:F%d', $thRowNum, $rollUpRowNum))->getFont()->setSize(13);

            });

            $this->downloadExcelDoc($excel, $purchase->getTitle());
            return null;
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * Display edit form
     *
     * By clicking `View/Edit` on each row in the purchase list, user will be
     * navigated to this page. User can edit the details of the selected purchase
     * as well as the item line (description, quantity, unit, and total amount).
     *
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function editAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('purchase');

        try {
            $purchase   = $this->getPurchaseService()->getPurchaseWithItems($this->getRequestQuery('id'));
            $logs       = $this->getLogService()->findByPurchase($purchase);
            $account    = $this->account();

            $jsonItems = '[]';
            if ($purchase->getWarehousePurchase() instanceof WarehousePurchase) {
                $warehouse  = $purchase->getWarehousePurchase()->getWarehouse();
                $items      = $this->findStockItemInWarehouse($warehouse);
                $jsonItems  = json_encode($items);
            }

            return compact('purchase', 'account', 'logs', 'jsonItems');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * Update purchase status
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @throws \Exception
     */
    public function updateStatusAction()
    {
        $this->ensure('post');

        try {
            $purchase   = $this->getPurchaseService()->getPurchaseWithItems($this->getRequestQuery('id'));
            $fromStatus = $purchase->getStatus();

            $purchaseFlow = new PurchaseFlow($purchase);
            $purchaseFlow->setOrderedAt($this->getRequestPost('ordered_at'));
            $purchaseFlow->setDeliveredAt($this->getRequestPost('delivered_at'));
            $purchaseFlow->setCanceledAt($this->getRequestPost('canceled_at'));

            try {
                $status = $this->getRequestPost('status');
                $purchaseFlow->to($status);
                $this->persist($purchase)->commit();

                if ($this->getRequestPost('update_stock_level')) {
                    if ($status == Purchase::STATUS_DELIVERED) {
                        $this->getStockService()->increaseStock($purchase, $this->user(), $purchase->getItemsAsArray());
                    }
                }

                // log status changes
                $this->getLogService()->logChangePurchaseStatus($this->user(), $purchase, $fromStatus, $purchase->getStatus());

                return $this->jsonError(['status' => $status], 200);
            } catch (\Exception $ex) {
                // todo: limit to exception from flow
                return $this->jsonError(['error_summary' => $ex->getMessage()], 400);
            }
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * Update purchase data
     *
     * When user clicks submit button from the edit page, all data will be submitted here.
     * All inputs should be validated before they are persisted in to database.
     *
     * The flow is quite complex here,
     *
     * 1. Changing warehouse is not permitted, because the purchase items are related to a
     * stock in a warehouse (i.e. the stock ID is attached to each purchase item).
     *
     * 2. Changing status is limited to defined workflow (e.g. change status from `delivered`
     * to `planned` is not permitted).
     *
     * 3. We iterate through existing purchase items, and then save the data in a temporary
     * variable, because we are going to delete all previous items first to avoid conflict,
     * and insert new data into database. So, if we fail to insert new data and the previous
     * data is already deleted, we can still recover the old data and display a proper message
     * to the user.
     *
     * If user checked the `update_stock_level` checkbox and the status is set to `delivered`,
     * the stock level in the related warehouse will also be updated.
     *
     * 4. The `LogService` will only log data that are written successfully into database.
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateAction()
    {
        $this->ensure('post');

        try {
            $purchase       = $this->getPurchaseService()->getPurchaseWithItems($this->getRequestQuery('id'));
            $input          = $this->autoFilledInputFilter(PurchaseInput::class, $purchase);
            $currentStatus  = $purchase->getStatus();

            // prevent changing warehouse
            $input->remove('warehouse');

            if ($input->isValid()) {

                $data = $input->getValues();
                $this->mapper(Purchase::class)->getHydrator()->hydrate($data, $purchase);
                $this->persist($purchase)->commit();

                // in case we got exception, recover current item data
                $currentItems = $purchase->getItemsAsArray();
                $this->getPurchaseService()->removeItemsFromPurchase($purchase);

                try {
                    $this->insertRawItems($purchase, $data['raw_items']);

                    if ($this->getRequestPost('update_stock_level')) {
                        if (($data['status'] != $currentStatus) && ($data['status'] == Purchase::STATUS_DELIVERED)) {
                            $this->getStockService()->increaseStock($purchase, $this->user(), $data['raw_items']);
                        }
                    }

                    // log all changes
                    $this->getLogService()->logUpdatePurchase($this->user(), $purchase, $data);

                } catch (\Exception $e) {
                    $this->insertRawItems($purchase, $currentItems);

                    // log partial changes
                    $dataWithoutItems = $data;
                    $dataWithoutItems['raw_items'] = [];
                    $this->getLogService()->logUpdatePurchase($this->user(), $purchase, $dataWithoutItems);

                }

                return $this->jsonOk([], 200);
            }
            return $this->jsonError($input->getMessages(), 400);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * Delete selected purchase
     *
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete');
        $this->mapper(Purchase::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @param Purchase $purchase
     * @param array $rawItems
     * @throws \Doctrine\ORM\ORMException
     */
    private function insertRawItems(Purchase $purchase, array $rawItems)
    {
        foreach ($rawItems as $item) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase);
            $purchaseItem->setItemName($item['item_name']);
            $purchaseItem->setQuantity($item['quantity']);
            $purchaseItem->setUnitPrice($item['unit_price']);
            $purchaseItem->setSubtotal($item['subtotal']);
            $purchaseItem->setTotal($item['total']);
            $purchaseItem->setUnit($item['unit']);
            $this->em()->persist($purchaseItem);

            // add purchase item to stock?
            if ((isset($item['add_to_stock']) && $item['add_to_stock'])
                && (isset($item['unit_id']) && $item['unit_id'])
            ) {
                $this->getEventManager()->attach('items_inserted', $this->createStockItemFormItemLine(
                    $item['item_name'],
                    $item['unit_id'],
                    $item['quantity'],
                    $item['unit_price']
                ));
            }

            if (isset($item['stock']) && $item['stock']) {
                /** @var Stock $stock */
                $stock = $this->em()->getReference(Stock::class, $item['stock']);
                $stockPurchase = new StockPurchase();
                $stockPurchase->setStock($stock);
                $stockPurchase->setPurchaseItem($purchaseItem);
                $this->em()->persist($stockPurchase);
            }
        }
    }

    /**
     * @param $itemName
     * @param $unitId
     * @param $currentLevel
     * @param $unitPrice
     * @return callable
     */
    private function createStockItemFormItemLine($itemName, $unitId, $currentLevel, $unitPrice)
    {
        return function(Event $e) use($itemName, $unitId, $currentLevel, $unitPrice) {
            $stockItem = new StockItem();
            $stockItem->setName($itemName);

            /** @var StockUnit $unit */
            $unit = $this->mapper(StockUnit::class)->ref($unitId);

            $stockItem->setUsageUnit($unit);
            $stockItem->setStorageUnit($unit);

            $this->persist($stockItem)->commit();
            $this->getStockService()->addToAllWarehouse($stockItem);

            $warehouse = $e->getParam('warehouse');
            if ($warehouse instanceof Warehouse) {
                $stock = $this->em()->getRepository(Stock::class)->findOneBy([
                    'warehouse' => $warehouse,
                    'stock_item' => $stockItem
                ]);

                if ($stock instanceof Stock) {
                    $units = $this->getAssocStockUnit();
                    if (isset($units[$unitId])) {
                        $unit = $units[$unitId];
                        $stock->setCurrentLevel($currentLevel * $unit->getRatio());
                        $stock->setCurrentUnitPrice($unitPrice);
                        $this->persist($stock)->commit();
                    }
                }
            }
        };
    }

    /**
     * @return StockUnit[]
     */
    private function getAssocStockUnit()
    {
        if (empty($this->stockUnits)) {
            /** @var StockUnit[] $stockUnits */
            $stockUnits = $this->mapper(StockUnit::class)->all();
            foreach ($stockUnits as $stockUnit) {
                $this->stockUnits[$stockUnit->getId()] = $stockUnit;
            }
        }

        return $this->stockUnits;
    }

    /**
     * @param string $keywordInp
     * @param string $warehousesInp
     * @param string $fromInp
     * @param string $toInp
     * @param string $statusInp
     * @return callable
     */
    private function filterList($keywordInp, $warehousesInp, $fromInp, $toInp, $statusInp)
    {
        return function(QueryBuilder $builder) use($keywordInp, $warehousesInp, $fromInp, $toInp, $statusInp) {
            if (trim($keywordInp)) {
                $builder->andWhere('(p.title like :keyword or w.name LIKE :keyword or c.name LIKE :keyword)');
                $builder->setParameter('keyword', '%' . $keywordInp . '%');
            }

            if (is_array($warehousesInp) && count($warehousesInp)) {
                $builder->andWhere('w.id in (:warehouses)');
                $builder->setParameter('warehouses', $warehousesInp, Connection::PARAM_INT_ARRAY);
            }

            if ($fromInp && ($fromDT = \DateTime::createFromFormat('Y-m-d', $fromInp)) && ($fromDT instanceof \DateTime)) {
                $builder->andWhere('p.ordered_at > :from');
                $builder->setParameter('from', $fromDT);
            }

            if ($toInp && ($toDT = \DateTime::createFromFormat('Y-m-d', $toInp)) && ($toDT instanceof \DateTime)) {
                $builder->andWhere('p.ordered_at < :to');
                $builder->setParameter('to', $toDT);
            }

            if (trim($statusInp)) {
                $builder->andWhere('p.status = :status');
                $builder->setParameter('status', $statusInp);
            }

            $builder->orderBy('p.ordered_at', 'desc');
        };
    }

    /**
     * @return callable
     */
    private function stockEntityToFlatArray()
    {
        return function(Stock $obj) {
            return [
                'id'            => $obj->getId(),
                'item_id'       => $obj->getStockItem()->getId(),
                'item_name'     => $obj->getStockItem()->getName(),
                'item_code'     => $obj->getStockItem()->getCode(),
                'unit_id'       => $obj->getStockItem()->getStorageUnit()->getId(),
                'unit_name'     => $obj->getStockItem()->getStorageUnit()->getName(),
                'unit_ratio'    => $obj->getStockItem()->getStorageUnit()->getRatio()
            ];
        };
    }

    /**
     * @param Warehouse $warehouse
     * @return array
     */
    private function findStockItemInWarehouse(Warehouse $warehouse)
    {
        return $this->mapper(Stock::class)->filterAndCollect(function(QueryBuilder $builder) use($warehouse) {
            $builder->addSelect('sti, w', 'ssu', 'suu');
            $builder->leftJoin('st.stock_item', 'sti');
            $builder->leftJoin('sti.storage_unit', 'ssu');
            $builder->leftJoin('sti.usage_unit', 'suu');
            $builder->leftJoin('st.warehouse', 'w');
            $builder->where('w.id = :id');
            $builder->setParameter('id', $warehouse->getId());
        }, $this->stockEntityToFlatArray());
    }
}