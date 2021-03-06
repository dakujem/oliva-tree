<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Scene\RecursiveTree;

use Oliva\Test\Scene\Scene,
	Oliva\Test\DataWrapper;


/**
 * Scene: recursive tree with multiple roots
 */
class MultiRootScene extends Scene
{


	public function getData()
	{
		return [
			(new DataWrapper(1, 'root1')),
			(new DataWrapper(2, 'root2')),
			(new DataWrapper(3, 'foobar'))->setParent(1),
		];
	}

}
