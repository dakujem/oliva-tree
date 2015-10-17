<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\JsonBuilder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Builder\JsonTreeBuilder;

// run the tests
subroutine1();


function subroutine1()
{
	$dataArray = ['a' => 'a', 'b' => 'b', 'children' => [
			['c' => 'c', 'children' => [['e' => 'e', 'f' => 'f',]]],
			['d' => 'd', 'children' => [['g' => 'g', 'h' => 'h',]]],
	]];
	$data = json_encode($dataArray);

	$builder = new JsonTreeBuilder('children');
	$root = $builder->build($data);

	Assert::equal((object) ['a' => 'a', 'b' => 'b'], $root->getContents());
	Assert::equal((object) ['c' => 'c'], $root->getChild(0)->getContents());
	Assert::equal((object) ['d' => 'd'], $root->getChild(1)->getContents());
	Assert::equal((object) ['e' => 'e', 'f' => 'f'], $root->getChild(0)->getChild(0)->getContents());
}
