<?php

namespace Stokq\Stdlib;

/**
 * Class ViewMessage
 * @package Stokq\Stdlib
 */
class ViewMessage implements \Countable, \Serializable
{
    /**
     * @var array
     */
    protected $messages = [];

    /**
     * @param $type
     * @param $message
     */
    public function add($type, $message)
    {
        if (!isset($this->messages[$type])) {
            $this->messages[$type] = [];
        }

        if (!is_array($this->messages[$type])) {
            $this->messages[$type] = [];
        }

        $this->messages[$type][] = $message;
    }

    /**
     * @param string|null $type
     */
    public function clear($type = null)
    {
        if (null === $type) {
            $this->messages = [];
        } else {
            $this->messages[$type] = [];
        }
    }

    /**
     * @param array $messages
     */
    public function setMessages($messages)
    {
        $this->messages = $messages;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return serialize($this->messages);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $this->messages = (array)@unserialize($serialized);
    }

    /**
     * @return int
     */
    public function count()
    {
        if (!count($this->messages)) {
            return 0;
        }

        $count = 0;
        foreach ($this->messages as $messages) {
            if (is_array($messages)) {
                $count += count($messages);
            }
        }

        return $count;
    }
}