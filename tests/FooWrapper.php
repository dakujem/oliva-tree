<?php


class FooWrapper
{
	private $foo = ['get' => [], 'set' => [], 'call' => []];


	public function getFoo()
	{
		return $this->foo;
	}


	public function __get($name)
	{
		$this->foo['get'][] = $name;
		return 'foo attr ' . $name;
	}


	public function __set($name, $value)
	{
		$this->foo['set'][] = [$name, $value];
		return $this;
	}


	public function __call($name, $arguments)
	{
		$this->foo['call'][] = [$name, $arguments];
		return $this;
	}

}
