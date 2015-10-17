<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Iterator;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Node\SimpleNode,
	Oliva\Utils\Tree\Iterator\TreeIterator;

$root = new SimpleNode(0);
$root->addChild($n1 = new SimpleNode(1), 1)
		->addChild($n11 = new SimpleNode(11), 11)
		->addChild(new SimpleNode(111), 111);
$n1->addChild(new SimpleNode(12), 12);
$n11->addChild(new SimpleNode(112), 112);
$root->addChild(new SimpleNode(2), 2)
		->addChild(new SimpleNode(21), 21);
$root->addChild(new SimpleNode(3), 3);


// run tests
subroutine1($root);
subroutine2($root);


function subroutine1(SimpleNode $root)
{
	$expected = [
		$root->getChild(1),
		$root->getChild(2),
		$root->getChild(3),
		$root->getChild(1)->getChild(11),
		$root->getChild(1)->getChild(12),
		$root->getChild(2)->getChild(21),
		$root->getChild(1)->getChild(11)->getChild(111),
		$root->getChild(1)->getChild(11)->getChild(112),
	];
	$iterator = new TreeIterator($root, TreeIterator::BREADTH_FIRST_RECURSION);
	Assert::same($expected, array_merge([], iterator_to_array($iterator)));
}


function subroutine2(SimpleNode $root)
{
	$expected = [
		$root->getChild(1),
		$root->getChild(1)->getChild(11),
		$root->getChild(1)->getChild(11)->getChild(111),
		$root->getChild(1)->getChild(11)->getChild(112),
		$root->getChild(1)->getChild(12),
		$root->getChild(2),
		$root->getChild(2)->getChild(21),
		$root->getChild(3),
	];
	$iterator = new TreeIterator($root, TreeIterator::DEPTH_FIRST_RECURSION);
	Assert::same($expected, array_merge([], iterator_to_array($iterator)));
}
