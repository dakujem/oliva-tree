<?php

require_once __DIR__ . '/bootstrap.php';

use Tester\Assert;
use Oliva\Utils\Tree\RecursiveTree;

list($parentTreeData, $perentTreeMultiRootsData, $perentTreeImplicitRootData1, $perentTreeImplicitRootData2, $perentTreeImplicitRootData3, $perentTreeImplicitRootData4) = recursiveTreeData();

$tree = new RecursiveTree($parentTreeData);


Assert::equal(0, $tree->getRoot()->id);

