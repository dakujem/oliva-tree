<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\DataTree;

require_once __DIR__ . '/bootstrap.php';

require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\DataTree,
	Oliva\Utils\Tree\Builder\RecursiveTreeBuilder,
	Oliva\Test\Scene\DefaultScene,
	Oliva\Utils\Tree\Comparator\NodeComparator;

$comparator = new NodeComparator();

$data = (new DefaultScene())->getData();
$builder = new RecursiveTreeBuilder();
$node = $builder->build($data);

$tree = new DataTree($data, $builder);
$root = $tree->getRoot();
Assert::same(TRUE, $comparator->compare($node, $root));

$tree->rebuild($data);
$rootNew = $tree->getRoot();
Assert::same(TRUE, $comparator->compare($node, $rootNew));
