<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\RecursiveBuilder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';
require_once SCENES . '/MissingRefScene.php';
require_once SCENES . '/BridgingScene.php';
require_once SCENES . '/RecursiveTree/ImplicitRootSceneBase.php';
require_once SCENES . '/RecursiveTree/ImplicitRootScene1.php';
require_once SCENES . '/RecursiveTree/ImplicitRootScene2.php';
require_once SCENES . '/RecursiveTree/ImplicitRootScene3.php';
require_once SCENES . '/RecursiveTree/ImplicitRootScene4.php';
require_once SCENES . '/RecursiveTree/MultiRootScene.php';
require_once SCENES . '/RecursiveTree/NoRootScene.php';

use RuntimeException;
use Tester\Assert;
use Oliva\Utils\Tree\Builder\RecursiveTreeBuilder;
use Oliva\Test\Scene\DefaultScene,
	Oliva\Test\Scene\MissingRefScene,
	Oliva\Test\Scene\BridgingScene,
	Oliva\Test\Scene\RecursiveTree\ImplicitRootScene1,
	Oliva\Test\Scene\RecursiveTree\ImplicitRootScene2,
	Oliva\Test\Scene\RecursiveTree\ImplicitRootScene3,
	Oliva\Test\Scene\RecursiveTree\ImplicitRootScene4,
	Oliva\Test\Scene\RecursiveTree\ImplicitRootSceneBase,
	Oliva\Test\Scene\RecursiveTree\MultiRootScene,
	Oliva\Test\Scene\RecursiveTree\NoRootScene,
	Oliva\Utils\Tree\Node\Node,
	Oliva\Utils\Tree\Comparator\NodeComparator;

$parentMember = 'parent';
$idMember = 'id';

$builder = new RecursiveTreeBuilder($parentMember, $idMember);

//		0   root
//		|
//		+--  1   hello
//		|    |
//		|    +--  11
//		|    |
//		|    +--  12
//		|
//		+--  2   world
//		|    |
//		|    +--  21
//		|    |
//		|    +--  22
//		|    |     |
//		|    |     +--  221
//		|    |     |
//		|    |     +--  222
//		|    |     |      |
//		|    |     |      +--  2221
//		|    |     |
//		|    |     +--  223
//		|    |
//		|    +--  23
//		|
//		+--  3

$builder->throwOnMultipleRoots = TRUE; // this will be the default setting later on

$data = (new DefaultScene)->getData();
$root = $builder->build($data);

testRoot($root);


function testRoot($root)
{
	Assert::same('hello', $root->getChild(1)->title);
	Assert::same('world', $root->getChild(2)->title);
	Assert::same('world second child', $root->getChild(2)->getChild(22)->title);
	Assert::same('world\'s furthest leaf', $root->getChild(2)->getChild(22)->getChild(222)->getChild(2221)->title);
	Assert::same('a lonely foo', $root->getChild(3)->title);
	Assert::same(3, count($root->getChild(2)->getChild(22)->getChildren()));
}

// implicit roots
implicitRootSubroutine($builder, new ImplicitRootScene2());
implicitRootSubroutine($builder, new ImplicitRootScene3());
implicitRootSubroutine($builder, new ImplicitRootScene4());

// different behaviour with NULL! $node->setParent(NULL) automatically sets the $node as the root
$nullRoot = $builder->build((new ImplicitRootScene1())->getData());
Assert::same(100, $nullRoot->id);
Assert::same(123, $nullRoot->getChild(123)->id);


function implicitRootSubroutine(RecursiveTreeBuilder $builder, ImplicitRootSceneBase $scene)
{
	$root = $builder->build($scene->getData());
	Assert::same($scene->implicitRoot, $root->id);
	Assert::same(100, $root->getChild(100)->id);
	Assert::same(123, $root->getChild(100)->getChild(123)->id);
}

// no root exception
Assert::exception(function() use ($builder) {
	$builder->build((new NoRootScene())->getData());
}, RuntimeException::CLASS, 'No root node present.', 100);


// multiple root problem
// - the last occuring root is returned as the root, but this is an unusal and unintended behaviour
// - do not rely on this result as it may change without notice
$builder->throwOnMultipleRoots = FALSE;
$fooRoot = $builder->build((new MultiRootScene())->getData());
Assert::same(2, $fooRoot->id);
Assert::same(0, count($fooRoot->getChildren()));
// exception on multi-roots
$builder->throwOnMultipleRoots = TRUE;
Assert::exception(function() use ($builder) {
	$builder->build((new MultiRootScene())->getData());
}, RuntimeException::CLASS, 'Multiple roots occurring in the data.', 200);


// test missing reference behaviour
missingRefSubroutine($builder);
bridgingSubroutine($builder, $root);


function missingRefSubroutine(RecursiveTreeBuilder $builder)
{
	// when reference to parent is missing, the whole branch is lost
	$data = (new MissingRefScene())->getData();

	$root = $builder->build($data);

	Assert::same(2, count($root->getChildren()));
	Assert::same('hello', $root->getChild(1)->title);
	Assert::same('lonely', $root->getChild(3)->title);

	Assert::same('world', $root->getChild(1)->getChild(11)->title);
	Assert::same([], $root->getChild(1)->getChild(11)->getChildren()); // no children, no bridging is happening
	// whole branch lost
	Assert::same(FALSE, $root->getChild(2)); // Note: the return value can change to NULL
}


function bridgingSubroutine(RecursiveTreeBuilder $builder, Node $expectedOutput)
{
	// when reference to parent is missing, the whole branch is lost
	$data = (new BridgingScene())->getData();
	$root = $builder->build($data);

	// same testing routine as for the DefaultScene
	testRoot($root);

	//NOTE: no comparison can be done (yet), as the children are in different order (no comparator for that complexity yet)
	$comparator = new NodeComparator(TRUE, NodeComparator::STRICT_SCALARS, TRUE);
	Assert::equal(FALSE, $comparator->compare($root, $expectedOutput)); // maybe a to-do task
}
