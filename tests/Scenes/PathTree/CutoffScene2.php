<?php


namespace Oliva\Test\Scene\PathTree;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: path tree with cutoff "007" and a single root
 */
class CutoffScene2 extends Scene
{


	public function getData()
	{
		return [
			(new DataWrapper(221, 'world'))->setPosition('007002002001'),
			(new DataWrapper(21, 'my'))->setPosition('007002001'),
			(new DataWrapper(1, 'node'))->setPosition('007001'),
			(new DataWrapper(2, 'hello'))->setPosition('007002'),
			(new DataWrapper(0, 'root'))->setPosition('007'),
		];
	}

}
