<?php


namespace Oliva\Test\Scene\RecursiveTree;

use Oliva\Test\Scene\Scene;


/**
 * Scene: recursive tree with multiple roots
 */
class MultiRootScene extends Scene
{


	public function getRoot()
	{
		return [
			(new DataWrapper(1, 'root1')),
			(new DataWrapper(2, 'root2')),
			(new DataWrapper(3, 'foobar'))->setParent(1),
		];
	}

}
