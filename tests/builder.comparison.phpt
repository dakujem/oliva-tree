<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\Comparison;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';

use Tester\Assert;
use Oliva\Utils\Tree\Builder\RecursiveTreeBuilder,
	Oliva\Utils\Tree\Builder\PathTreeBuilder,
	Oliva\Utils\Tree\Comparator\NodeComparator;
use Oliva\Test\Scene\DefaultScene;

$parentMember = 'parent';
$idMember = 'id';
$charsPerLevel = 3;
$hierarchyMember = 'position';

// different builders
$builderRecursive = new RecursiveTreeBuilder($parentMember, $idMember);
$builderPath = new PathTreeBuilder($hierarchyMember, $charsPerLevel);

// identical data
$data = (new DefaultScene)->getData();

$root1 = $builderRecursive->build($data);
$root2 = $builderPath->build($data);

// do not compare for indices (they will differ)
$comparator = new NodeComparator(TRUE, NULL, FALSE, TRUE);
Assert::same(TRUE, $comparator->compare($root1, $root2));

