<?php

use Tester\Assert;
use Oliva\Utils\Tree\RecursiveTree;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/bootstrap.php';
Tester\Environment::setup();


list($parentTreeData, $perentTreeMultiRootsData, $perentTreeImplicitRootData1, $perentTreeImplicitRootData2, $perentTreeImplicitRootData3, $perentTreeImplicitRootData4) = recursiveTreeData();

$tree = new RecursiveTree($parentTreeData);


Assert::equal(0, $tree->getRoot()->id);

