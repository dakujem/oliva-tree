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
class DelimitedScene extends Scene
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
			(new DataWrapper(1, 'hello'))->setPosition('1'),
			(new DataWrapper(2, 'world'))->setPosition('2'),
			(new DataWrapper(11, 'hello child'))->setPosition('1.1'),
			(new DataWrapper(12, 'hello second child'))->setPosition('1.2'),
			(new DataWrapper(21, 'world first child'))->setPosition('2.1'),
			(new DataWrapper(22, 'world second child'))->setPosition('2.2'),
			(new DataWrapper(23, 'world third child'))->setPosition('2.3'),
			(new DataWrapper(221, 'world second-first'))->setPosition('2.2.1'),
			(new DataWrapper(222, 'world second-second'))->setPosition('2.2.2'),
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition('2.2.2.1'),
			(new DataWrapper(223, 'world second-third'))->setPosition('2.2.3'),
			(new DataWrapper(3, 'a lonely foo'))->setPosition('3'),
		];
	}

}
