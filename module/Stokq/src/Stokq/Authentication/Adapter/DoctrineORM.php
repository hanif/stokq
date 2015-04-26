<?php

namespace Stokq\Authentication\Adapter;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NoResultException;
use Stokq\Authentication\AuthenticableInterface;
use Stokq\Stdlib\IdProviderInterface;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Zend\Crypt\Password\PasswordInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

/**
 * Class DoctrineORM
 * @package Stokq\Authentication\Adapter
 */
class DoctrineORM implements AdapterInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var string
     */
    protected $identity;

    /**
     * @var string
     */
    protected $credentials;

    /**
     * @param ServiceLocatorInterface $service
     */
    public function __construct(ServiceLocatorInterface $service)
    {
        $this->setServiceLocator($service);
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->credentials;
    }

    /**
     * @param string $credentials
     */
    public function setCredentials($credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->identity;
    }

    /**
     * @param string $identity
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
    }

    /**
     * @return PasswordInterface
     */
    public function getPasswordService()
    {
        $passwordService = $this->getServiceLocator()->get('auth_password');
        if (!$passwordService instanceof PasswordInterface) {
            throw new \RuntimeException('Password service is not configured properly');
        }
        return $passwordService;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        $em = $this->getServiceLocator()->get(EntityManager::class);
        if (!$em instanceof EntityManager) {
            throw new \RuntimeException('Entity manager is not configured properly');
        }
        return $em;
    }

    /**
     * @param string $name
     * @param bool $errorIfNotSet
     * @return string|null
     */
    public function getConfig($name, $errorIfNotSet = true)
    {
        if (empty($this->config)) {
            $config = $this->getServiceLocator()->get('Config');
            if (!isset($config['authentication']['doctrine_orm']) || !is_array($config['authentication']['doctrine_orm'])) {
                throw new \RuntimeException('Auth config is not configured properly');
            }
            $this->config = $config['authentication']['doctrine_orm'];
        }

        if (isset($this->config[$name])) {
            return $this->config[$name];
        }

        if ($errorIfNotSet) {
            throw new \RuntimeException(sprintf('Authentication `%s` config is not set.', $name));
        }

        return null;
    }

    /**
     * @return Result
     * @throws \Exception
     */
    public function authenticate()
    {
        $builder = $this->getEntityManager()
            ->createQueryBuilder()
            ->select(['u'])
            ->from($this->getConfig('entity_class'), 'u')
            ->where(sprintf('u.%s = :%s', $this->getConfig('identity_field'), $this->getConfig('identity_field')));

        $builder->setParameter($this->getConfig('identity_field'), $this->getIdentity());
        $query = $builder->getQuery();

        try {
            $user = $query->getSingleResult();
            if (!$user instanceof AuthenticableInterface) {
                throw new \DomainException('Authentication entity must implement AuthenticableInterface');
            }

            if (!$this->getPasswordService()->verify($this->getCredentials(), $user->getCredentials())) {
                return new Result(Result::FAILURE_CREDENTIAL_INVALID, 0);
            }

            if (!$user->isEnabled()) {
                return new Result(Result::FAILURE_UNCATEGORIZED, 0);
            }

            if ($user instanceof IdProviderInterface) {
                return new Result(Result::SUCCESS, $user->getId());
            } else {
                return new Result(Result::SUCCESS, $user->getIdentity());
            }
        } catch (NoResultException $e) {
            return new Result(Result::FAILURE_IDENTITY_NOT_FOUND, 0);
        }
    }
}