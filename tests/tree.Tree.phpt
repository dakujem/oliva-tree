<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Tree;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Tree,
	Oliva\Utils\Tree\Node\Node,
	Oliva\Utils\Tree\Iterator\TreeIterator,
	Oliva\Utils\Tree\Builder\RecursiveTreeBuilder,
	Oliva\Test\Scene\DefaultScene,
	Oliva\Utils\Tree\Iterator\TreeFilterIterator;

// run tests
subroutine1();
subroutine2();
subroutine3();


function subroutine1()
{
	$tree = new Tree();
	$root = new Node(['id' => 1, 'title' => 'foo']);
	$tree->setRoot($root);

	Assert::equal(1, $tree->getRoot()->id);

	Assert::type(TreeIterator::CLASS, $tree->getIterator());
	Assert::same(TreeIterator::BREADTH_FIRST_RECURSION, $tree->getIterator(TreeIterator::BREADTH_FIRST_RECURSION)->getRecursion());
	Assert::same(TreeIterator::DEPTH_FIRST_RECURSION, $tree->getIterator(TreeIterator::DEPTH_FIRST_RECURSION)->getRecursion());
	Assert::same(TreeIterator::NON_RECURSIVE, $tree->getIterator(TreeIterator::NON_RECURSIVE)->getRecursion());
}


function subroutine2()
{
	$tree = new Tree();
	$tree->setRoot((new RecursiveTreeBuilder())->build((new DefaultScene())->getData()));

	// callback iterator
	$iterator = $tree->getIterator();
	Assert::same(iterator_to_array($iterator), iterator_to_array($tree->getCallbackFilterIterator(function() {
						return TRUE;
					}, $iterator->getRecursion())));
	Assert::same([], iterator_to_array($tree->getCallbackFilterIterator(function() {
						return FALSE;
					})));
	Assert::same([221 => $tree->getRoot()->getChild(2)->getChild(22)->getChild(221)], iterator_to_array($tree->getCallbackFilterIterator(function($item) {
						return $item->id === 221;
					})));

	// filter iterator
	Assert::same(iterator_to_array($iterator), iterator_to_array($tree->getFilterIterator(['foo' => 'bar'], TreeFilterIterator::MODE_AND, $iterator->getRecursion())));
	Assert::same([], iterator_to_array($tree->getFilterIterator(['foo' => 'bar', 'a' => 'b'], TreeFilterIterator::MODE_AND)));
	Assert::same(iterator_to_array($iterator), iterator_to_array($tree->getFilterIterator(['foo' => 'bar', 'a' => 'b'], TreeFilterIterator::MODE_OR, $iterator->getRecursion())));
	Assert::same([221 => $tree->getRoot()->getChild(2)->getChild(22)->getChild(221)], iterator_to_array($tree->getFilterIterator(['id' => 221])));

	// find
	Assert::same($tree->getRoot()->getChild(2)->getChild(22)->getChild(221), $tree->find('id', 221));
}


function subroutine3()
{
	$tree = new Tree();
	$tree->setRoot((new RecursiveTreeBuilder())->build((new DefaultScene())->getData()));

	Assert::same($tree->getLinear(), $tree->getBreadthFirst());
	Assert::same(array_merge([$tree->getRoot()], iterator_to_array($tree->getIterator(TreeIterator::BREADTH_FIRST_RECURSION))), $tree->getBreadthFirst());
	Assert::same(array_merge([$tree->getRoot()], iterator_to_array($tree->getIterator(TreeIterator::DEPTH_FIRST_RECURSION))), $tree->getDepthFirst());
}
