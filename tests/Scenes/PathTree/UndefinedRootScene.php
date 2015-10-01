<?php


namespace Oliva\Test\Scene\PathTree;

use Oliva\Test\Scene\Scene;


/**
 * Scene: path tree with undefined root
 */
class UndefinedRootScene extends Scene
{


	public function getData()
	{
		return [
			(new DataWrapper(1, 'hello'))->setPosition('001'),
			(new DataWrapper(11, 'hello child'))->setPosition('001001'),
			(new DataWrapper(12, 'hello second child'))->setPosition('001002'),
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('002002002001'),
			(new DataWrapper(2, 'world'))->setPosition('002'),
			(new DataWrapper(21, 'world first child'))->setPosition('002001'),
			(new DataWrapper(22, 'world second child'))->setPosition('002002'),
			(new DataWrapper(23, 'world third child'))->setPosition('002003'),
			(new DataWrapper(221, 'world second-first'))->setPosition('002002001'),
			(new DataWrapper(222, 'world second-second'))->setPosition('002002002'),
			(new DataWrapper(223, 'world second-third'))->setPosition('002002003'),
			(new DataWrapper(3, 'a lonely foo'))->setPosition('003'),
		];
	}

}
