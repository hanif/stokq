<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Outlet;
use Stokq\Entity\Warehouse;
use Stokq\InputFilter\OutletInput;

/**
 * Class OutletController
 * @package Stokq\Controller\Web
 */
class OutletController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('outlet');

        return [
            'account' => $this->account(),
            'warehouses' => $this->warehouseToArray($this->mapper(Warehouse::class)->all())
        ];
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function detailAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('outlet');

        $mapper  = $this->mapper(Outlet::class);
        $outlet  = $mapper->one($this->getRequestQuery('id'));
        $prices  = $this->getMenuService()->getMenuInOutlet($outlet);
        $account = $this->account();

        return compact('outlet', 'prices', 'account');
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Outlet::class)->collect($this->outletToArrayFunc())
        );
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listWithSalesAction()
    {
        $this->ensure('get');

        $transformData = function(array $row) {
            return $row + [
                'warehouse' => [
                    'id' => $row['warehouse_id'],
                    'name' => $row['warehouse_name'],
                ],
            ];
        };

        $data = $this->getOutletService()->findOutletWithSales();
        return $this->responseJson(array_map($transformData, $data));
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(OutletInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Outlet::class);
            $outlet = $mapper->insert($input->getValues());

            if ($this->getRequestPost('add_to_all')) {
                $this->getOutletService()->addAllMenu($outlet);
            }

            return $this->jsonOk($this->outletToArray($outlet), 201);
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
            /** @var Outlet $outlet */
            $outlet = $this->mapper(Outlet::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(OutletInput::class, $outlet);
            if ($input->isValid()) {
                $outlet = $this->mapper(Outlet::class)->getHydrator()->hydrate($input->getValues(), $outlet);
                $this->persist($outlet)->commit();
                return $this->jsonOk($this->outletToArray($outlet), 200);
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
        $this->ensure('post');
        $this->mapper(Outlet::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function outletToArrayFunc()
    {
        return function(Outlet $obj) {
            return [
                'id' => $obj->getId(),
                'warehouse' => [
                    'id' => $obj->getWarehouse()->getId(),
                    'name' => $obj->getWarehouse()->getName(),
                ],
                'name' => $obj->getName(),
                'address' => $obj->getAddress(),
                'description' => $obj->getDescription(),
                'longitude' => $obj->getLongitude(),
                'latitude' => $obj->getLatitude(),
            ];
        };
    }

    /**
     * @param Outlet $obj
     * @return array
     */
    private function outletToArray(Outlet $obj)
    {
        $func = $this->outletToArrayFunc();
        return $func($obj);
    }

    /**
     * @param Warehouse[] $warehouses
     * @return array
     */
    private function warehouseToArray(array $warehouses)
    {
        $arr = [];
        foreach ($warehouses as $warehouse) {
            $arr[] = [
                'id' => $warehouse->getId(),
                'name' => $warehouse->getName(),
            ];
        }
        return $arr;
    }
}