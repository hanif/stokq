<?php

namespace Stokq\Stdlib;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use DoctrineORMModule\Paginator\Adapter\DoctrinePaginator;
use Stokq\Stdlib\Hydrator\DoctrineObject;
use Zend\Filter\FilterInterface;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Paginator\Paginator as ZendPaginator;
use Zend\Stdlib\Hydrator\HydratorInterface;

/**
 * Class DataMapper
 * @package Stokq\Stdlib
 */
class DataMapper
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var HydratorInterface
     */
    protected $hydrator;

    /**
     * @var string
     */
    protected $entityClass;

    /**
     * @var FilterInterface
     */
    protected $methodNamingFilter;

    /**
     * @param EntityManager $entityManager
     * @param string $entityClass
     */
    public function __construct(EntityManager $entityManager, $entityClass)
    {
        $this->entityManager = $entityManager;
        $this->entityClass = $entityClass;
    }

    /**
     * @return string
     */
    public function getAlias()
    {
        return defined("{$this->entityClass}::ALIAS") ? constant("{$this->entityClass}::ALIAS") : 'e';
    }

    /**
     * @return FilterInterface
     */
    public function getMethodNamingFilter()
    {
        if (!$this->methodNamingFilter) {
            $this->methodNamingFilter = new UnderscoreToCamelCase();
        }
        return $this->methodNamingFilter;
    }

    /**
     * @param FilterInterface $methodNamingFilter
     */
    public function setMethodNamingFilter(FilterInterface $methodNamingFilter)
    {
        $this->methodNamingFilter = $methodNamingFilter;
    }

    /**
     * @return EntityRepository
     */
    public function getRepository()
    {
        return $this->entityManager->getRepository($this->entityClass);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        $entity = $this->entityManager->getRepository($this->entityClass)->find($id);
        return $entity;
    }

    /**
     * @param $id
     * @return mixed
     * @throws NoResultException
     */
    public function one($id)
    {
        if ($id && ($entity = $this->find($id))) {
            return $entity;
        }

        throw new NoResultException;
    }

    /**
     * @return mixed
     * @throws NoResultException
     */
    public function first()
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select($this->getAlias())
            ->from($this->entityClass, $this->getAlias())
            ->setMaxResults(1);
        return $builder->getQuery()->getSingleResult();
    }

    /**
     * @return int
     */
    public function count()
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select("count({$this->getAlias()})")->from($this->entityClass, $this->getAlias());
        return $builder->getQuery()->getSingleScalarResult();
    }

    /**
     * @return array
     */
    public function all()
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select($this->getAlias())->from($this->entityClass, $this->getAlias());
        return $builder->getQuery()->getResult();
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function filter(callable $callback)
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select($this->getAlias())->from($this->entityClass, $this->getAlias());
        $callback($builder);
        return $builder->getQuery()->getResult();
    }

    /**
     * @param callable $callback
     * @return Query
     */
    public function query(callable $callback)
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select($this->getAlias())->from($this->entityClass, $this->getAlias());
        $query = $callback($builder);
        if ($query instanceof QueryBuilder) {
            $query = $query->getQuery();
        }

        if (!$query instanceof Query) {
            throw new \RuntimeException('Callback result must be instance of Doctrine\ORM\Query.');
        }

        return $query;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function collect(callable $callback)
    {
        return Collection::collect($this->all(), $callback);
    }

    /**
     * @param callable $filter
     * @param callable $collector
     * @return array
     */
    public function filterAndCollect(callable $filter, callable $collector = null)
    {
        $result = $this->filter($filter);
        return $collector ? Collection::collect($result, $collector) : $result;
    }

    /**
     * @param callable $callback
     * @return array
     */
    public function process(callable $callback)
    {
        return Collection::process($this->all(), $callback);
    }

    /**
     * @param $query
     * @param int $page
     * @param int $pageSize
     * @return Paginator
     */
    public function paginate($query, $page, $pageSize)
    {
        if (!$query instanceof Query) {
            switch (true) {
                case ($query instanceof QueryBuilder):
                    $query = $query->getQuery();
                    break;

                case (is_string($query)):
                    $query = $this->entityManager->createQuery($query);
                    break;

                case (is_array($query) && count($query)):
                    $dql = array_shift($query);
                    $params = array_values($query);
                    $count = count($params);

                    $query = $this->entityManager->createQuery($dql);
                    for ($i = 1; $i < $count; $i++) {
                        $query->setParameter($i, $params[$i-1]);
                    }
                    break;

                default:
                    throw new \InvalidArgumentException('Invalid query given');
            }
        }

        $pages = new ZendPaginator(new DoctrinePaginator(new Paginator($query)));
        $pages->setItemCountPerPage($pageSize);
        $pages->setCurrentPageNumber($page);
        return $pages;
    }

    /**
     * @param object $target
     * @param bool $flush
     * @return object
     * @throws \DomainException
     */
    public function save($target, $flush = false)
    {
        if (!$target instanceof $this->entityClass) {
            throw new \DomainException(sprintf('Entity must be instance of `%s`.', $this->entityClass));
        }

        if (($target instanceof IdProviderInterface) && $target->getId()) {
            $target = $this->entityManager->merge($target);
        } else if (isset($target->id) && $target->id) {
            $this->entityManager->merge($target);
        } else {
            $this->entityManager->persist($target);
        }

        if ($flush) {
            $this->flush();
        }

        return $target;
    }

    /**
     * @param object|callable|string|int $target
     */
    public function delete($target)
    {
        if ($target instanceof $this->entityClass) {
            $this->entityManager->remove($target);
        } else if (is_scalar($target)) {
            $dql = sprintf('delete from'.' %s e where e.id = :id', $this->entityClass);
            $query = $this->entityManager->createQuery($dql);
            $query->setParameter('id', $target);
            $query->execute();
        } else if (is_callable($target)) {
            $qb = $this->entityManager->createQueryBuilder();
            $delete = $qb->delete($this->entityClass);
            $target($delete);
            $query = $qb->getQuery();
            $query->execute();
        } else {
            throw new \InvalidArgumentException('The delete method expect #1 argument to be an object, scalar, or callable.');
        }
    }

    /**
     * @param callable $callback
     * @return QueryBuilder
     */
    public function select(callable $callback = null)
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder->select($this->getAlias());
        $builder->from($this->entityClass, $this->getAlias());

        if ($callback) {
            $callback($builder);
        }

        return $builder;
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->entityManager->flush();
    }

    /**
     * @param array|object $data
     * @return mixed
     * @throws \DomainException
     */
    public function insert($data)
    {
        $entity = $this->create($data);
        $this->entityManager->flush();
        return $entity;
    }

    /**
     * @param array|object $data
     * @return mixed
     * @throws \DomainException
     */
    public function create($data)
    {
        if ($data instanceof $this->entityClass) {
            $this->entityManager->persist($data);
            return $data;
        } else if (is_array($data)) {
            $entity = new $this->entityClass;
            foreach ($data as $key => $value) {
                if ($key != 'id') {
                    $setter = sprintf('set%s', ucfirst($this->getMethodNamingFilter()->filter($key)));
                    if (method_exists($entity, $setter)) {
                        $entity->$setter($value);
                    } else if (property_exists(get_class($entity), $key)) {
                        $entity->{$key} = $value;
                    }
                }
            }
            $this->entityManager->persist($entity);
            return $entity;
        }

        throw new \DomainException(sprintf('Target must be an array or an instance of `%s`.', $this->entityClass));
    }

    /**
     * @param array $data
     * @return array
     */
    public function createMany(array $data)
    {
        $entities = [];
        foreach ($data as $datum) {
            $entities[] = $this->create($datum);
        }
        return $entities;
    }

    /**
     * @param object|array $data
     * @param object|array $condition
     * @return mixed
     */
    public function update($data, $condition)
    {
        $updater = function($entity) use($data) {
            foreach ($data as $key => $val) {
                $setter = sprintf('set%s', ucfirst($this->getMethodNamingFilter()->filter($key)));
                if (is_object($entity) && method_exists($entity, $setter)) {
                    $entity->$setter($val);
                } else if (property_exists(get_class($entity), $key)) {
                    $entity->{$key} = $val;
                }
            }
            $this->entityManager->persist($entity);
        };

        if (is_array($condition)) {
            $record = $this->getRepository()->findBy($condition);
            foreach ($record as $entity) {
                $updater($entity);
            }
        } else {
            $record = $updater($this->getRepository()->find($condition));
        }

        $this->entityManager->flush();
        return $record;
    }

    /**
     * @param string $class
     */
    public function setEntityClass($class)
    {
        $this->entityClass = $class;
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return $this->entityClass;
    }

    /**
     * @return object
     */
    public function getManager()
    {
        return $this->entityManager;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function ref($id)
    {
        return $this->entityManager->getReference($this->entityClass, $id);
    }

    /**
     * @return array|object
     */
    public function getPrototype()
    {
        return new $this->entityClass;
    }

    /**
     * @param array|object $prototype
     */
    public function setPrototype($prototype)
    {
        $this->setEntityClass(get_class($prototype));
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager()
    {
        return $this->entityManager;
    }

    /**
     * @param string $class
     */
    public function setObjectClass($class)
    {
        $this->setEntityClass($class);
    }

    /**
     * @return string
     */
    public function getObjectClass()
    {
        return $this->getEntityClass();
    }

    /**
     * @return HydratorInterface
     */
    public function getHydrator()
    {
        if (!$this->hydrator) {
            $this->hydrator = new DoctrineObject($this->entityManager);
        }

        return $this->hydrator;
    }

    /**
     * @param HydratorInterface $hydrator
     * @return mixed
     */
    public function setHydrator(HydratorInterface $hydrator)
    {
        $this->hydrator = $hydrator;
    }
}