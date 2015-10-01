<?php


namespace Oliva\Test\Scene\RecursiveTree;

use Oliva\Test\Scene\Scene;


/**
 * Scene: recursive tree with an implicit root
 */
abstract class ImplicitRootSceneBase extends Scene
{
	protected $implicitRoot;


	public function getData()
	{
		return [
			(new DataWrapper(0, 'root')),
			(new DataWrapper(2, 'node'))->setParent($this->implitictRoot),
			(new DataWrapper(3, 'leaf'))->setParent(2),
		];
	}

}
