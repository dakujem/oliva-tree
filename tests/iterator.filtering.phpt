<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Filtering;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Node\NodeBase,
	Oliva\Utils\Tree\Iterator\TreeIterator,
	Oliva\Utils\Tree\Iterator\TreeFilterIterator,
	Oliva\Utils\Tree\Iterator\TreeSimpleFilterIterator,
	Oliva\Utils\Tree\Iterator\TreeCallbackFilterIterator;


class FooNode extends NodeBase
{
	public $a = NULL;
	public $b = NULL;
	public $c = NULL;


	public function __construct($a, $b = NULL, $c = NULL)
	{
		$this->a = $a;
		$this->b = $b;
		$this->c = $c;
	}

}

$root = new FooNode(0, 'foo', 'root');
$root->addChild($n1 = new FooNode(1, 'foo', 'node of root'), 1)
		->addChild($n11 = new FooNode(11, 'foo', 'yay'), 11)
		->addChild(new FooNode(111, 'huh'), 111);
$n1->addChild(new FooNode(12), 12);
$n11->addChild(new FooNode(112), 112);
$root->addChild(new FooNode(2, 'last', 'mohican'), 2)
		->addChild(new FooNode(21, 'foo'), 21);
$root->addChild(new FooNode(3, 'last', 'mohican'), 3);

$iterator = new TreeIterator($root, TreeIterator::BREADTH_FIRST_RECURSION);

// run tests
subroutine1($root, $iterator);
subroutine2($root, $iterator);
subroutine3($root, $iterator);
subroutine4($root, $iterator);
subroutine5($root, $iterator);
subroutine6($root, $iterator);


function subroutine1(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(2)->getChild(21),
	];
	Assert::same($expected, array_merge([], iterator_to_array(new TreeSimpleFilterIterator($iterator, 'a', 21))));
	Assert::same([], iterator_to_array(new TreeSimpleFilterIterator($iterator, 'a', 1234)));
}


function subroutine2(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(2)->getChild(21),
	];
	Assert::same($expected, array_merge([], iterator_to_array(new TreeFilterIterator($iterator, ['a' => 21]))));
	Assert::same([], iterator_to_array(new TreeFilterIterator($iterator, ['a' => 21, 'b' => 'nonsense'])));
	Assert::same($expected, array_merge([], iterator_to_array(new TreeFilterIterator($iterator, ['a' => 21, 'b' => 'nonsense'], TreeFilterIterator::MODE_OR))));
}


function subroutine3(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(1),
		$root->getChild(1)->getChild(11),
		$root->getChild(2)->getChild(21),
	];
	Assert::same($expected, array_merge([], iterator_to_array(new TreeFilterIterator($iterator, ['b' => 'foo']))));
	Assert::same([], iterator_to_array(new TreeFilterIterator($iterator, ['b' => 'sasek'])));

	$cb = function(FooNode $item) {
		return $item->b === 'foo';
	};
	Assert::same($expected, array_merge([], iterator_to_array(new TreeCallbackFilterIterator($iterator, $cb))));
}


function subroutine4(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(2),
		$root->getChild(3),
	];
	Assert::same($expected, array_merge([], iterator_to_array(new TreeFilterIterator($iterator, ['b' => 'last', 'c' => 'mohican']))));
}


function subroutine5(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(1),
		$root->getChild(2),
		$root->getChild(3),
		$root->getChild(1)->getChild(11),
		$root->getChild(2)->getChild(21),
	];
	Assert::same($expected, array_merge([], iterator_to_array(new TreeFilterIterator($iterator, ['b' => 'foo', 'c' => 'mohican'], TreeFilterIterator::MODE_OR))));
}


function subroutine6(FooNode $root, TreeIterator $iterator)
{
	$expected = [
		$root->getChild(1),
		$root->getChild(2),
		$root->getChild(3),
		$root->getChild(1)->getChild(11),
		$root->getChild(1)->getChild(12),
		$root->getChild(2)->getChild(21),
	];
	$cb = function(FooNode $item, $index, $param1, $param2, $param3) {
		return $item->b === $param1 || $item->c === $param2 || $index === $param3;
	};
	Assert::same($expected, array_merge([], iterator_to_array(new TreeCallbackFilterIterator($iterator, $cb, 'foo', 'mohican', 12))));
}
