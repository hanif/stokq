<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Menu;
use Stokq\Entity\StorageType;
use Stokq\Entity\Warehouse;
use Stokq\InputFilter\WarehouseInput;

/**
 * Class WarehouseController
 * @package Stokq\Controller\Web
 */
class WarehouseController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('warehouse');

        return [];
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function listLowAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('warehouse');

        $keyword    = $this->getRequestQuery('q');
        $warehouse  = $this->getRequestQuery('warehouse');
        $type       = $this->getRequestQuery('type');
        $menu       = $this->getRequestQuery('menu');
        $list       = $this->getWarehouseService()->findLowStocks($this->filterList($keyword, $warehouse, $type, $menu));
        $user       = $this->user();
        $account    = $this->account();
        $menus      = $this->mapper(Menu::class)->all();
        $types      = $this->mapper(StorageType::class)->all();
        $warehouses = $this->mapper(Warehouse::class)->all();

        return compact(
            'list', 'user', 'account', 'menus',
            'types', 'warehouses', 'keyword',
            'warehouse', 'type', 'menu'
        );
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function detailAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('warehouse');

        try {
            $warehouse  = $this->mapper(Warehouse::class)->one($this->getRequestQuery('id'));
            $changelog  = $this->getWarehouseService()->getChangelogInRange($warehouse, new \DateTime('-70 days'), new \DateTime());
            $stockItems = $this->getStockService()->getStockItemInWarehouse($warehouse);

            return compact('warehouse', 'stockItems', 'changelog');
        } catch (NoResultException $e) {
            return $this->notFoundAction();
        }
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Warehouse::class)->collect($this->warehouseToArrayFunc())
        );
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listWithIndicatorAction()
    {
        $this->ensure('get');
        return $this->responseJson($this->getWarehouseService()->findWarehouseWithIndicator());
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(WarehouseInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Warehouse::class);
            $warehouse = $mapper->insert($input->getValues());

            if ($this->getRequestPost('add_to_all')) {
                $this->getWarehouseService()->addAllStockItems($warehouse);
            }

            return $this->jsonOk($this->warehouseToArray($warehouse), 201);
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateAction()
    {
        $this->ensure('post');

        try {
            $warehouse = $this->mapper(Warehouse::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(WarehouseInput::class, $warehouse);
            if ($input->isValid()) {
                $this->mapper(Warehouse::class)->getHydrator()->hydrate($input->getValues(), $warehouse);
                $this->persist($warehouse)->commit();
                return $this->jsonOk($this->warehouseToArray($warehouse), 200);
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
        $this->mapper(Warehouse::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function warehouseToArrayFunc()
    {
        return function(Warehouse $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'address' => $obj->getAddress(),
                'description' => $obj->getDescription(),
                'longitude' => $obj->getLongitude(),
                'latitude' => $obj->getLatitude(),
            ];
        };
    }

    /**
     * @param Warehouse $obj
     * @return array
     */
    private function warehouseToArray(Warehouse $obj)
    {
        $func = $this->warehouseToArrayFunc();
        return $func($obj);
    }

    /**
     * @param string $keyword
     * @param string $warehouse
     * @param string $type
     * @param string $menu
     * @return callable
     */
    private function filterList($keyword, $warehouse, $type, $menu)
    {
        return function(QueryBuilder $builder) use($keyword, $warehouse, $type, $menu) {
            if (trim($keyword)) {
                $builder->andWhere('(sti.name like :keyword or sti.code LIKE :keyword or sti.description LIKE :keyword)');
                $builder->setParameter('keyword', '%' . $keyword . '%');
            }

            if (trim($warehouse)) {
                $builder->andWhere('(st.warehouse = :warehouse)');
                $builder->setParameter('warehouse', $warehouse);
            }

            if (trim($type)) {
                $builder->andWhere('(sti.type = :type)');
                $builder->setParameter('type', $type);
            }

            if (trim($menu)) {
                $builder->andWhere('(i.menu = :menu)');
                $builder->setParameter('menu', $menu);
            }

            $builder->orderBy('sti.name', 'asc');
        };
    }
}