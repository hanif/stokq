<?php

namespace Stokq\Controller\Web;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Ingredient;
use Stokq\Entity\Menu;
use Stokq\InputFilter\IngredientInput;

/**
 * Class IngredientController
 * @package Stokq\Controller\Web
 */
class IngredientController extends AuthenticatedController
{
    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function ratioChartDataAction()
    {
        $this->ensure('get');

        try {
            $menu  = $this->mapper(Menu::class)->one($this->getRequestQuery('menu'));
            $types = $this->getMenuService()->getIngredientsOfGroupedByType($menu);

            $data = [['Jenis', 'Total']];
            foreach ($types as $type) {
                $total = 0;
                foreach ($type->getIngredients() as $ingredient) {
                    $total += $ingredient->getQtyPrice();
                }
                $data[] = [$type->getName(), $total];
            }
            return $this->jsonOk($data);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');

        $input = $this->autoFilledInputFilter(IngredientInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Ingredient::class);
            try {
                $ingredient = $mapper->insert($input->getValues());
                return $this->jsonOk($this->ingredientToArray($ingredient), 201);
            } catch (UniqueConstraintViolationException $e) {
                return $this->jsonError(['error_summary' => 'Item yang dipilih sudah pernah ditambahkan.'], 400);
            }
        }

        return $this->jsonError($input->getMessages(), 400);
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createRedirectAction()
    {
        $this->ensure('post');

        $input = $this->autoFilledInputFilter(IngredientInput::class);
        $menuId = $this->getRequestPost('menu');

        if ($input->isValid()) {
            $mapper = $this->mapper(Ingredient::class);
            try {
                $mapper->insert($input->getValues());
                return $this->redirect()->toRoute(...$this->routeSpec(sprintf('web.menu.detail?id=%d', $menuId)));
            } catch (UniqueConstraintViolationException $e) {
                $this->flashMessenger()->addErrorMessage('Item yang dipilih sudah pernah ditambahkan.');
                return $this->redirect()->toRoute(...$this->routeSpec(sprintf('web.menu.detail?id=%d', $menuId)));
            }
        }

        return $this->notFoundAction();
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateQuantityAction()
    {
        $this->ensure('post');

        try {
            /** @var Ingredient $ingredient */
            $ingredient = $this->mapper(Ingredient::class)->one($this->getRequestPost('id'));
            $quantity = abs(floatval($this->getRequestPost('quantity', 0)));
            $ingredient->setQuantity($quantity);
            $this->persist($ingredient)->commit();
            return $this->jsonOk([], 200);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateQtyPriceAction()
    {
        $this->ensure('post');

        try {
            /** @var Ingredient $ingredient */
            $ingredient = $this->mapper(Ingredient::class)->one($this->getRequestPost('id'));
            $quantity = abs(floatval($this->getRequestPost('qty_price', 0)));
            $ingredient->setQtyPrice($quantity);
            $this->persist($ingredient)->commit();
            return $this->jsonOk([], 200);
        } catch (NoResultException $e) {
            return $this->jsonError([], 404);
        }
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function updateNoteAction()
    {
        $this->ensure('post');

        try {
            /** @var Ingredient $ingredient */
            $ingredient = $this->mapper(Ingredient::class)->one($this->getRequestPost('id'));
            $ingredient->setNote($this->getRequestPost('note', ''));
            $this->persist($ingredient)->commit();
            return $this->jsonOk([], 200);
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
        $this->mapper(Ingredient::class)->delete($this->getRequestQuery('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function ingredientToArrayFunc()
    {
        return function(Ingredient $obj) {
            return [
                'id' => $obj->getId(),
                'quantity' => $obj->getQuantity(),
                'menu' => [
                    'id' => $obj->getMenu()->getId(),
                    'name' => $obj->getMenu()->getName(),
                    'servingUnit' => $obj->getMenu()->getServingUnit(),
                ],
                'stockItem' => [
                    'id' => $obj->getStockItem()->getId(),
                    'name' => $obj->getStockItem()->getName(),
                    'usageUnit' => [
                        'id' => $obj->getStockItem()->getUsageUnit()->getId(),
                        'name' => $obj->getStockItem()->getUsageUnit()->getName(),
                    ],
                ],
            ];
        };
    }

    /**
     * @param Ingredient $obj
     * @return array
     */
    private function ingredientToArray(Ingredient $obj)
    {
        $func = $this->ingredientToArrayFunc();
        return $func($obj);
    }
}