<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test;

require_once __DIR__ . '/bootstrap.php';

require_once SCENES . '/RecursiveTree/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\DataTree,
	Oliva\Utils\Tree\Builder\RecursiveTreeBuilder,
	Oliva\Test\Scene\RecursiveTree\DefaultScene;

$data = (new DefaultScene())->getData();
$builder = new RecursiveTreeBuilder();
$node = $builder->build($data);

$tree = new DataTree($data, $builder);
$root = $tree->getRoot();

//TODO: compare subtrees !
Assert::same($node->title, $root->title);
$children = $root->getChildren();
foreach ($node->getChildren() as $index => $child) {
	Assert::same($child->id, $children[$index]->id);
}

$tree->rebuild($data);
$rootNew = $tree->getRoot();

//TODO: compare subtrees !
Assert::same($node->title, $rootNew->title);
$childrenNew = $rootNew->getChildren();
foreach ($node->getChildren() as $index => $child) {
	Assert::same($child->id, $childrenNew[$index]->id);
}
