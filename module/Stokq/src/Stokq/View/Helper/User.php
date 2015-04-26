<?php

namespace Stokq\View\Helper;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Stokq\Entity\User as UserEnt;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Application;
use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceManager;
use Zend\View\Helper\AbstractHelper;

/**
 * Class User
 * @package Stokq\View\Helper
 */
class User extends AbstractHelper implements ServiceLocatorAwareInterface
{
    /**
     * @var ServiceLocatorInterface|AbstractPluginManager
     */
    protected $serviceLocator;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var array
     */
    protected $applicationConfig = [];

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var UserEnt
     */
    protected $user;

    /**
     * @return $this|mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke()
    {
        if (!$this->user) {
            /** @var AuthenticationService $authenticationService */
            $authenticationService = $this->getServiceManager()->get(AuthenticationService::class);
            if ($authenticationService->hasIdentity()) {
                /** @var EntityManager $em */
                $em = $this->getServiceManager()->get(EntityManager::class);
                $query = $em->createQuery("select" . " u from ent:User u where u.id = :id");
                $query->setParameter('id', $authenticationService->getIdentity());

                try {
                    $this->user = $query->getSingleResult();
                } catch (NoResultException $e) {}
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function hasIdentity()
    {
        return ($this->user instanceof UserEnt);
    }

    /**
     * @return UserEnt
     */
    public function currentUser()
    {
        return $this->user;
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function ifLoggedIn(callable $callback)
    {
        if ($this->user instanceof UserEnt) {
            return $callback($this->user);
        }

        return null;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface|AbstractPluginManager $serviceLocator
     * @throws \InvalidArgumentException
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof AbstractPluginManager) {
            $this->serviceLocator = $serviceLocator;
            $this->serviceManager = $serviceLocator->getServiceLocator();
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Arg. passed to %s::setServiceLocator() must be instance of %s.',
                static::class, AbstractPluginManager::class
            ));
        }
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->serviceManager = $this->serviceLocator->getServiceLocator();
        }
        return $this->serviceManager;
    }

    /**
     * @return Application
     */
    public function getApp()
    {
        /** @var Application $app */
        return $this->getServiceManager()->get('Application');
    }
}