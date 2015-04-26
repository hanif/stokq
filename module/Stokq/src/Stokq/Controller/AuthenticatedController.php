<?php

namespace Stokq\Controller;

use Stokq\Controller\Exception\UnauthenticatedException;
use Stokq\Entity\Account;
use Stokq\Entity\User;
use Stokq\Stdlib\ExceptionHandler;
use Zend\Authentication\AuthenticationService;
use Zend\Http\PhpEnvironment\Request;
use Zend\Http\PhpEnvironment\Response;
use Zend\Stdlib\InitializableInterface;
use Zend\View\Model\ViewModel;

/**
 * Class AuthenticatedController
 * @package Stokq\Controller
 */
abstract class AuthenticatedController extends Controller implements InitializableInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * @throws Exception\UnauthenticatedException
     */
    public function init()
    {
        // handle unauthenticated exception
        if (($handler = $this->service(ExceptionHandler::class)) && ($handler instanceof ExceptionHandler)) {
            $handler->setHandler(UnauthenticatedException::class, function(UnauthenticatedException $e) {
                $request = $this->getRequest();
                $response = $this->getResponse();

                if (($request instanceof Request) && ($response instanceof Response)) {
                    if ($request->isXmlHttpRequest()) {
                        return $response->setStatusCode(403);
                    } else {
                        return $this->redirect()->toRoute(...$this->routeSpec('web.access.login'));
                    }
                } else {
                    return $response->setContent('Forbidden');
                }
            });
        }

        /** @var AuthenticationService $service */
        $service = $this->service(AuthenticationService::class);
        if (!$service->hasIdentity()) {
            throw new UnauthenticatedException();
        }
    }

    /**
     * @return User
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function user()
    {
        if ($this->user) {
            return $this->user;
        }

        /** @var AuthenticationService $authenticationService */
        $authenticationService = $this->service(AuthenticationService::class);
        if ($authenticationService->hasIdentity()) {
            $query = $this->em()->createQuery("select u from ent:User u where u.id = :id");
            $query->setParameter('id', $authenticationService->getIdentity());
            return $query->getSingleResult();
        }

        throw new \RuntimeException('Could not identify current user.');
    }

    /**
     * @return Account
     * @throws \Doctrine\ORM\NoResultException
     */
    public function account()
    {
        return $this->mapper(Account::class)->one(1);
    }

    /**
     * @return ViewModel
     */
    public function forbiddenAction()
    {
        $model = new ViewModel();
        $model->setTemplate('error/forbidden');
        return $model;
    }
}