<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\SimpleNode;

require_once __DIR__ . '/bootstrap.php';

use Tester\Assert;
use Oliva\Utils\Tree\Node\SimpleNode,
	Oliva\Test\DataWrapper;

$node = new SimpleNode(1);

Assert::same(1, $node->value);
Assert::same(1, $node->getValue());

$node->setValue('foobar');
Assert::same('foobar', $node->getValue());


// test clonning
$root = new SimpleNode('0');
$root->addChild(new SimpleNode(new DataWrapper('foo')));
$root->addChild(new SimpleNode('bar'));
$clone = clone $root;
Assert::equal(FALSE, $clone === $root);
Assert::equal(FALSE, $clone->getChild(0) === $root->getChild(0));
Assert::equal(TRUE, $clone->getChild(0)->getContents() === $root->getChild(0)->getContents()); // the data not clonned => identical
Assert::equal(TRUE, $clone->getChild(1)->getContents() === $root->getChild(1)->getContents()); // scalar data is identical
$clone->getChild(0)->cloneContents(); // clone the data
Assert::equal(FALSE, $clone->getChild(0)->getContents() === $root->getChild(0)->getContents()); // the data has been clonned
Assert::equal(TRUE, $clone->getChild(1)->getContents() === $root->getChild(1)->getContents()); // scalar data remains identical

