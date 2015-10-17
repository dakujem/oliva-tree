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
class NoRootScene extends Scene
{


	public function getData()
	{
		return [
			(new DataWrapper(1, 'node1'))->setParent(3),
			(new DataWrapper(2, 'node2'))->setParent(1),
			(new DataWrapper(3, 'node3'))->setParent(2),
		];
	}

}
