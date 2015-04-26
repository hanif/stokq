<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;
use Zend\Filter\Word\UnderscoreToCamelCase;
use Zend\Stdlib\ArraySerializableInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="accounts")
 */
class Account implements IdProviderInterface, ArraySerializableInterface
{
    const ALIAS = 'a';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", nullable=false, onDelete="RESTRICT")
     */
    private $owner;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="billing_user_id", nullable=false, onDelete="RESTRICT")
     */
    private $billing_user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $address = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $phone = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $fax = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $website = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $email = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $facebook = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $twitter = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $default_timezone = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $default_currency = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $default_locale = '';

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_users;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_outlets;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_warehouses;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_menus;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $max_stock_items;

    /**
     * @var \DateTime
     * @ORM\Column(type="date", nullable=true)
     */
    private $next_due_date;

    /**
     * @var \DateTime
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    private $created_at;

    /**
     *
     */
    function __construct()
    {
    }

    /**
     * @return string
     */
    public function getDefaultLocale()
    {
        return $this->default_locale;
    }

    /**
     * @param string $default_locale
     */
    public function setDefaultLocale($default_locale)
    {
        $this->default_locale = $default_locale;
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
     * @return User
     */
    public function getBillingUser()
    {
        return $this->billing_user;
    }

    /**
     * @param User $billing_user
     */
    public function setBillingUser($billing_user)
    {
        $this->billing_user = $billing_user;
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
    public function getDefaultCurrency()
    {
        return $this->default_currency;
    }

    /**
     * @param string $default_currency
     */
    public function setDefaultCurrency($default_currency)
    {
        $this->default_currency = $default_currency;
    }

    /**
     * @return string
     */
    public function getDefaultTimezone()
    {
        return $this->default_timezone;
    }

    /**
     * @param string $default_timezone
     */
    public function setDefaultTimezone($default_timezone)
    {
        $this->default_timezone = $default_timezone;
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
     * @return string
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param string $facebook
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;
    }

    /**
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * @param string $fax
     */
    public function setFax($fax)
    {
        $this->fax = $fax;
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
     * @return int
     */
    public function getMaxMenus()
    {
        return $this->max_menus;
    }

    /**
     * @param int $max_menus
     */
    public function setMaxMenus($max_menus)
    {
        $this->max_menus = $max_menus;
    }

    /**
     * @return int
     */
    public function getMaxOutlets()
    {
        return $this->max_outlets;
    }

    /**
     * @param int $max_outlets
     */
    public function setMaxOutlets($max_outlets)
    {
        $this->max_outlets = $max_outlets;
    }

    /**
     * @return int
     */
    public function getMaxStockItems()
    {
        return $this->max_stock_items;
    }

    /**
     * @param int $max_stock_items
     */
    public function setMaxStockItems($max_stock_items)
    {
        $this->max_stock_items = $max_stock_items;
    }

    /**
     * @return int
     */
    public function getMaxUsers()
    {
        return $this->max_users;
    }

    /**
     * @param int $max_users
     */
    public function setMaxUsers($max_users)
    {
        $this->max_users = $max_users;
    }

    /**
     * @return int
     */
    public function getMaxWarehouses()
    {
        return $this->max_warehouses;
    }

    /**
     * @param int $max_warehouses
     */
    public function setMaxWarehouses($max_warehouses)
    {
        $this->max_warehouses = $max_warehouses;
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
     * @return \DateTime
     */
    public function getNextDueDate()
    {
        return $this->next_due_date;
    }

    /**
     * @param \DateTime $next_due_date
     */
    public function setNextDueDate($next_due_date)
    {
        $this->next_due_date = $next_due_date;
    }

    /**
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $twitter
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param string $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
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
            'owner'             => $this->getOwner()->getId(),
            'billing_user'      => $this->getBillingUser()->getId(),
            'name'              => $this->getName(),
            'address'           => $this->getAddress(),
            'phone'             => $this->getPhone(),
            'fax'               => $this->getFax(),
            'website'           => $this->getWebsite(),
            'facebook'          => $this->getFacebook(),
            'email'             => $this->getEmail(),
            'twitter'           => $this->getTwitter(),
            'default_timezone'  => $this->getDefaultTimezone(),
            'default_currency'  => $this->getDefaultCurrency(),
            'default_locale'    => $this->getDefaultLocale(),
            'max_users'         => $this->getMaxUsers(),
            'max_outlets'       => $this->getMaxOutlets(),
            'max_warehouses'    => $this->getMaxWarehouses(),
            'max_menus'         => $this->getMaxMenus(),
            'max_stock_items'   => $this->getMaxStockItems(),
            'next_due_date'     => $this->getNextDueDate(),
            'created_at'        => $this->getCreatedAt(),
        ];
    }
}