<?php

namespace Stokq\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Authentication\AuthenticableInterface;
use Stokq\Stdlib\IdProviderInterface;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User implements IdProviderInterface, AuthenticableInterface, ArraySerializableInterface
{
    const ALIAS = 'u';

    const STATUS_NEW = 'new';
    const STATUS_ACTIVE = 'active';
    const STATUS_INACTIVE = 'inactive';

    const DEFAULT_TIMEZONE = 'Asia/Jakarta';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name = '';

    /**
     * @var string
     * @ORM\Column(type="string", unique=true)
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $password = '';

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $password_changed = false;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $contact_no = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $address = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $bio = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $status = self::STATUS_NEW;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $timezone = self::DEFAULT_TIMEZONE;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     * @var ArrayCollection|Log[]
     * @ORM\OneToMany(targetEntity="Log", mappedBy="user")
     */
    private $logs;

    /**
     * @var ArrayCollection|Notification[]
     * @ORM\OneToMany(targetEntity="Notification", mappedBy="user")
     */
    private $notifications;

    /**
     * @var ArrayCollection|OutletManager[]
     * @ORM\OneToMany(targetEntity="OutletManager", mappedBy="user")
     */
    private $managed_outlets;

    /**
     * @var ArrayCollection|Token[]
     * @ORM\OneToMany(targetEntity="Token", mappedBy="user")
     */
    private $tokens;

    /**
     * @var ArrayCollection|WarehouseManager[]
     * @ORM\OneToMany(targetEntity="WarehouseManager", mappedBy="user")
     */
    private $managed_warehouses;

    /**
     *
     */
    function __construct()
    {
        $this->logs = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->managed_outlets = new ArrayCollection();
        $this->tokens = new ArrayCollection();
        $this->managed_warehouses = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getBio()
    {
        return $this->bio;
    }

    /**
     * @param string $bio
     */
    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    /**
     * @return string
     */
    public function getContactNo()
    {
        return $this->contact_no;
    }

    /**
     * @param string $contact_no
     */
    public function setContactNo($contact_no)
    {
        $this->contact_no = $contact_no;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param \DateTime $created_at
     */
    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return ArrayCollection|Log[]
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * @param ArrayCollection|Log[] $logs
     */
    public function setLogs($logs)
    {
        $this->logs = $logs;
    }

    /**
     * @return ArrayCollection|OutletManager[]
     */
    public function getManagedOutlets()
    {
        return $this->managed_outlets;
    }

    /**
     * @param ArrayCollection|OutletManager[] $managed_outlets
     */
    public function setManagedOutlets($managed_outlets)
    {
        $this->managed_outlets = $managed_outlets;
    }

    /**
     * @return ArrayCollection|WarehouseManager[]
     */
    public function getManagedWarehouses()
    {
        return $this->managed_warehouses;
    }

    /**
     * @param ArrayCollection|WarehouseManager[] $managed_warehouses
     */
    public function setManagedWarehouses($managed_warehouses)
    {
        $this->managed_warehouses = $managed_warehouses;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return ArrayCollection|Notification[]
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * @param ArrayCollection|Notification[] $notifications
     */
    public function setNotifications($notifications)
    {
        $this->notifications = $notifications;
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return boolean
     */
    public function isPasswordChanged()
    {
        return $this->password_changed;
    }

    /**
     * @param boolean $password_changed
     */
    public function setPasswordChanged($password_changed)
    {
        $this->password_changed = $password_changed;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getTimezone()
    {
        return $this->timezone;
    }

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @return ArrayCollection|Token[]
     */
    public function getTokens()
    {
        return $this->tokens;
    }

    /**
     * @param ArrayCollection|Token[] $tokens
     */
    public function setTokens($tokens)
    {
        $this->tokens = $tokens;
    }

    /**
     * @return string
     */
    public function getIdentity()
    {
        return $this->getEmail();
    }

    /**
     * @return string
     */
    public function getCredentials()
    {
        return $this->getPassword();
    }

    /**
     * @return bool
     */
    public function isEnabled()
    {
        return $this->getStatus() == self::STATUS_ACTIVE;
    }

    /**
     * Exchange internal values from provided array
     *
     * @param  array $array
     * @return void
     */
    public function exchangeArray(array $array)
    {
        $filter = new UnderscoreToCamelCase();
        foreach ($array as $key => $val) {
            $method = sprintf('set%s', ucfirst($filter->filter($key)));
            if (method_exists($this, $method)) {
                $this->{$method}($val);
            }
        }
    }

    /**
     * Return an array representation of the object
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return [
            'id'                => $this->getId(),
            'name'              => $this->getName(),
            'email'             => $this->getEmail(),
            'contact_no'        => $this->getContactNo(),
            'password_changed'  => $this->isPasswordChanged(),
            'address'           => $this->getAddress(),
            'bio'               => $this->getBio(),
            'status'            => $this->getStatus(),
            'timezone'          => $this->getTimezone(),
            'created_at'        => $this->getCreatedAt(),
        ];
    }
}