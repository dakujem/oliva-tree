<?php


class DataWrapper
{
	public $id, $foo = 'bar', $title, $position, $parent;
	private $attributes = [];


	public function __construct($id = NULL, $title = NULL)
	{

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

}


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


	public function __call(...$m)
	{
		$this->foo['call'][] = $m;
		return $this;
	}

}


function recursiveTreeData()
{
	// recursive tree with a single root
	$parentTreeData = [
		(new DataWrapper(0, 'root')),
		(new DataWrapper(1, 'hello'))->setParent(0),
		(new DataWrapper(2, 'world'))->setParent(0),
		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
		(new DataWrapper(3, 'a lonely foo'))->setParent(0),
	];
	// recursive tree with multiple roots
	$perentTreeMultiRootsData = [
		(new DataWrapper(1, 'root1')),
		(new DataWrapper(2, 'root2')),
		(new DataWrapper(3, 'foobar'))->setParent(1),
	];
	// recursive trees with implicit roots
	$perentTreeImplicitRootData1 = [
		(new DataWrapper(0, 'root')),
		(new DataWrapper(2, 'node'))->setParent(0),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData2 = [
		(new DataWrapper('', 'root')),
		(new DataWrapper(2, 'node'))->setParent(''),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData3 = [
		(new DataWrapper(NULL, 'root')),
		(new DataWrapper(2, 'node'))->setParent(NULL),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	$perentTreeImplicitRootData4 = [
		(new DataWrapper('0', 'root')),
		(new DataWrapper(2, 'node'))->setParent('0'),
		(new DataWrapper(3, 'leaf'))->setParent(2),
	];
	return [$parentTreeData, $perentTreeMultiRootsData, $perentTreeImplicitRootData1, $perentTreeImplicitRootData2, $perentTreeImplicitRootData3, $perentTreeImplicitRootData4,];
}


function prepareData()
{
	// path tree with undefined root
	$pathTreeData = [
		(new DataWrapper(1, 'hello'))->setPosition('001'),
		(new DataWrapper(2, 'world'))->setPosition('002'),
		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
		(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
	];


	return [$pathTreeData,];
}


function materializedTreeData()
{

	// materialized tree with undefined root
	$materializedTreeData = [
//		(new DataWrapper(1, 'hello'))->setPosition('001'),
//		(new DataWrapper(2, 'world'))->setPosition('002'),
//		(new DataWrapper(11, 'hello child'))->setPosition('001001'),
//		(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
//		(new DataWrapper(21, 'world first child'))->setPosition('002001'),
//		(new DataWrapper(22, 'world second child'))->setPosition('002002'),
//		(new DataWrapper(23, 'world third child'))->setPosition('002003'),
//		(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
//		(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
//		(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
//		(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
//		(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
	];
}


function foo()
{
	
}
