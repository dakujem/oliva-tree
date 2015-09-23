<?php

require_once __DIR__ . '/bootstrap.php';

use Tester\Assert;
use Oliva\Utils\Tree\Tree,
	Oliva\Utils\Tree\Node\Node;

$tree = new Tree();
$root = new Node(['id' => 1, 'title' => 'foo']);
$tree->setRoot($root);

Assert::equal(1, $tree->getRoot()->id);

dump($tree);

//Assert::equal(3, $tree->getRoot()->id);
//Assert::exception(function() {
//	$tree->
//}, 'Exception');




