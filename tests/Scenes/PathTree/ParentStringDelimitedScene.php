<?php


namespace Oliva\Test\Scene\PathTree;

use Oliva\Test\DataWrapper;
use Oliva\Test\Scene\Scene;


/**
 * ParentStringDelimitedScene.
 *
 *
 * Hierarchy string contains only parent IDs delimited.
 *
 *
 *
 * @author Andrej Rypak <andrej.rypak@viaaurea.cz>
 * @copyright Via Aurea, s.r.o.
 */
class ParentStringDelimitedScene extends Scene
{


	public function getData($suffix = '', $prefix = '')
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
			(new DataWrapper(1, 'hello'))->setPosition($prefix . '' . $suffix),
			(new DataWrapper(2, 'world'))->setPosition($prefix . '' . $suffix),
			(new DataWrapper(11, 'hello child'))->setPosition($prefix . '1' . $suffix),
			(new DataWrapper(12, 'hello second child'))->setPosition($prefix . '1' . $suffix),
			(new DataWrapper(21, 'world first child'))->setPosition($prefix . '2' . $suffix),
			(new DataWrapper(22, 'world second child'))->setPosition($prefix . '2' . $suffix),
			(new DataWrapper(23, 'world third child'))->setPosition($prefix . '2' . $suffix),
			(new DataWrapper(221, 'world second-first'))->setPosition($prefix . '2.22' . $suffix),
			(new DataWrapper(222, 'world second-second'))->setPosition($prefix . '2.22' . $suffix),
			(new DataWrapper(2221, 'world\'s furthest leaf'))->setPosition($prefix . '2.22.222' . $suffix),
			(new DataWrapper(223, 'world second-third'))->setPosition($prefix . '2.22' . $suffix),
			(new DataWrapper(3, 'a lonely foo'))->setPosition($prefix . '' . $suffix),
		];
	}

}
