<?php

namespace Stokq\Controller\Web;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Controller\Mixin\ExcelDocUtilsMixin;
use Stokq\Entity\Menu;
use Stokq\Entity\MenuPrice;
use Stokq\Entity\MenuSale;
use Stokq\Entity\Outlet;
use Stokq\Entity\OutletSale;
use Stokq\Entity\Sale;
use Stokq\Entity\SaleItem;
use Stokq\Entity\User;
use Stokq\InputFilter\SaleInput;

/**
 * Class SaleController
 * @package Stokq\Controller\Web
 */
class SaleController extends AuthenticatedController
{
    use ExcelDocUtilsMixin;

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('sale');

        $keywordInp  = $this->getRequestQuery('q');
        $outletsInp  = $this->getRequestQuery('outlets');
        $fromInp     = $this->getRequestQuery('from');
        $toInp       = $this->getRequestQuery('to');
        $cbFilter    = $this->filterList($keywordInp, $outletsInp, $fromInp, $toInp);
        $query       = $this->getSaleService()->getSaleWithTotalQuery($cbFilter);
        $page        = $this->getRequestQuery('page', 1);
        $pageSize    = $this->getRequestQuery('pageSize', 20);
        $pages       = $this->mapper(Sale::class)->paginate($query, $page, $pageSize);
        $user        = $this->user();
        $account     = $this->account();
        $outlets     = $this->mapper(Outlet::class)->all();

        return compact('outlets', 'pages', 'page', 'user', 'account') +
        [
            'selectedOutlets' => $outletsInp,
            'keyword'         => $keywordInp,
            'from'            => $fromInp,
            'to'              => $toInp
        ];
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function newAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('sale');

