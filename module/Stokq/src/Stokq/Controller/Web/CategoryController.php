<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Category;
use Stokq\InputFilter\CategoryInput;

/**
 * Class CategoryController
 * @package Stokq\Controller\Web
 */
class CategoryController extends AuthenticatedController
{
    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(Category::class)->collect($this->categoryToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(CategoryInput::class);
        if ($input->isValid()) {
            $mapper = $this->mapper(Category::class);
            $category = $mapper->insert($input->getValues());
            return $this->jsonOk($this->categoryToArray($category), 201);
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
            /** @var Category $category */
            $category = $this->mapper(Category::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(CategoryInput::class, $category);
            if ($input->isValid()) {
                $category = $this->mapper(Category::class)->getHydrator()->hydrate($input->getValues(), $category);
                $this->persist($category)->commit();
                return $this->jsonOk($this->categoryToArray($category), 200);
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
        $this->mapper(Category::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function categoryToArrayFunc()
    {
        return function(Category $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
            ];
        };
    }

    /**
     * @param Category $obj
     * @return array
     */
    protected function categoryToArray(Category $obj)
    {
        $func = $this->categoryToArrayFunc();
        return $func($obj);
    }
}