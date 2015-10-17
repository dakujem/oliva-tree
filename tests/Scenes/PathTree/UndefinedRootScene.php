<?php


namespace Oliva\Test\Scene\PathTree;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: path tree with undefined root
 */
class UndefinedRootScene extends Scene
{


	public function getData()
	{
		return [
			(new DataWrapper(1, 'hello'))->setPosition('001'),
			(new DataWrapper(2, 'world'))->setPosition('002'),
		];
	}

}
