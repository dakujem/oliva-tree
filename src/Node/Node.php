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
 * Note:	when clonning a Node instance, the data is NOT clonned! Only a shallow copy is created.
 * 			Use cloneContents() after clonning to clone objects.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class Node extends NodeBase implements ArrayAccess, IDataNode
{
	const TYPE_OBJECT = 'object';
	const TYPE_ARRAY = 'array';
	const TYPE_SCALAR = 'scalar'; // NULL or scalar

	protected $contents = NULL;
	protected $type = self::TYPE_SCALAR;


	/**
	 * @param mixed $data any data - array, object or scalar
	 */
	public function __construct($data = NULL)
	{
		$this->setContents($data);
	}


	/**
	 * True when the object data is NULL.
	 *
	 *
	 * @return bool
	 */
	public function isNull()
	{
		return $this->contents === NULL;
	}


	/**
	 * Returns the node's contents.
	 *
	 *
	 * @return mixed
	 */
	public function getContents()
	{
		return $this->type !== self::TYPE_ARRAY ? $this->contents : (array) $this->contents;
	}


	/**
	 * Set (overwrite) the node's contents.
	 *
	 *
	 * @param mixed $data
	 * @return Node
	 */
	public function setContents($data)
	{
		$this->type = is_object($data) ? self::TYPE_OBJECT : (is_array($data) ? self::TYPE_ARRAY : self::TYPE_SCALAR);
		$this->contents = $data;
		return $this;
	}


	/**
	 * @deprecated replaced by setContents()
	 *
	 *
	 * @param mixed $data
	 * @return Node
	 */
	public function setObject($data)
	{
		return $this->setContents($data);
	}


	/**
	 * @deprecated replaced by getContents()
	 *
	 *
	 * @return mixed
	 */
	public function getObject()
	{
		return $this->getContents();
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
			return call_user_func_array([$this->contents, $name], $args);
		}
		throw new BadMethodCallException($this->formatErrorMessage('Undefined call to %s::%s(). The method cannot be called on the node\'s contents of type %s either.', $name));
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
				return $this->contents->$name;
			case self::TYPE_ARRAY:
				return $this->contents[$name];
		}
		throw new RuntimeException($this->formatErrorMessage('Cannot read an undeclared property %s::$%s. Furthermore, the node contains scalar data of type %s.', $name));
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
				return $this->contents->$name = $value;
			case self::TYPE_ARRAY:
				if ($name !== NULL) {
					return $this->contents[$name] = $value;
				} else {
					return $this->contents[] = $value;
				}
		}
		throw new RuntimeException($this->formatErrorMessage('Cannot write to an undeclared property %s::$%s. Furthermore, the node contains scalar data of type %s.', $name));
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
				return isset($this->contents->$name);
			case self::TYPE_ARRAY:
				return isset($this->contents[$name]);
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
				unset($this->contents->$name);
				return;
			case self::TYPE_ARRAY:
				unset($this->contents[$name]);
				return;
		}
		throw new RuntimeException($this->formatErrorMessage('Cannot unset an undeclared property %s::$%s.' . ($this->type === self::TYPE_SCALAR ? ' Furthermore, the node contains scalar data of type %s.' : ''), $name));
	}


	private function formatErrorMessage($template, $name)
	{
		return sprintf($template, get_class($this), $name, gettype($this->contents));
	}


	//-----------------------------------------------------------------
	//----------------------- \ArrayAccess ----------------------------


    public function offsetExists($offset): bool
    {
		return $this->__isset($offset);
	}


    #[\ReturnTypeWillChange]
	public function offsetGet($offset)
	{
		return $this->__get($offset);
	}


	public function offsetSet($offset, $value): void
	{
		$this->__set($offset, $value);
	}


	public function offsetUnset($offset): void
	{
		$this->__unset($offset);
	}

	//-----------------------------------------------------------------
	//------------------------- clonning ------------------------------


	/**
	 * Make a copy of the contents.
	 *
	 * This is useful when clonning a branch or a node.
	 *
	 *
	 * @return self fluent
	 */
	public function cloneContents()
	{
		if (is_object($this->getContents())) {
			$this->setContents(clone $this->getContents());
		}
		return $this;
	}
}
