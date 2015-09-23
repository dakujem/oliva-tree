<?php


class DataWrapper
{
	public $id, $foo = 'bar', $title, $position, $parent;
	private $attributes = [];
	private $_overloadedScalar = NULL;
	private $_overloadedArray = NULL;
	private $_overloadedObject = NULL;


	public function __construct($id = NULL, $title = NULL)
	{
		$this->id = $id;
		$this->title = $title;
	}


	public function setParent($id)
	{
		$this->parent = $id;
		return $this;
	}


	public function setPosition($position)
	{
		$this->position = $position;
		return $this;
	}


	public function setAttribute($name, $val)
	{
		$this->attributes[$name] = $val;
		return $this;
	}


	public function getAttribute($name, $default = NULL)
	{
		return isset($this->attributes[$name]) ? $this->attributes[$name] : $default;
	}


	/**
	 * NOTE: returning overloaded members by reference is not supported!
	 */
	public function __get($name)
	{
		$nl = strtolower($name);
		$supported = ['scalar', 'array', 'object',];
		if (in_array($nl, $supported)) {
			return $this->{'_overloaded' . ucfirst($nl)};
		}
		throw new RuntimeException('Member access exception on getting "' . $name . '" of "' . get_class($this) . '".');
	}


	public function __set($name, $value)
	{
		$nl = strtolower($name);
		$supported = ['scalar', 'array', 'object',];
		if (in_array($nl, $supported)) {
			if (call_user_func('is_' . $nl, $name)) {
				$this->{'_overloaded' . ucfirst($nl)} = $value;
				return $value;
			}
			throw new RuntimeException('Value is not of type "' . $nl . '" on setting "' . $name . '" of "' . get_class($this) . '".');
		}
		throw new RuntimeException('Member access exception on setting "' . $name . '" of "' . get_class($this) . '".');
	}


	public function __call($name, $arguments)
	{
		throw new RuntimeException('Invalid function call "' . $name . '" of "' . get_class($this) . '".');
	}

}
