<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Comparator;

require_once __DIR__ . '/bootstrap.php';

use Oliva\Utils\Tree\Comparator\NodeComparator;
use Oliva\Utils\Tree\Node\Node;
use Oliva\Utils\Tree\Node\SimpleNode;
use Tester\Assert;

$node1 = new SimpleNode(1);
$node2 = new SimpleNode(1);
$node3 = new SimpleNode(2);

// sanity test
Assert::same(TRUE, $node1 == $node2);
Assert::same(FALSE, $node1 === $node2);
Assert::same(FALSE, $node1 == $node3);

// the comparator
$comparator = new NodeComparator();


// basic comparison (no recursion needed)
Assert::same(TRUE, $comparator->compare($node1, $node2));
Assert::same(TRUE, $comparator->compare($node2, $node1));
Assert::same(FALSE, $comparator->compare($node1, $node3));
Assert::same(FALSE, $comparator->compare($node3, $node1));

// callback compare
Assert::same(TRUE, $comparator->callbackCompare($node1, $node2, function($nodeData1, $nodeData2) {
			return $nodeData1 == $nodeData2;
		}));


$node1->addChild(new SimpleNode(10));
$node1->addChild(new SimpleNode(20));
$node2->addChild(new SimpleNode(10));
$node2->addChild(new SimpleNode(20));


// comapre with recursion needed
Assert::same(TRUE, $comparator->compare($node1, $node2));
Assert::same(TRUE, $comparator->compare($node2, $node1));
Assert::same(FALSE, $comparator->compare($node1, $node3));
Assert::same(FALSE, $comparator->compare($node3, $node1));


// different node count
$node2->addChild(new SimpleNode(30));
Assert::same(FALSE, $comparator->compare($node1, $node2));

// same again
$node1->addChild(new SimpleNode(30));
Assert::same(TRUE, $comparator->compare($node1, $node2));

// different child node content
$node2->addChild(new SimpleNode(40));
$node1->addChild(new SimpleNode(41));
Assert::same(FALSE, $comparator->compare($node1, $node2));


$compareCallback = function($data1, $data2) {
	return $data1 == $data2;
};
$failingCallback = function() {
	return FALSE;
};

// own callback
Assert::same(FALSE, $comparator->callbackCompare($node1, $node2, $failingCallback));
Assert::same(FALSE, $comparator->callbackCompare($node1, $node2, $compareCallback)); // should fail, nodes are different

$nonRecursiveComparator = new NodeComparator(FALSE, NULL, NULL, TRUE);
// test non-recursive comparison, should be true without recursion
Assert::same(TRUE, $nonRecursiveComparator->callbackCompare($node1, $node2, $compareCallback));

// test node type comparison
Assert::same(FALSE, $nonRecursiveComparator->compare(new SimpleNode('foo'), new SimpleNode('bar'))); // fails, different data
Assert::same(TRUE, $nonRecursiveComparator->compare(new SimpleNode('foo'), new SimpleNode('foo'))); // success, same node data
Assert::same(FALSE, $nonRecursiveComparator->compare(new SimpleNode('foo'), new Node('foo'))); // should fail, different node classes
Assert::same(TRUE, $nonRecursiveComparator->compare(new Node('foo'), new Node('foo')));

// test child indices comparison
$nonIndexComparingComparator = new NodeComparator(TRUE, NULL, FALSE);
$nodeA = new SimpleNode('A');
$nodeA->addChild(new SimpleNode('B'), 0);
$nodeA->addChild(new SimpleNode('C'), 2);
$nodeB = new SimpleNode('A');
$nodeB->addChild(new SimpleNode('B'), 1);
$nodeB->addChild(new SimpleNode('C'), 123);
$nodeC = new SimpleNode('A');
$nodeC->addChild(new SimpleNode('B'), '');
$nodeC->addChild(new SimpleNode('C'), 2);

Assert::same(FALSE, $comparator->compare($nodeA, $nodeB));
Assert::same(FALSE, $comparator->compare($nodeA, $nodeC));
Assert::same(TRUE, $nonIndexComparingComparator->compare($nodeA, $nodeB));
Assert::same(TRUE, $nonIndexComparingComparator->compare($nodeA, $nodeC));

