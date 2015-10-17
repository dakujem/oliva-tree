<?php


namespace Oliva\Test\Scene\PathTree;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: path tree with two defined roots, second root specified after inserting some nodes
 */
class CollidingRootsScene extends Scene
{


	public function getData()
	{
		return[
			(new DataWrapper(0, 'root'))->setPosition(NULL),
			(new DataWrapper(1, 'hello'))->setPosition('001'),
			(new DataWrapper(100, 'root2'))->setPosition(''), // root colliding with the first root
			(new DataWrapper(11, 'hello child'))->setPosition('001001'),
			(new DataWrapper(3, 'lonely'))->setPosition('003'),
		];
	}

}
