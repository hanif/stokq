<?php

namespace Stokq\Controller\Web;

use Doctrine\ORM\NoResultException;
use Stokq\Controller\AuthenticatedController;
use Stokq\Entity\Token;
use Stokq\Entity\User;
use Stokq\InputFilter\UserInput;

/**
 * Class UserController
 * @package Stokq\Controller\Web
 */
class UserController extends AuthenticatedController
{
    /**
     * @return array|\Zend\Stdlib\ResponseInterface
     */
    public function indexAction()
    {
        $this->ensure('get');
        $this->layout('layout/single-column');
        $this->getNavService()->setActive('setting');

        return [];
    }

    /**
     * @return \Zend\Stdlib\ResponseInterface
     */
    public function listAction()
    {
        $this->ensure('get');
        return $this->responseJson(
            $this->mapper(User::class)->collect($this->userToArrayFunc())
        );
    }

    /**
     * @return \Zend\Http\Response|\Zend\Stdlib\Message
     */
    public function createAction()
    {
        $this->ensure('post');
        $input = $this->autoFilledInputFilter(UserInput::class);
        if ($input->isValid()) {
            $user  = $this->mapper(User::class)->create($input->getValues());
            $this->persist($user)->commit(); // to obtain user ID

            $token = $this->getUserService()->createToken($this->mapper(Token::class), $user, 'activation');
            $this->persist($token)->commit();

            $message = $this->getUserService()->createActivationEmailMessage($user, $token);
            $this->getMailerService()->send($message);

            return $this->jsonOk($this->userToArray($user), 201);
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
            $user = $this->mapper(User::class)->one($this->getRequestPost('id'));
            $input = $this->autoFilledInputFilter(UserInput::class, $user);
            if ($input->isValid()) {
                $this->mapper(User::class)->update($input->getValues(), $this->getRequestPost('id'));
                return $this->jsonOk($this->userToArray($user), 201);
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
        $this->mapper(User::class)->delete($this->getRequestPost('id'));
        return $this->ok(204);
    }

    /**
     * @return callable
     */
    public function userToArrayFunc()
    {
        return function(User $obj) {
            return [
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'email' => $obj->getEmail(),
                'status' => $obj->getStatus(),
                'contactNo' => $obj->getContactNo(),
                'address' => $obj->getAddress(),
                'timezone' => $obj->getTimezone()
            ];
        };
    }

    /**
     * @param User $obj
     * @return array
     */
    protected function userToArray(User $obj)
    {
        $func = $this->userToArrayFunc();
        return $func($obj);
    }
}