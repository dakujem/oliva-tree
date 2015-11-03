<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\PathBuilder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';
require_once SCENES . '/MissingRefScene.php';
require_once SCENES . '/BridgingScene.php';
require_once SCENES . '/PathTree/CollidingRootsScene.php';
require_once SCENES . '/PathTree/UndefinedRootScene.php';
require_once SCENES . '/PathTree/CutoffScene.php';
require_once SCENES . '/PathTree/CutoffScene2.php';

use RuntimeException;
use Tester\Assert;
use Oliva\Utils\Tree\Builder\PathTreeBuilder;
use Oliva\Utils\Tree\Node\Node,
	Oliva\Test\Scene\Scene,
	Oliva\Test\Scene\DefaultScene,
	Oliva\Test\Scene\MissingRefScene,
	Oliva\Test\Scene\BridgingScene,
	Oliva\Test\Scene\PathTree\CollidingRootsScene,
	Oliva\Test\Scene\PathTree\UndefinedRootScene,
	Oliva\Test\Scene\PathTree\CutoffScene,
	Oliva\Test\Scene\PathTree\CutoffScene2,
	Oliva\Utils\Tree\Iterator\TreeIterator,
	Oliva\Utils\Tree\Iterator\TreeSimpleFilterIterator;

$hierarchyMember = 'position';
$charsPerLevel = 3;

$builder = new PathTreeBuilder($hierarchyMember, $charsPerLevel);

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

$data = (new DefaultScene)->getData();
$root = $builder->build($data);

// basic test
testRoot($root);

// multiple root problem - colliding roots
// - the behaviour is the same as any other duplicit hierarchy node - tha data is overwritten
// - do not rely on this behaviour as it may change without notice
collidingRootsSubroutine($builder, new CollidingRootsScene());

// no root - no problem
noRootSubroutine($builder);

// test missing reference behaviour
missingRefSubroutine($builder);

// bridging
bridgingSubroutine($builder, $root);

// hierarchy cutoff
cutoffSubroutine0($builder);
cutoffSubroutine1($builder);
cutoffSubroutine2($builder);


function testRoot(Node $root)
{
	Assert::equal(FALSE, $root->getChild(NULL));
	Assert::same('hello', $root->getChild('001')->title);
	Assert::same('world', $root->getChild('002')->title);
	Assert::same('world second child', $root->getChild('002')->getChild('002002')->title);
	Assert::same('world\'s furthest leaf', $root->getChild('002')->getChild('002002')->getChild('002002002')->getChild('002002002001')->title);
	Assert::same('a lonely foo', $root->getChild('003')->title);
	Assert::same(3, count($root->getChild('002')->getChild('002002')->getChildren()));
}


function collidingRootsSubroutine(PathTreeBuilder $builder, CollidingRootsScene $scene)
{
	$root = $builder->build($scene->getData());
	Assert::same('root2', $root->title);
	Assert::same(2, count($root->getChildren()));
	Assert::same('hello', $root->getChild('001')->title);
	Assert::same(FALSE, $root->getChild('002')); // Note: the return value can change to NULL
	Assert::same('lonely', $root->getChild('003')->title);
	Assert::same('hello child', $root->getChild('001')->getChild('001001')->title);
}


function noRootSubroutine(PathTreeBuilder $builder)
{
	$root = $builder->build((new UndefinedRootScene())->getData());
	Assert::same(NULL, $root->getContents());
	Assert::same(2, count($root->getChildren()));
	Assert::same('hello', $root->getChild('001')->title);
	Assert::same('world', $root->getChild('002')->title);
}


