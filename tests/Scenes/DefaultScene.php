<?php


namespace Oliva\Test\Scene;

use Oliva\Test\DataWrapper;


/**
 * Default test scene to be used with all builders.
 *
 *
 * Scene: path tree with two defined roots, second root specified after inserting some nodes
 * Scene: recursive tree with a single root
 */
class DefaultScene extends Scene
{


	public function getData()
	{
		//		0   root
		//		|
		//		+--  1   hello
		//		|    |
		//		|    +--  11
		//		|    |
		//		|    +--  12
		//		|
		//		+--  2   world
		//		|    |
		//		|    +--  21
		//		|    |
		//		|    +--  22
		//		|    |     |
		//		|    |     +--  221
		//		|    |     |
		//		|    |     +--  222
		//		|    |     |      |
		//		|    |     |      +--  2221
		//		|    |     |
		//		|    |     +--  223
		//		|    |
		//		|    +--  23
		//		|
		//		+--  3

		return[
			(new DataWrapper(0, 'root'))->setPosition(NULL),
			(new DataWrapper(1, 'hello'))->setParent(0)->setPosition('001'),
			(new DataWrapper(2, 'world'))->setParent(0)->setPosition('002'),
			(new DataWrapper(11, 'hello child'))->setParent(1)->setPosition('001001'),
			(new DataWrapper(12, 'hello second child'))->setParent(1)->setPosition('001002'),
			(new DataWrapper(21, 'world first child'))->setParent(2)->setPosition('002001'),
			(new DataWrapper(22, 'world second child'))->setParent(2)->setPosition('002002'),
			(new DataWrapper(23, 'world third child'))->setParent(2)->setPosition('002003'),
			(new DataWrapper(221, 'world second-first'))->setParent(22)->setPosition('002002001'),
			(new DataWrapper(222, 'world second-second'))->setParent(22)->setPosition('002002002'),
			(new DataWrapper(223, 'world second-third'))->setParent(22)->setPosition('002002003'),
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setParent(222)->setPosition('002002002001'),
			(new DataWrapper(3, 'a lonely foo'))->setParent(0)->setPosition('003'),
		];
	}

}
