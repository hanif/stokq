<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\QueryBuilder;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\IngredientType;
use Stokq\Entity\Menu;
use Stokq\Entity\StockItem;
use Stokq\Entity\Type;
use Stokq\InputFilter\MenuInput;

/**
 * Class MenuController
 * @package Stokq\Controller\Web
 */
class MenuController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('menu');

        $parents = $this->parentMenuToArray($this->getMenuService()->findAllParentMenu());
        $types   = $this->typesToArray($this->mapper(Type::class)->all());

        return compact('types', 'parents');
    }

    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function detailAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('menu');

        $mapper          = $this->mapper(Menu::class);
        $menu            = $mapper->one($this->getRequestQuery('id'));
        $ingredients     = $this->getMenuService()->getIngredientsOfGroupedByType($menu);
        $ingredientTypes = $this->mapper(IngredientType::class)->all();
        $prices          = $this->getMenuService()->getPricePerOutlet($menu);
        $stockItems      = $this->getStockService()->queryStockItems($this->mapper(StockItem::class))->getResult();
        $account         = $this->account();

        return compact('menu', 'ingredients', 'ingredientTypes', 'prices', 'stockItems', 'account');
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Menu::class)->filterAndCollect(function(QueryBuilder $builder) {
                $builder->addSelect('mt, t');
                $builder->leftJoin('m.menu_types', 'mt');
                $builder->leftJoin('mt.type', 't');
            }, $this->menuToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(MenuInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Menu::class);
            $menu = $mapper->insert($input->getValues());

            if ($this->getRequestPost('add_to_all')) {
                $this->getMenuService()->addToAllOutlet($menu);
            }

            return $this->jsonOk($this->menuToArray($menu), 201);
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
            /** @var Menu $menu */
            $menu = $this->mapper(Menu::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(MenuInput::class, $menu);
            if ($input->isValid()) {

                // avoid integrity constraint violation
                $menu->removeAttachedTypes($this->em());

                $menu = $this->mapper(Menu::class)->getHydrator()->hydrate($input->getValues(), $menu);
                $this->persist($menu)->commit();
                return $this->jsonOk($this->menuToArray($menu), 200);
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
        $this->ensure('delete', 'post');
        $this->mapper(Menu::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function menuToArrayFunc()
    {
        return function(Menu $obj) {

            $types = [];
            $menuTypes = [];
            foreach ($obj->getMenuTypes() as $menuType) {
                $types[] = $menuType->getType()->getId();
                $menuTypes[] = [
                    'id' => $menuType->getId(),
                    'type' => [
                        'id' => $menuType->getType()->getId(),
                        'name' => $menuType->getType()->getName(),
                    ]
                ];
            }

            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'hasParent' => $obj->getParent() ? true : false,
                'parent' => [
                    'id' => $obj->getParent() ? $obj->getParent()->getId() : 0,
                    'name' => $obj->getParent() ? $obj->getParent()->getName() : null,
                ],
                'types' => $types,
                'menuTypes' => $menuTypes,
                'createdAt' => $obj->getCreatedAt()->format(DATE_ISO8601),
                'description' => $obj->getDescription(),
                'servingUnit' => $obj->getServingUnit(),
                'defaultPrice' => $obj->getDefaultPrice(),
                'status' => $obj->getStatus(),
                'note' => $obj->getNote(),
            ];
        };
    }

    /**
     * @param Menu $obj
     * @return array
     */
    private function menuToArray(Menu $obj)
    {
        $func = $this->menuToArrayFunc();
        return $func($obj);
    }

    /**
     * @param Type[] $types
     * @return array
     */
    private function typesToArray(array $types)
    {
        $arr = [];
        foreach ($types as $type) {
            $arr[] = [
                'id' => $type->getId(),
                'name' => $type->getName(),
            ];
        }
        return $arr;
    }

    /**
     * @param Menu[] $menu
     * @return array
     */
    private function parentMenuToArray(array $menu)
    {
        $arr = [];
        foreach ($menu as $obj) {
            $arr[] = [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
            ];
        }
        return $arr;
    }
}