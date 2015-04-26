<?php

namespace Stokq\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Stokq\Stdlib\IdProviderInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="logs")
 */
class Log implements IdProviderInterface
{
    const ALIAS = 'l';

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    private $id;

    /**
     * @var User
     * @ORM\ManyToOne(targetEntity="User", inversedBy="logs")
     * @ORM\JoinColumn(name="user_id", nullable=true, onDelete="SET NULL")
     */
    private $user;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $user_name = '';

    /**
     * @var integer
     * @ORM\Column(type="integer")
     */
    private $resource_id = 0;

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $resource_type = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $action = '';

    /**
     * @var string
     * @ORM\Column(type="string")
     */
    private $params = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $message = '';

    /**
     * @var string
     * @ORM\Column(type="text")
     */
    private $data = '';

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
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getUserName()
    {
        if ($this->getUser() instanceof User) {
            return $this->getUser()->getName();
        }
        return $this->user_name;
    }

    /**
     * @param string $user_name
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
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
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getFormattedMessage()
    {
        return str_replace(
            ['{user_name}', '{resource_id}'],
            [$this->getUserName(), $this->getResourceId()],
            $this->message
        );
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return int
     */
    public function getResourceId()
    {
        return $this->resource_id;
    }

    /**
     * @param int $resource_id
     */
    public function setResourceId($resource_id)
    {
        $this->resource_id = $resource_id;
    }

    /**
     * @return string
     */
    public function getResourceType()
    {
        return $this->resource_type;
    }

    /**
     * @param string $resource_type
     */
    public function setResourceType($resource_type)
    {
        $this->resource_type = $resource_type;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

}