        try {
            $outlet  = $this->mapper(Outlet::class)->one($this->getRequestQuery('id'));
            $items   = $this->findMenuInOutlet($outlet);
            $account = $this->account();
            $user    = $this->user();

            return compact('outlet', 'user', 'account', 'items');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');

        $input = $this->autoFilledInputFilter(SaleInput::class);

        if ($input->isValid()) {
            $data = $input->getValues();
            $sale = $this->mapper(Sale::class)->create($data + ['creator' => $this->user()]);

            $this->insertRawItems($sale, $data['raw_items']);
            $this->commit();

            // reduce stock in warehouse only if sale has associated warehouse
            if ($this->getRequestPost('update_stock_level') && ($sale->getOutletSale() instanceof OutletSale)) {
                $this->getStockService()->reduceStock($sale, $data['raw_items']);
            }

            // do logging
            $this->getLogService()->logCreateSale($this->user(), $sale, $data);

            return $this->jsonOk([], 201);
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function printAction()
    {
        $this->ensure('get');

        try {
            $sale = $this->getSaleService()->getSaleWithItems($this->getRequestQuery('id'));
            $account = $this->account();
            return compact('sale', 'account');
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
            $sale = $this->getSaleService()->getSaleWithItems($this->getRequestQuery('id'));
            $account = $this->account();

            // set excel working directory
            $config = $this->getServiceManager()->get('ApplicationConfig');
            if (isset($config['cache_dir'])) {
                $this->setExcelDocsWorkingDir($config['cache_dir']);
            } else {
                $this->setExcelDocsWorkingDir(sys_get_temp_dir());
            }

            $excel = $this->createExcelDoc(function(\PHPExcel $excel) use($sale, $account) {
                $ws = $excel->getActiveSheet();

                $ws->mergeCells('A2:F2');
                $ws->setCellValue('A2', $sale->getTitle());
                $ws->getRowDimension(2)->setRowHeight(30);
                $ws->getStyle('A2')->getFont()->setBold(true);
                $ws->getStyle('A2')->getFont()->setSize(18);

                $thRowNum = 4;
                if ($sale->getOutletSale() instanceof OutletSale) {
                    $ws->mergeCells('A4:F4');
                    $ws->setCellValue('A4', sprintf('Outlet: %s', $sale->getOutletSale()->getOutlet()->getName()));
                    $ws->getStyle('A4')->getFont()->setSize(13);
                    $ws->getRowDimension(4)->setRowHeight(22);
                    $thRowNum += 1;
                }

                if ($sale->getCreator() instanceof User) {
                    $ws->mergeCells('A5:F5');
                    $ws->setCellValue('A5', sprintf('Dibuat Oleh: %s', $sale->getCreator()->getName()));
                    $ws->getStyle('A5')->getFont()->setSize(13);
                    $ws->getRowDimension(5)->setRowHeight(22);
                    $thRowNum += 1;
                }

                if ($sale->getCreatedAt() instanceof \DateTime) {
                    $ws->mergeCells('A6:F6');
                    $ws->setCellValue('A6', sprintf('Tgl. Dibuat: %s', $sale->getCreatedAt()->format('d M Y')));
                    $ws->getStyle('A6')->getFont()->setSize(13);
                    $ws->getRowDimension(6)->setRowHeight(22);
                    $thRowNum += 1;
                }

                $thRowNum += 1;

                $ws->getColumnDimension('A')->setWidth(5);
                $ws->getColumnDimension('B')->setWidth(32);
                $ws->getColumnDimension('C')->setWidth(14);
                $ws->getColumnDimension('D')->setWidth(20);
                $ws->getColumnDimension('E')->setWidth(20);
                $ws->getColumnDimension('F')->setWidth(20);

                $ws->setCellValue('A' . $thRowNum, 'No');
                $ws->setCellValue('B' . $thRowNum, 'Menu');
                $ws->setCellValue('C' . $thRowNum, 'Qty');
                $ws->setCellValue('D' . $thRowNum, 'Harga/Unit');
                $ws->setCellValue('E' . $thRowNum, 'Subtotal');
                $ws->setCellValue('F' . $thRowNum, 'Total');

                $ws->getRowDimension($thRowNum)->setRowHeight(22);
                $ws->getStyle(sprintf('A%d:F%d', $thRowNum, $thRowNum))->getFont()->setBold(true);

                $itemStartRow = $thRowNum + 1;
                $itemEndRow = $itemStartRow;
                foreach ($sale->getItems() as $index => $item) {
                    $ws->setCellValue('A' . $itemEndRow, $index + 1);
                    $ws->setCellValue('B' . $itemEndRow, $item->getItemName());
                    $ws->setCellValue('C' . $itemEndRow, $item->getQuantity());
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
                if ($sale->getNote()) {
                    $ws->mergeCells(sprintf('A%d:F%d', $noteRowNum, $noteRowNum));
                    $ws->getStyle('A' . $noteRowNum)->getFont()->setSize(13);
                    $ws->setCellValue('A' . $noteRowNum, 'Catatan:');

                    $ws->mergeCells(sprintf('A%d:F%d', $noteRowNum + 1, $noteRowNum + 1));
                    $ws->getStyle('A' . ($noteRowNum + 1))->getFont()->setSize(13);
                    $ws->setCellValue('A' . ($noteRowNum + 1), $sale->getNote());
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

            $this->downloadExcelDoc($excel, $sale->getTitle());
            return null;
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function editAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('sale');

        try {
            $sale    = $this->getSaleService()->getSaleWithItems($this->getRequestQuery('id'));
            $logs    = $this->getLogService()->findBySale($sale);
            $account = $this->account();

            return compact('sale', 'account', 'logs');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateAction()
    {
        $this->ensure('post');

        try {
            $sale = $this->getSaleService()->getSaleWithItems($this->getRequestQuery('id'));
            $input = $this->autoFilledInputFilter(SaleInput::class);

            // prevent changing outlet
            $input->remove('outlet');

            if ($input->isValid()) {

                $data = $input->getValues();
                $this->mapper(Sale::class)->getHydrator()->hydrate($data, $sale);
                $this->persist($sale)->commit();

                // in case we got exception, recover current item data
                $currentItems = $sale->getItemsAsArray();
                $this->getSaleService()->removeItemsFromSale($sale);

                try {
                    $this->insertRawItems($sale, $data['raw_items']);

                    if ($this->getRequestPost('update_stock_level') && ($sale->getOutletSale() instanceof OutletSale)) {
                        $warehouse = $sale->getOutletSale()->getOutlet()->getWarehouse();
                        $this->getStockService()->reduceStock($warehouse, $this->user(), $data['raw_items'], true);
                    }

                    // log all changes
                    $this->getLogService()->logUpdateSale($this->user(), $sale, $data);

                } catch (\Exception $e) {
                    $this->insertRawItems($sale, $currentItems);

                    // log partial changes
                    $dataWithoutItems = $data;
                    $dataWithoutItems['raw_items'] = [];
                    $this->getLogService()->logUpdateSale($this->user(), $sale, $dataWithoutItems);

                }

                return $this->jsonOk([], 200);
            }
            return $this->jsonError($input->getMessages(), 400);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete');
        $this->mapper(Sale::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @param Sale $sale
     * @param array $rawItems
     * @throws \Doctrine\ORM\ORMException
     */
    private function insertRawItems(Sale $sale, array $rawItems)
    {
        foreach ($rawItems as $item) {
            $saleItem = new SaleItem();
            $saleItem->setSale($sale);
            $saleItem->setItemName($item['item_name']);
            $saleItem->setUnitPrice($item['unit_price']);
            $saleItem->setQuantity($item['quantity']);
            $saleItem->setSubtotal($item['subtotal']);
            $saleItem->setTotal($item['total']);
            $this->em()->persist($saleItem);

            if (isset($item['menu_price']) && $item['menu_price']) {
                /** @var MenuPrice $menuPrice */
                $menuPrice = $this->em()->getReference(MenuPrice::class, $item['menu_price']);
                $menuSale = new MenuSale();
                $menuSale->setMenuPrice($menuPrice);
                $menuSale->setSaleItem($saleItem);
                $this->em()->persist($menuSale);
            }
        }
    }

    /**
     * @param string $keywordInp
     * @param string $outletsInp
     * @param string $fromInp
     * @param string $toInp
     * @return callable
     */
    private function filterList($keywordInp, $outletsInp, $fromInp, $toInp)
    {
        return function(QueryBuilder $builder) use($keywordInp, $outletsInp, $fromInp, $toInp) {
            if (trim($keywordInp)) {
                $builder->andWhere('(s.title like :keyword or o.name LIKE :keyword or c.name LIKE :keyword)');
                $builder->setParameter('keyword', '%' . $keywordInp . '%');
            }

            if (is_array($outletsInp) && count($outletsInp)) {
                $builder->andWhere('o.id in (:outlets)');
                $builder->setParameter('outlets', $outletsInp, Connection::PARAM_INT_ARRAY);
            }

            if ($fromInp && ($fromDT = \DateTime::createFromFormat('Y-m-d', $fromInp)) && ($fromDT instanceof \DateTime)) {
                $builder->andWhere('s.ordered_at > :from');
                $builder->setParameter('from', $fromDT);
            }

            if ($toInp && ($toDT = \DateTime::createFromFormat('Y-m-d', $toInp)) && ($toDT instanceof \DateTime)) {
                $builder->andWhere('s.ordered_at < :to');
                $builder->setParameter('to', $toDT);
            }

            $builder->orderBy('s.ordered_at', 'desc');
        };
    }

    /**
     * @param Outlet $outlet
     * @return array
     */
    private function findMenuInOutlet(Outlet $outlet)
    {
        return $this->mapper(MenuPrice::class)->filter(function(QueryBuilder $builder) use($outlet) {
            $builder->addSelect('m, o');
            $builder->leftJoin('mp.menu', 'm');
            $builder->leftJoin('mp.outlet', 'o');
            $builder->where('o.id = :id and m.status = :status');
            $builder->setParameter('id', $outlet->getId());
            $builder->setParameter('status', Menu::STATUS_ACTIVE);
        });
    }
}