function missingRefSubroutine(PathTreeBuilder $builder)
{
	// when any part is missing, the path to the root is bridged with empty stub nodes
	$data = (new MissingRefScene())->getData();

	$root = $builder->build($data);

	Assert::same(3, count($root->getChildren()));
	Assert::same('hello', $root->getChild('001')->title);
	Assert::same('lonely', $root->getChild('003')->title);

	Assert::same('world', $root->getChild('001')->getChild('001001')->title);
	Assert::same(NULL, $root->getChild('001')->getChild('001001')->getChild('001001001')->getContents()); // stub node
	Assert::same('foo', $root->getChild('001')->getChild('001001')->getChild('001001001')->getChild('001001001001')->title);
	Assert::same(FALSE, $root->getChild(2)); // Note: the return value can change to NULL
	// whole branch bridged
	Assert::same(NULL, $root->getChild('002')->getContents());
	Assert::same(NULL, $root->getChild('002')->getChild('002002')->getContents());
	Assert::same(NULL, $root->getChild('002')->getChild('002002')->getChild('002002002')->getContents());
	Assert::same('world\'s furthest leaf', $root->getChild('002')->getChild('002002')->getChild('002002002')->getChild('002002002001')->title);
}


function bridgingSubroutine(PathTreeBuilder $builder)
{
	// when reference to parent is missing, the whole branch is lost
	$data = (new BridgingScene())->getData();
	$root = $builder->build($data);

	// same testing routine as for the DefaultScene
	testRoot($root);
}


function cutoffSubroutine0(PathTreeBuilder $builder)
{
	$builderWithCutoff = clone $builder;
	$builderWithCutoff->hierarchyCutoff = '007';

	$data = (new CutoffScene())->getData();
	$data[] = ['id' => 3, $builderWithCutoff->hierarchyMember => '003', 'title' => 'stray dog'];

	// must throw exception - prefix 007 not present in "stray dog" node
	Assert::error(function()use($builderWithCutoff, $data) {
		$builderWithCutoff->build($data);
	}, RuntimeException::CLASS, NULL, 3);
}


function cutoffSubroutine1(PathTreeBuilder $builder)
{
	/* @var $root Node */
	/* @var $rootCut Node */
	/* @var $node Node */
	/* @var $nodeCut Node */
	list($root, $node, $rootCut, $nodeCut) = cutoffDataSubroutine($builder, new CutoffScene());

	// without cutoff
	Assert::same(4, $node->getLevel()); // no cut-off happened, stub root inserted
	Assert::same(NULL, $root->getContents());
	Assert::same(NULL, $root->getChild('007')->getContents());

	// test cutoff
	Assert::same(3, $nodeCut->getLevel());
	Assert::same(NULL, $rootCut->getContents());
	Assert::equal(FALSE, $rootCut->getChild('007'));
	Assert::equal(2, count($rootCut->getChildren()));
}


function cutoffSubroutine2(PathTreeBuilder $builder)
{
	/* @var $root Node */
	/* @var $rootCut Node */
	/* @var $node Node */
	/* @var $nodeCut Node */
	list($root, $node, $rootCut, $nodeCut) = cutoffDataSubroutine($builder, new CutoffScene2());

	// without cutoff
	Assert::same(4, $node->getLevel()); // no cut-off happened, stub root inserted
	Assert::same(NULL, $root->getContents());
	Assert::same('root', $root->getChild('007')->title);

	// test cutoff
	Assert::same(3, $nodeCut->getLevel());
	Assert::same('root', $rootCut->title);
	Assert::equal(FALSE, $rootCut->getChild('007'));
	Assert::equal(2, count($rootCut->getChildren()));
}


function cutoffDataSubroutine(PathTreeBuilder $builder, Scene $scene)
{
	$data = $scene->getData();
	$root = $builder->build($data);

	$it = new TreeSimpleFilterIterator(new TreeIterator($root), $builder->hierarchyMember, '007002002001');
	$it->rewind();
	/* @var $node Node */
	$node = $it->current();

	$builderWithCutoff = clone $builder;
	$builderWithCutoff->hierarchyCutoff = '007';

	$rootCut = $builderWithCutoff->build($data);

	$it2 = new TreeSimpleFilterIterator(new TreeIterator($rootCut), $builder->hierarchyMember, '007002002001');
	$it2->rewind();
	/* @var $nodeCut Node */
	$nodeCut = $it2->current();

	return [$root, $node, $rootCut, $nodeCut];
}
