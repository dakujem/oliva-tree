<?php


namespace Oliva\Test\Scene;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: some referenced nodes are missing. The behaviour is undefined - depends on the builder.
 */
class MissingRefScene extends Scene
{


	public function getData()
	{
		return[
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setParent(222)->setPosition('002002002001'), // whole "2" branch missing
			(new DataWrapper(0, 'root'))->setPosition(NULL),
			(new DataWrapper(1, 'hello'))->setParent(0)->setPosition('001'),
			(new DataWrapper(11, 'world'))->setParent(1)->setPosition('001001'),
			(new DataWrapper(1111, 'foo'))->setParent(111)->setPosition('001001001001'), // one level skipped
			(new DataWrapper(3, 'lonely'))->setParent(0)->setPosition('003'),
		];
	}

}
