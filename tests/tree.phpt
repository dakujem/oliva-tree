<?php

use Tester\Assert;
use Oliva\Utils\Tree\Tree,
	Oliva\Utils\Tree\Node\Node;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';
Tester\Environment::setup();


$tree = new Tree();
$root = new Node(['id' => 1, 'title' => 'foo']);
$tree->setRoot($root);

Assert::equal(1, $tree->getRoot()->id);

//Assert::exception(function() {
//	$tree->
//}, 'Exception');


/**/
// tuna to zdochne - tester nenacita bootstrap - ?
foo();
