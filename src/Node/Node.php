<?php


namespace Oliva\Utils\Tree\Node;

use RuntimeException,
	BadMethodCallException,
	ArrayAccess;


/**
 * A universal tree node implementation.
 *
 * Usage:
 * $any_data = ['foo'=>'bar'];
 * $node = new Node($any_data);
 * $node['foo']; // 'bar'
 * $node->foo;   // 'bar'
 * $node->getObject(); // $any_data
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class Node extends NodeBase implements ArrayAccess
{
	const TYPE_OBJECT = 'object';
	const TYPE_ARRAY = 'array';
	const TYPE_SCALAR = 'scalar'; // NULL or scalar

	protected $object = NULL;
	protected $type = self::TYPE_SCALAR;


	/**
	 * @param mixed $data any data - array, object or scalar
	 */
	public function __construct($data = NULL)
	{
		$this->setObject($data);
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
		$this->type = is_object($data) ? self::TYPE_OBJECT : (is_array($data) ? self::TYPE_ARRAY : self::TYPE_SCALAR);
		$this->object = $data;
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
		return $this->type !== self::TYPE_ARRAY ? $this->object : (array) $this->object;
	}


	/**
	 * Call to undefined method.
	 *
	 *
	 * @param  string $name method name
	 * @param  array  $args arguments
	 * @return mixed
	 * @throws BadMethodCallException
	 */
	public function __call($name, $args)
	{
		if ($this->type === self::TYPE_OBJECT) {
			// delegate call
			return $this->object->$name(...$args);
		}
		throw new BadMethodCallException('Undefined method call: ' . get_class($this) . '::$' . $name . '. Method cannot be called on the node\'s data either.');
	}


	/**
	 * Returns property value. Do not call directly.
	 *
	 *
	 * @param  string  $name property name
	 * @return mixed   $value property value
	 * @throws RuntimeException if the property is not defined.
	 */
	public function __get($name)
	{
		switch ($this->type) {
			case self::TYPE_OBJECT:
				return $this->object->$name;
			case self::TYPE_ARRAY:
				return $this->object[$name];
		}
		throw new RuntimeException('Cannot read an undeclared property ' . get_class($this) . '::$' . $name . '. Furthermore, the node\'s data is scalar or NULL.');
	}


	/**
	 * Sets value of a property. Do not call directly.
	 *
	 *
	 * @param  string  $name property name
	 * @param  mixed   $value property value
	 * @return void
	 * @throws RuntimeException if the property is not defined or is read-only
	 */
	public function __set($name, $value)
	{
		switch ($this->type) {
			case self::TYPE_OBJECT:
				return $this->object->$name = $value;
			case self::TYPE_ARRAY:
				if ($name !== NULL) {
					return $this->object[$name] = $value;
				} else {
					return $this->object[] = $value;
				}
		}
		throw new RuntimeException('Cannot write to an undeclared property ' . get_class($this) . '::$' . $name . '. Furthermore, the node\'s data is scalar or NULL.');
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
		switch ($this->type) {
			case self::TYPE_OBJECT:
				return isset($this->object->$name);
			case self::TYPE_ARRAY:
				return isset($this->object[$name]);
		}
		return FALSE;
	}


	/**
	 * Access to undeclared property.
	 *
	 *
	 * @param  string  $name property name
	 * @return void
	 * @throws RuntimeException
	 */
	public function __unset($name)
	{
		switch ($this->type) {
			case self::TYPE_OBJECT:
				unset($this->object->$name);
			case self::TYPE_ARRAY:
				unset($this->object[$name]);
		}
		throw new RuntimeException('Cannot unset an undeclared property ' . get_class($this) . '::$' . $name . '. Furthermore, the node\'s data is scalar or NULL.');
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
