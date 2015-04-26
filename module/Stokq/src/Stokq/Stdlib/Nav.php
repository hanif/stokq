<?php

namespace Stokq\Stdlib;

use Stokq\View\Helper\Nav\Renderer\RendererInterface;
use Stokq\View\Helper\Nav\Renderer\Simple;
use Traversable;

/**
 * Class Nav
 * @package Stokq\Stdlib
 */
class Nav implements \Serializable, \ArrayAccess, \IteratorAggregate
{
    use ConfigurableTrait;

    /**
     * @var array
     */
    protected $spec = [];

    /**
     * @var array
     */
    protected $active = [];

    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * @param array $spec
     */
    function __construct(array $spec = [])
    {
        $this->spec = $spec;
    }

    /**
     * @return RendererInterface
     */
    public function getRenderer()
    {
        if (!$this->renderer) {
            $this->renderer = new Simple();
        }

        return $this->renderer;
    }

    /**
     * @param RendererInterface $renderer
     */
    public function setRenderer(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @return string
     */
    public function render()
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * @return string
     */
    function __toString()
    {
        return $this->render();
    }

    /**
     * @param $name
     * @param array $spec
     */
    public function add($name, array $spec)
    {
        $this->spec[$name] = $spec;
    }

    /**
     * @param string $key
     * @return array|null
     */
    public function get($key)
    {
        if (isset($this->spec[$key])) {
            return $this->spec[$key];
        }
        return null;
    }

    /**
     * @param $name
     */
    public function setActive(...$name)
    {
        $this->active = $name;
    }

    /**
     * @return array
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param $name
     * @return bool
     */
    public function isActive($name)
    {
        return in_array($name, $this->active);
    }

    /**
     * @param $name
     * @param null $action
     * @param null $else
     * @return null
     */
    public function ifActive($name, $action = null, $else = null)
    {
        if ($this->isActive($name)) {
            return is_callable($action) ? $action($this) : $action;
        } else {
            return is_callable($else) ? $else($this) : $else;
        }
    }

    /**
     * @param array $spec
     */
    public function fromArray($spec)
    {
        $this->spec = $spec;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->spec;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return json_encode($this->spec);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $this->spec = json_decode($serialized);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists($offset)
    {
        return isset($this->spec[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     */
    public function offsetGet($offset)
    {
        return isset($this->spec[$offset]) ? $this->spec[$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     */
    public function offsetSet($offset, $value)
    {
        $this->spec[$offset] = $value;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     */
    public function offsetUnset($offset)
    {
        unset($this->spec[$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->spec);
    }
}