// test strictness - loose index comparator, loose index comparison
$looseIndexComparator = new NodeComparator(TRUE, NodeComparator::STRICT_SCALARS, true);
if (PHP_VERSION_ID >= 80000) {
    // NOTE: this test will fail in PHP 8+
    Assert::same(false, $looseIndexComparator->compare($nodeA, $nodeC));
} else {
    Assert::same(true, $looseIndexComparator->compare($nodeA, $nodeC));
}

// test strictness setup - scalars
$nodeInt1 = new SimpleNode(10);
$nodeInt2 = new SimpleNode('10');
$nodeInt3 = new SimpleNode(0xA);
$nodeInt4 = new SimpleNode(0b1010);
$nodeInt5 = new SimpleNode('10foo'); // php string handling...
$nodeEleven = new SimpleNode(0b1011);
$nodeNULL = new SimpleNode(NULL);
$nodeZero = new SimpleNode(0);

Assert::same(TRUE, $comparator->compare($nodeInt1, $nodeInt3));
Assert::same(TRUE, $comparator->compare($nodeInt3, $nodeInt4));
Assert::same(FALSE, $comparator->compare($nodeInt1, $nodeEleven));
Assert::same(FALSE, $comparator->compare($nodeInt1, $nodeInt2));
Assert::same(FALSE, $comparator->compare($nodeInt2, $nodeInt3));
Assert::same(FALSE, $comparator->compare($nodeInt2, $nodeInt5));
Assert::same(FALSE, $comparator->compare($nodeInt1, $nodeInt5));
Assert::same(FALSE, $comparator->compare($nodeNULL, $nodeZero));

$tolerantComparator = new NodeComparator(NULL, NodeComparator::STRICT_NONE);
Assert::same(FALSE, $tolerantComparator->compare($nodeInt1, $nodeEleven));
Assert::same(TRUE, $tolerantComparator->compare($nodeInt1, $nodeInt2));
Assert::same(TRUE, $tolerantComparator->compare($nodeInt1, $nodeInt3));
Assert::same(TRUE, $tolerantComparator->compare($nodeInt2, $nodeInt3));
Assert::same(TRUE, $tolerantComparator->compare($nodeInt3, $nodeInt4));
Assert::same(TRUE, $tolerantComparator->compare($nodeNULL, $nodeZero));

if (PHP_VERSION_ID >= 80000) {
    // NOTE: this test will fail in PHP 8+
    Assert::same(false, $tolerantComparator->compare($nodeInt2, $nodeInt5));
    Assert::same(false, $tolerantComparator->compare($nodeInt1, $nodeInt5));
} else {
    // funny php string handling...
    Assert::same(false, $tolerantComparator->compare($nodeInt2, $nodeInt5));
    Assert::same(true, $tolerantComparator->compare($nodeInt1, $nodeInt5));
}

// test strictness setup - arrays
$nodeArray1 = new SimpleNode(['a', 'b', 1]);
$nodeArray2 = new SimpleNode(['a', 'b', '1']);
$nodeArray3 = new SimpleNode([ 1 => 'b', 0 => 'a', 2 => 1]);
$nodeArray4 = new SimpleNode(['a' => 'a', 'b' => 'b', 'c' => 1]);

// arrays identical when they have the same key/value pairs in the same order and of the same types, equal have equal elements
Assert::same(FALSE, $comparator->compare($nodeArray1, $nodeArray2));
Assert::same(TRUE, $tolerantComparator->compare($nodeArray1, $nodeArray2));
Assert::same(FALSE, $comparator->compare($nodeArray1, $nodeArray3));
Assert::same(TRUE, $tolerantComparator->compare($nodeArray1, $nodeArray3));
Assert::same(FALSE, $tolerantComparator->compare($nodeArray1, $nodeArray4));

// test strictness setup - objects
$nodeObject1 = new SimpleNode((object) ['foo' => 'bar']);
$nodeObject2 = new SimpleNode((object) ['hello' => 'world']);
$nodeObject3 = new SimpleNode((object) ['foo' => 'bar']);
$strictObjectComparator = new NodeComparator(NULL, NodeComparator::STRICT_OBJECTS);
Assert::same(FALSE, $comparator->compare($nodeObject1, $nodeObject2));
Assert::same(TRUE, $comparator->compare($nodeObject1, $nodeObject3)); // objects are compared loosly by default
Assert::same(FALSE, $strictObjectComparator->compare($nodeObject1, $nodeObject2));
Assert::same(FALSE, $strictObjectComparator->compare($nodeObject1, $nodeObject3));


