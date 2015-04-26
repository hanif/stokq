<?php

namespace DoctrineORMModule\Proxy\__CG__\Stokq\Entity;

/**
 * DO NOT EDIT THIS FILE - IT WAS CREATED BY DOCTRINE'S PROXY GENERATOR
 */
class User extends \Stokq\Entity\User implements \Doctrine\ORM\Proxy\Proxy
{
    /**
     * @var \Closure the callback responsible for loading properties in the proxy object. This callback is called with
     *      three parameters, being respectively the proxy object to be initialized, the method that triggered the
     *      initialization process and an array of ordered parameters that were passed to that method.
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setInitializer
     */
    public $__initializer__;

    /**
     * @var \Closure the callback responsible of loading properties that need to be copied in the cloned object
     *
     * @see \Doctrine\Common\Persistence\Proxy::__setCloner
     */
    public $__cloner__;

    /**
     * @var boolean flag indicating if this object was already initialized
     *
     * @see \Doctrine\Common\Persistence\Proxy::__isInitialized
     */
    public $__isInitialized__ = false;

    /**
     * @var array properties to be lazy loaded, with keys being the property
     *            names and values being their default values
     *
     * @see \Doctrine\Common\Persistence\Proxy::__getLazyProperties
     */
    public static $lazyPropertiesDefaults = array();



    /**
     * @param \Closure $initializer
     * @param \Closure $cloner
     */
    public function __construct($initializer = null, $cloner = null)
    {

        $this->__initializer__ = $initializer;
        $this->__cloner__      = $cloner;
    }







    /**
     * 
     * @return array
     */
    public function __sleep()
    {
        if ($this->__isInitialized__) {
            return array('__isInitialized__', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'id', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'name', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'email', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'password', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'password_changed', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'contact_no', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'address', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'bio', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'status', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'timezone', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'created_at', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'logs', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'notifications', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'managed_outlets', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'tokens', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'managed_warehouses');
        }

        return array('__isInitialized__', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'id', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'name', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'email', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'password', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'password_changed', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'contact_no', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'address', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'bio', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'status', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'timezone', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'created_at', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'logs', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'notifications', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'managed_outlets', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'tokens', '' . "\0" . 'Stokq\\Entity\\User' . "\0" . 'managed_warehouses');
    }

    /**
     * 
     */
    public function __wakeup()
    {
        if ( ! $this->__isInitialized__) {
            $this->__initializer__ = function (User $proxy) {
                $proxy->__setInitializer(null);
                $proxy->__setCloner(null);

                $existingProperties = get_object_vars($proxy);

                foreach ($proxy->__getLazyProperties() as $property => $defaultValue) {
                    if ( ! array_key_exists($property, $existingProperties)) {
                        $proxy->$property = $defaultValue;
                    }
                }
            };

        }
    }

    /**
     * 
     */
    public function __clone()
    {
        $this->__cloner__ && $this->__cloner__->__invoke($this, '__clone', array());
    }

    /**
     * Forces initialization of the proxy
     */
    public function __load()
    {
        $this->__initializer__ && $this->__initializer__->__invoke($this, '__load', array());
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __isInitialized()
    {
        return $this->__isInitialized__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitialized($initialized)
    {
        $this->__isInitialized__ = $initialized;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setInitializer(\Closure $initializer = null)
    {
        $this->__initializer__ = $initializer;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __getInitializer()
    {
        return $this->__initializer__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     */
    public function __setCloner(\Closure $cloner = null)
    {
        $this->__cloner__ = $cloner;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific cloning logic
     */
    public function __getCloner()
    {
        return $this->__cloner__;
    }

    /**
     * {@inheritDoc}
     * @internal generated method: use only when explicitly handling proxy specific loading logic
     * @static
     */
    public function __getLazyProperties()
    {
        return self::$lazyPropertiesDefaults;
    }

    
    /**
     * {@inheritDoc}
     */
    public function getAddress()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getAddress', array());

        return parent::getAddress();
    }

    /**
     * {@inheritDoc}
     */
    public function setAddress($address)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setAddress', array($address));

        return parent::setAddress($address);
    }

    /**
     * {@inheritDoc}
     */
    public function getBio()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getBio', array());

        return parent::getBio();
    }

    /**
     * {@inheritDoc}
     */
    public function setBio($bio)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setBio', array($bio));

        return parent::setBio($bio);
    }

    /**
     * {@inheritDoc}
     */
    public function getContactNo()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getContactNo', array());

        return parent::getContactNo();
    }

    /**
     * {@inheritDoc}
     */
    public function setContactNo($contact_no)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setContactNo', array($contact_no));

        return parent::setContactNo($contact_no);
    }

