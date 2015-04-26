<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Type;
use Stokq\InputFilter\TypeInput;

/**
 * Class TypeController
 * @package Stokq\Controller\Web
 */
class TypeController extends AuthenticatedController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Type::class)->collect($this->typeToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(TypeInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Type::class);
            $type = $mapper->insert($input->getValues());
            return $this->jsonOk($this->typeToArray($type), 201);
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
            /** @var Type $type */
            $type = $this->mapper(Type::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(TypeInput::class, $type);
            if ($input->isValid()) {
                $type = $this->mapper(Type::class)->getHydrator()->hydrate($input->getValues(), $type);
                $this->persist($type)->commit();
                return $this->jsonOk($this->typeToArray($type), 200);
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
        $this->mapper(Type::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function typeToArrayFunc()
    {
        return function(Type $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
            ];
        };
    }

    /**
     * @param Type $obj
     * @return array
     */
    protected function typeToArray(Type $obj)
    {
        $func = $this->typeToArrayFunc();
        return $func($obj);
    }
}