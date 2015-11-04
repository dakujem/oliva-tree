<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Builder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use RuntimeException;
use Tester\Assert;
use Oliva\Utils\Tree\Builder\SimpleTreeBuilder,
	Oliva\Utils\Tree\Builder\RecursiveTreeBuilder;
use Oliva\Utils\Tree\Node\Node,
	Oliva\Utils\Tree\Node\SimpleNode;

// run tests
subroutine1();
subroutine2();


function subroutine1()
{
	$builder = new SimpleTreeBuilder('children');

	$data = ['a' => 'a', 'b' => 'b', 'children' => [
			['c' => 'c', 'children' => [['e' => 'e', 'f' => 'f',]]],
			['d' => 'd', 'children' => [['g' => 'g', 'h' => 'h',]]],
	]];

	// default node type should be Node
	Assert::type(Node::className() /* Node::CLASS */, $builder->build($data));

	// change node class
	$nodeClass = SimpleNode::className(); //SimpleNode::CLASS;
	$builder->nodeClass = $nodeClass;
	Assert::type($nodeClass, $builder->build($data));

	// set own node creation callback
	$builder->setNodeCallback(function($data, $className) {
		return new $className(array_merge($data, ['foo' => 'bar',]));
	}, $nodeClass);

	$root = $builder->build($data);
	Assert::type($nodeClass, $root);
	Assert::same(['a' => 'a', 'b' => 'b', 'foo' => 'bar'], $root->getContents());
	Assert::same(['c' => 'c', 'foo' => 'bar'], $root->getChild(0)->getContents());
}


function subroutine2()
{
	$builder = new RecursiveTreeBuilder('parent', 'id');


	// data for this builder have to contain 'id' and 'parent' members
	Assert::exception(function() use ($builder) {
		$builder->build(['a']);
	}, 'RuntimeException' /* RuntimeException::CLASS */, 'Missing attribute "id" of $item of type string.', 1);

	// set own throwing error handling routine
	$builder->setDataErrorCallback(function() {
		throw new RuntimeException('foo', 123);
	}, 'p1', 'p2');

	Assert::exception(function() use ($builder) {
		$builder->build(['a']);
	}, 'RuntimeException' /* RuntimeException::CLASS */, 'foo', 123);


	// set own repairing error handling routine
	$builder->setDataErrorCallback(function($item, $member, $exception, $id1, $id2) {
		if ($member === 'id') {
			switch ($item) {
				case 'a':
					return $id1;
				case 'b':
					return $id2;
			}
		}
		if ($member === 'parent') {
			switch ($item) {
				case 'a':
					return $id2;
				case 'b':
					return NULL;
			}
		}
		throw new RuntimeException('failing test');
	}, 'id_a', 'id_b');

	// no exception should be thrown
	$root = $builder->build(['a', 'b']);

	// the tree should look like this:
	// - [b]
	//    |
	//    +- id_a : [a]
	//
	Assert::same('b', $root->getContents());
	Assert::same('a', $root->getChild('id_a')->getContents());


	// building from invalid data
	Assert::exception(function() use ($builder) {
		$builder->build('foo');
	}, 'RuntimeException' /* RuntimeException::CLASS */, 'The data provided must be an array or must be traversable, string provided.', 2);
}
