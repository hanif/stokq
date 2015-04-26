<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\StockUnit;
use Stokq\InputFilter\StockUnitInput;

/**
 * Class StockUnitController
 * @package Stokq\Controller\Web
 */
class StockUnitController extends AuthenticatedController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(StockUnit::class)->collect($this->stockUnitToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(StockUnitInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(StockUnit::class);
            $stockUnit = $mapper->insert($input->getValues());
            return $this->jsonOk($this->stockUnitToArray($stockUnit), 201);
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
            /** @var StockUnit $stockUnit */
            $stockUnit = $this->mapper(StockUnit::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(StockUnitInput::class, $stockUnit);
            if ($input->isValid()) {
                $stockUnit = $this->mapper(StockUnit::class)->getHydrator()->hydrate($input->getValues(), $stockUnit);
                $this->persist($stockUnit)->commit();
                return $this->jsonOk($this->stockUnitToArray($stockUnit), 200);
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
        $this->mapper(StockUnit::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function stockUnitToArrayFunc()
    {
        return function(StockUnit $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'ratio' => $obj->getRatio(),
                'description' => $obj->getDescription(),
                'type' => [
                    'id' => $obj->getType()->getId(),
                    'name' => $obj->getType()->getName(),
                ],
            ];
        };
    }

    /**
     * @param StockUnit $obj
     * @return array
     */
    protected function stockUnitToArray(StockUnit $obj)
    {
        $func = $this->stockUnitToArrayFunc();
        return $func($obj);
    }
}