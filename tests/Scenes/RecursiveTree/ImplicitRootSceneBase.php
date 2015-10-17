<?php


namespace Oliva\Test\Scene\RecursiveTree;

use Oliva\Test\Scene\Scene;
use Oliva\Test\DataWrapper;


/**
 * Scene: recursive tree with an implicit root
 */
abstract class ImplicitRootSceneBase extends Scene
{
	public $implicitRoot;


	public function getData()
	{
		return [
			(new DataWrapper($this->implicitRoot, 'implicitRoot'))->setParent('foo'),
			(new DataWrapper(100, 'node'))->setParent($this->implicitRoot),
			(new DataWrapper(123, 'leaf'))->setParent(100),
		];
	}

}
