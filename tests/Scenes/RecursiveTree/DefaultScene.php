<?php


namespace Oliva\Test\Scene\RecursiveTree;

use Oliva\Test\Scene\Scene;


/**
 * Scene: recursive tree with a single root
 */
class DefaultScene extends Scene
{


	public function getRoot()
	{
		return [
			(new DataWrapper(0, 'root')),
			(new DataWrapper(1, 'hello'))->setParent(0),
			(new DataWrapper(2, 'world'))->setParent(0),
			(new DataWrapper(11, 'hello child'))->setParent(1),
			(new DataWrapper(12, 'hello second child'))->setParent(1),
			(new DataWrapper(21, 'world first child'))->setParent(2),
			(new DataWrapper(22, 'world second child'))->setParent(2),
			(new DataWrapper(23, 'world third child'))->setParent(2),
			(new DataWrapper(221, 'world second-first'))->setParent(22),
			(new DataWrapper(222, 'world second-second'))->setParent(22),
			(new DataWrapper(223, 'world second-third'))->setParent(22),
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setParent(222),
			(new DataWrapper(3, 'a lonely foo'))->setParent(0),
		];
	}

}
