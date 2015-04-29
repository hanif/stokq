<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\StorageType;
use Stokq\InputFilter\StorageTypeInput;

/**
 * Class StorageTypeController
 * @package Stokq\Controller\Web
 */
class StorageTypeController extends AuthenticatedController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(StorageType::class)->collect($this->storageTypeToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(StorageTypeInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(StorageType::class);
            $storageType = $mapper->insert($input->getValues());
            return $this->jsonOk($this->storageTypeToArray($storageType), 201);
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
            /** @var StorageType $storageType */
            $storageType = $this->mapper(StorageType::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(StorageTypeInput::class, $storageType);
            if ($input->isValid()) {
                $storageType = $this->mapper(StorageType::class)->getHydrator()->hydrate($input->getValues(), $storageType);
                $this->persist($storageType)->commit();
                return $this->jsonOk($this->storageTypeToArray($storageType), 200);
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
        $this->mapper(StorageType::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function storageTypeToArrayFunc()
    {
        return function(StorageType $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
            ];
        };
    }

    /**
     * @param StorageType $obj
     * @return array
     */
    protected function storageTypeToArray(StorageType $obj)
    {
        $func = $this->storageTypeToArrayFunc();
        return $func($obj);
    }
}