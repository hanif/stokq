<?php

namespace Stokq\Controller\Web;

use Doctrine\DBAL\Exception\ConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Menu;
use Stokq\Entity\MenuPrice;
use Stokq\Entity\Outlet;

/**
 * Class MenuPriceController
 * @package Stokq\Controller\Web
 */
class MenuPriceController extends AuthenticatedController
{
    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     * @throws \Doctrine\ORM\ORMException
     * @throws \Exception
     */
    public function createAction()
    {
        $this->ensure('post');
        $menuId = $this->getRequestQuery('menuId');
        $outletId = $this->getRequestQuery('outletId');

        if ($menuId && $outletId) {
            try {
                /** @var Menu $menu */
                $menu = $this->mapper(Menu::class)->one($menuId);

                /** @var Outlet $outlet */
                $outlet = $this->em()->getReference(Outlet::class, $outletId);

                $price = new MenuPrice();
                $price->setMenu($menu);
                $price->setPrice($menu->getDefaultPrice());
                $price->setOutlet($outlet);

                $this->persist($price)->commit();
                return $this->jsonOk($this->menuPriceToArray($price), 201);
            } catch (NoResultException $e) {
                return $this->jsonError(['error_summary' => 'Menu ID tidak valid.'], 404);
            } catch (ConstraintViolationException $e) {
                return $this->jsonError(['error_summary' => 'Menu yang dipilih sudah pernah ditambahkan di outlet yang dipilih.'], 400);
            }
        }

        return $this->jsonError(['error_summary' => 'Menu ID atau outlet ID tidak valid.'], 400);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updatePriceAction()
    {
        $this->ensure('post');

        try {
            /** @var MenuPrice $menuPrice */
            $menuPrice = $this->mapper(MenuPrice::class)->one($this->getRequestPost('id'));
            $price = abs(floatval($this->getRequestPost('price', 0)));
            $menuPrice->setPrice($price);
            $this->persist($menuPrice)->commit();
            return $this->jsonOk([], 200);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listByOutletAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(MenuPrice::class)->filterAndCollect(function(QueryBuilder $builder) {
                $builder->addSelect('m, o');
                $builder->leftJoin('mp.menu', 'm');
                $builder->leftJoin('mp.outlet', 'o');
                $builder->where('o.id = :id and m.status = :status');
                $builder->setParameter('id', $this->getRequestQuery('id'));
                $builder->setParameter('status', Menu::STATUS_ACTIVE);
            }, $this->menuPriceToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function deleteAction()
    {
        $this->ensure('delete');
        $this->mapper(MenuPrice::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function menuPriceToArrayFunc()
    {
        return function(MenuPrice $obj) {

            return [
                'id' => $obj->getId(),
                'price' => $obj->getPrice(),
                'menu' => [
                    'id' => $obj->getMenu()->getId(),
                    'name' => $obj->getMenu()->getName(),
                    'servingUnit' => $obj->getMenu()->getServingUnit(),
                    'defaultPrice' => $obj->getMenu()->getDefaultPrice(),
                    'status' => $obj->getMenu()->getStatus(),
                ],
                'outlet' => [
                    'id' => $obj->getOutlet()->getId(),
                    'name' => $obj->getOutlet()->getName(),
                ],
            ];
        };
    }

    /**
     * @param MenuPrice $obj
     * @return array
     */
    private function menuPriceToArray(MenuPrice $obj)
    {
        $func = $this->menuPriceToArrayFunc();
        return $func($obj);
    }
}