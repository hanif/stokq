<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Classification;
use Stokq\InputFilter\ClassificationInput;

/**
 * Class ClassificationController
 * @package Stokq\Controller\Web
 */
class ClassificationController extends AuthenticatedController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Classification::class)->collect($this->classificationToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(ClassificationInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Classification::class);
            $classification = $mapper->insert($input->getValues());
            return $this->jsonOk($this->classificationToArray($classification), 201);
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
            /** @var Classification $classification */
            $classification = $this->mapper(Classification::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(ClassificationInput::class, $classification);
            if ($input->isValid()) {
                $classification = $this->mapper(Classification::class)->getHydrator()->hydrate($input->getValues(), $classification);
                $this->persist($classification)->commit();
                return $this->jsonOk($this->classificationToArray($classification), 200);
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
        $this->mapper(Classification::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function classificationToArrayFunc()
    {
        return function(Classification $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
            ];
        };
    }

    /**
     * @param Classification $obj
     * @return array
     */
    protected function classificationToArray(Classification $obj)
    {
        $func = $this->classificationToArrayFunc();
        return $func($obj);
    }
}