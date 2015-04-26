<?php

namespace Stokq\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Stokq\Service\Exception\AssertionException;
use Zend\Paginator\Paginator as ZendPaginator;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class AbstractService
 * @package Stokq\Service
 */
abstract class AbstractService implements ServiceInterface, ServiceLocatorAwareInterface, LoggerAwareInterface
{
    /**
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        if (!$this->serviceLocator) {
            throw new \RuntimeException('Service locator has not been set.');
        }

        return $this->serviceLocator;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->getServiceLocator()->get(EntityManager::class);
    }

    /**
     * @return EntityManager
     */
    public function em()
    {
        return $this->getEntityManager();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function getConnection()
    {
        return $this->getEntityManager()->getConnection();
    }

    /**
     * @return \Doctrine\DBAL\Connection
     */
    public function db()
    {
        return $this->getConnection();
    }

    /**
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!$this->logger instanceof LoggerInterface) {
            $this->logger = $this->getServiceLocator()->get(LoggerInterface::class);
        }

        return $this->logger;
    }

    /**
     * @return LoggerInterface
     */
    public function logger()
    {
        return $this->getLogger();
    }

    /**
     * @param mixed $expr
     * @param string $message
     * @throws AssertionException
     */
    public function assert($expr, $message = null)
    {
        if ($expr === false) {
            throw new AssertionException($message ?: 'Given expression returns false.');
        }
    }

    /**
     * @param Paginator $paginator
     * @param $page
     * @param $pageSize
     * @return ZendPaginator
     */
    public function paginatePaginator(Paginator $paginator, $page, $pageSize)
    {
        $pages = new ZendPaginator(new DoctrinePaginator($paginator));
        $pages->setItemCountPerPage($pageSize);
        $pages->setCurrentPageNumber($page);
        return $pages;
    }

    /**
     * @param Query $query
     * @param $page
     * @param $pageSize
     * @return ZendPaginator
     */
    public function paginateQuery(Query $query, $page, $pageSize)
    {
        $pages = new ZendPaginator(new DoctrinePaginator(new Paginator($query)));
        $pages->setItemCountPerPage($pageSize);
        $pages->setCurrentPageNumber($page);
        return $pages;
    }

    /**
     * @param QueryBuilder $builder
     * @param $page
     * @param $pageSize
     * @return ZendPaginator
     */
    public function paginateQueryBuilder(QueryBuilder $builder, $page, $pageSize)
    {
        $pages = new ZendPaginator(new DoctrinePaginator(new Paginator($builder)));
        $pages->setItemCountPerPage($pageSize);
        $pages->setCurrentPageNumber($page);
        return $pages;
    }

    /**
     * @param $sql
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Exception
     */
    public function executeSql($sql)
    {
        $this->db()->beginTransaction();
        try {
            $this->db()->exec($sql);
            $this->db()->commit();
        } catch (\Exception $e) {
            $this->db()->rollback();
            throw $e;
        }
    }
}