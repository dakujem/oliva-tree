<?php


namespace Oliva\Test\Scene;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: same struture as in DefaultScene, but roots that refer to parents not yet defined are present.
 *
 * Note:	the builders usually don't care about the children order. The nodes are created in the same order the data is served.
 * 			The exception is the PathBuilder, that can order the nodes by path.
 */
class BridgingScene extends Scene
{


	public function getData()
	{
		return[
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setParent(222)->setPosition('002002002001'),
			(new DataWrapper(0, 'root'))->setPosition(NULL),
			(new DataWrapper(11, 'hello child'))->setParent(1)->setPosition('001001'),
			(new DataWrapper(1, 'hello'))->setParent(0)->setPosition('001'),
			(new DataWrapper(2, 'world'))->setParent(0)->setPosition('002'),
			(new DataWrapper(12, 'hello second child'))->setParent(1)->setPosition('001002'),
			(new DataWrapper(21, 'world first child'))->setParent(2)->setPosition('002001'),
			(new DataWrapper(23, 'world third child'))->setParent(2)->setPosition('002003'),
			(new DataWrapper(22, 'world second child'))->setParent(2)->setPosition('002002'),
			(new DataWrapper(221, 'world second-first'))->setParent(22)->setPosition('002002001'),
			(new DataWrapper(222, 'world second-second'))->setParent(22)->setPosition('002002002'),
			(new DataWrapper(223, 'world second-third'))->setParent(22)->setPosition('002002003'),
			(new DataWrapper(3, 'a lonely foo'))->setParent(0)->setPosition('003'),
		];
	}

}
