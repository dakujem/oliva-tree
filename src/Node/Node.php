<?php


namespace Oliva\Utils\Tree\Node;

use ArrayAccess;
use Nette\MemberAccessException,
	Nette\Utils\ObjectMixin;


/**
 * A universal tree node implementation.
 *
 * Usage:
 * $any_data = ['foo'=>'bar'];
 * $node = new Node($any_data);
 * $node['foo']; // 'bar'
 * $node->foo;   // 'bar'
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class Node extends NodeBase implements ArrayAccess
{
	private $object = NULL;
	private $isObject = TRUE;


	public function __construct($data_or_object = NULL)
	{
		$this->setObject($data_or_object);
	}


	/**
	 * True when the object data is NULL.
	 *
	 * 
	 * @return bool
	 */
	public function isNull()
	{
		return $this->object === NULL;
	}


	/**
	 * Set (overwrite) the node's object data.
	 *
	 *
	 * @param mixed $data
	 * @return Node
	 */
	public function setObject($data)
	{
		$this->object = $data;
		$this->isObject = is_object($this->object);
		return $this;
	}


	/**
	 * Returns the node's object data.
	 * 
	 *
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->object;
	}


	/**
	 * Call to undefined method.
	 *
	 * 
	 * @param  string $name method name
	 * @param  array  $args arguments
	 * @return mixed
	 * @throws MemberAccessException
	 */
	public function __call($name, $args)
	{
		if (is_object($this->object)) {
			// delegate call
			return ObjectMixin::call($this->object, $name, $args);
		}
		throw new MemberAccessException('Undefined method call: ' . get_class($this) . '::$' . $name . '. Method cannot be called on the node\'s object/array either.');
	}


	/**
	 * Returns property value. Do not call directly.
	 *
	 * 
	 * @param  string  $name property name
	 * @return mixed   $value property value
	 * @throws MemberAccessException if the property is not defined.
	 */
	public function &__get($name)
	{
		if ($this->object !== NULL && !is_scalar($this->object)) {
			if (is_object($this->object)) {
				return $this->object->$name;
			} elseif (is_array($this->object)) {
				return $this->object[$name];
			}
		}
		throw new MemberAccessException('Cannot read an undeclared property ' . get_class($this->object) . '::$' . $name . ', the node\'s object is scalar or NULL.');
	}


	/**
	 * Sets value of a property. Do not call directly.
	 *
	 *
	 * @param  string  $name property name
	 * @param  mixed   $value property value
	 * @return void
	 * @throws MemberAccessException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		if ($this->object !== NULL && !is_scalar($this->object)) {
			if (is_object($this->object)) {
				return $this->object->$name = $value;
			} elseif (is_array($this->object)) {
				return $this->object[$name] = $value;
			}
		}
		throw new MemberAccessException('Cannot write to an undeclared property ' . get_class($this->object) . '::$' . $name . ', the node\'s object is scalar or NULL.');
	}


	/**
	 * Is property defined?
	 *
	 * 
	 * @param  string  $name property name
	 * @return bool
	 */
	public function __isset($name)
	{
		if ($this->object !== NULL && !is_scalar($this->object)) {
			if (is_object($this->object)) {
				return isset($this->object->$name);
			} elseif (is_array($this->object)) {
				return isset($this->object[$name]);
			}
		}
		return FALSE;
	}


	/**
	 * Access to undeclared property.
	 *
	 *
	 * @param  string  $name property name
	 * @return void
	 * @throws MemberAccessException
	 */
	public function __unset($name)
	{
		if ($this->object !== NULL && !is_scalar($this->object)) {
			if (is_object($this->object)) {
				unset($this->object->$name);
			} elseif (is_array($this->object)) {
				unset($this->object[$name]);
			}
		}
		throw new MemberAccessException('Cannot access an undeclared property ' . get_class($this->object) . '::$' . $name . ', the node\'s object is scalar or NULL.');
	}


	//-----------------------------------------------------------------
	//----------------------- \ArrayAccess ----------------------------


	public function offsetExists($offset)
	{
		return $this->__isset($offset);
	}


	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}


	public function offsetSet($offset, $value)
	{
		return $this->__set($offset, $value);
	}


	public function offsetUnset($offset)
	{
		return $this->__unset($offset);
	}

}