    /**
     * {@inheritDoc}
     */
    public function getCreatedAt()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCreatedAt', array());

        return parent::getCreatedAt();
    }

    /**
     * {@inheritDoc}
     */
    public function setCreatedAt($created_at)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setCreatedAt', array($created_at));

        return parent::setCreatedAt($created_at);
    }

    /**
     * {@inheritDoc}
     */
    public function getEmail()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getEmail', array());

        return parent::getEmail();
    }

    /**
     * {@inheritDoc}
     */
    public function setEmail($email)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setEmail', array($email));

        return parent::setEmail($email);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        if ($this->__isInitialized__ === false) {
            return (int)  parent::getId();
        }


        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getId', array());

        return parent::getId();
    }

    /**
     * {@inheritDoc}
     */
    public function setId($id)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setId', array($id));

        return parent::setId($id);
    }

    /**
     * {@inheritDoc}
     */
    public function getLogs()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getLogs', array());

        return parent::getLogs();
    }

    /**
     * {@inheritDoc}
     */
    public function setLogs($logs)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setLogs', array($logs));

        return parent::setLogs($logs);
    }

    /**
     * {@inheritDoc}
     */
    public function getManagedOutlets()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getManagedOutlets', array());

        return parent::getManagedOutlets();
    }

    /**
     * {@inheritDoc}
     */
    public function setManagedOutlets($managed_outlets)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setManagedOutlets', array($managed_outlets));

        return parent::setManagedOutlets($managed_outlets);
    }

    /**
     * {@inheritDoc}
     */
    public function getManagedWarehouses()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getManagedWarehouses', array());

        return parent::getManagedWarehouses();
    }

    /**
     * {@inheritDoc}
     */
    public function setManagedWarehouses($managed_warehouses)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setManagedWarehouses', array($managed_warehouses));

        return parent::setManagedWarehouses($managed_warehouses);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getName', array());

        return parent::getName();
    }

    /**
     * {@inheritDoc}
     */
    public function setName($name)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setName', array($name));

        return parent::setName($name);
    }

    /**
     * {@inheritDoc}
     */
    public function getNotifications()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getNotifications', array());

        return parent::getNotifications();
    }

    /**
     * {@inheritDoc}
     */
    public function setNotifications($notifications)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setNotifications', array($notifications));

        return parent::setNotifications($notifications);
    }

    /**
     * {@inheritDoc}
     */
    public function getPassword()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getPassword', array());

        return parent::getPassword();
    }

    /**
     * {@inheritDoc}
     */
    public function setPassword($password)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPassword', array($password));

        return parent::setPassword($password);
    }

    /**
     * {@inheritDoc}
     */
    public function isPasswordChanged()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isPasswordChanged', array());

        return parent::isPasswordChanged();
    }

    /**
     * {@inheritDoc}
     */
    public function setPasswordChanged($password_changed)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setPasswordChanged', array($password_changed));

        return parent::setPasswordChanged($password_changed);
    }

    /**
     * {@inheritDoc}
     */
    public function getStatus()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getStatus', array());

        return parent::getStatus();
    }

    /**
     * {@inheritDoc}
     */
    public function setStatus($status)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setStatus', array($status));

        return parent::setStatus($status);
    }

    /**
     * {@inheritDoc}
     */
    public function getTimezone()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTimezone', array());

        return parent::getTimezone();
    }

    /**
     * {@inheritDoc}
     */
    public function setTimezone($timezone)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTimezone', array($timezone));

        return parent::setTimezone($timezone);
    }

    /**
     * {@inheritDoc}
     */
    public function getTokens()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getTokens', array());

        return parent::getTokens();
    }

    /**
     * {@inheritDoc}
     */
    public function setTokens($tokens)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'setTokens', array($tokens));

        return parent::setTokens($tokens);
    }

    /**
     * {@inheritDoc}
     */
    public function getIdentity()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getIdentity', array());

        return parent::getIdentity();
    }

    /**
     * {@inheritDoc}
     */
    public function getCredentials()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getCredentials', array());

        return parent::getCredentials();
    }

    /**
     * {@inheritDoc}
     */
    public function isEnabled()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'isEnabled', array());

        return parent::isEnabled();
    }

    /**
     * {@inheritDoc}
     */
    public function exchangeArray(array $array)
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'exchangeArray', array($array));

        return parent::exchangeArray($array);
    }

    /**
     * {@inheritDoc}
     */
    public function getArrayCopy()
    {

        $this->__initializer__ && $this->__initializer__->__invoke($this, 'getArrayCopy', array());

        return parent::getArrayCopy();
    }

}
