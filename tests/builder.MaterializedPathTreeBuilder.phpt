<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\MaterializedPathTreeBuilder;

require_once __DIR__ . '/bootstrap.php';
require_once SCENES . '/DefaultScene.php';
require_once SCENES . '/MissingRefScene.php';
require_once SCENES . '/BridgingScene.php';
require_once SCENES . '/PathTree/CollidingRootsScene.php';
require_once SCENES . '/PathTree/UndefinedRootScene.php';
require_once SCENES . '/PathTree/CutoffScene.php';
require_once SCENES . '/PathTree/CutoffScene2.php';
require_once SCENES . '/PathTree/DelimitedScene.php';
require_once SCENES . '/PathTree/ParentStringDelimitedScene.php';

use RuntimeException;
use Tester\Assert;
use Oliva\Utils\Tree\Builder\MaterializedPathTreeBuilder;
use Oliva\Utils\Tree\Node\Node,
	Oliva\Utils\Tree\Node\INode,
	Oliva\Utils\Tree\Builder\MaterializedPathTreeHelper,
	Oliva\Utils\Tree\Builder\MaterializedPathTreeBuilderFactory,
	Oliva\Test\Scene\Scene,
	Oliva\Test\Scene\DefaultScene,
	Oliva\Test\Scene\DelimitedScene,
	Oliva\Test\Scene\PathTree\ParentStringDelimitedScene,
	Oliva\Test\Scene\MissingRefScene,
	Oliva\Test\Scene\BridgingScene,
	Oliva\Test\Scene\PathTree\CollidingRootsScene,
	Oliva\Test\Scene\PathTree\UndefinedRootScene,
	Oliva\Test\Scene\PathTree\CutoffScene,
	Oliva\Test\Scene\PathTree\CutoffScene2,
	Oliva\Utils\Tree\Iterator\TreeIterator,
	Oliva\Utils\Tree\Iterator\TreeSimpleFilterIterator;

coreTest();
testBuilderWithEncoder(new MaterializedPathTreeBuilder('position', 3), 'e1');

testDelimitedParentSceneSubroutine();


function testDelimitedParentSceneSubroutine()
{
	$scene = new ParentStringDelimitedScene();


	$delimiter = '.'; // delimit by "."
	$index = 'id'; // index children by ID


	/**/
//	$builder = new MaterializedPathTreeBuilder(NULL, $delimiter, $index);
//	$hierarchyGetter = function($data) use ($builder) {
//		return $builder->getMember($data, 'position');
//	};
//	$idGetter = function($data) use ($builder) {
//		return $builder->getMember($data, 'id');
//	};
//	$hierarchy = MaterializedPathTreeHelper::robustHierarchyGetter($delimiter, $hierarchyGetter, $idGetter);
//	$builder->setHierarchy($hierarchy);
//	$builder = new MaterializedPathTreeBuilder($hierarchy, $delimiter, $index);
	/**/


	$builder = MaterializedPathTreeBuilderFactory::createDelimitedReferenceVariant('position', $delimiter, 'id', $index);


//	dump($builder->build($scene->getData('')));
	testRoutine($builder, $scene->getData(''), 'e3');


//	dump($builder->build($scene->getData($delimiter, $delimiter)));
	testRoutine($builder, $scene->getData($delimiter, $delimiter), 'e3');
}


function coreTest()
{
	$hierarchyMember = 'position';

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


	$delimitedData = (new DelimitedScene)->getData();
	$defaultData = (new DefaultScene)->getData();

	// --------------------------------------------
	// test various builds and scenes
	// --------------------------------------------
	/**/

	// basic fixed-length scene
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, 3), $defaultData, 'e1');

	// basic delimited scene
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, '.'), $delimitedData, 'e2');

	// custom indices
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, '.', 'id'), $delimitedData, 'e3');
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, 3, function($hierarchy, $node) {
		return $node->id;
	}), $defaultData, 'e3');
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, 3, function($hierarchy) {
		return $hierarchy . 'foobar';
	}), $defaultData, 'e1_foo');

	// custom hierarchy callback
	testRoutine(new MaterializedPathTreeBuilder(function($data) use ($hierarchyMember) {
		return $data->$hierarchyMember;
	}, '.'), $delimitedData, 'e2');

	// custom delimiter processor
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, function($hierarchy) {
		return substr($hierarchy, 0, -3) !== FALSE ? substr($hierarchy, 0, -3) : NULL;
	}), $defaultData, 'e1');
	testRoutine(new MaterializedPathTreeBuilder($hierarchyMember, function($hierarchy) {
		return substr($hierarchy, 0, strrpos($hierarchy, '.')) !== FALSE ? substr($hierarchy, 0, strrpos($hierarchy, '.')) : NULL;
	}), $delimitedData, 'e2');
}


function testBuilderWithEncoder(MaterializedPathTreeBuilder $builder, $encoder)
{

	// multiple root problem - colliding roots
	// - the behaviour is the same as any other duplicit hierarchy node - tha data is overwritten
	// - do not rely on this behaviour as it may change without notice
	collidingRootsSubroutine($builder, new CollidingRootsScene(), $encoder);
	// no root - no problem
	noRootSubroutine($builder, $encoder);

	// test missing reference behaviour
	missingRefSubroutine($builder, $encoder);

	// bridging
	bridgingSubroutine($builder, $encoder);


	// hierarchy cutoff
	cutoffSubroutine0($builder, $encoder);
	cutoffSubroutine1($builder, $encoder);
	cutoffSubroutine2($builder, $encoder);
}


function testRoutine(MaterializedPathTreeBuilder $builder, $data, $encoder)
{
	$root = $builder->build($data);

	Assert::equal(FALSE, $root->getChild(NULL));
	$encoder = __NAMESPACE__ . '\\' . $encoder;
	Assert::same('hello', $root->getChild($encoder('001'))->title);
	Assert::same('world', $root->getChild($encoder('002'))->title);
	Assert::same('world second child', $root->getChild($encoder('002'))->getChild($encoder('002002'))->title);
	Assert::same('world\'s furthest leaf', $root->getChild($encoder('002'))->getChild($encoder('002002'))->getChild($encoder('002002002'))->getChild($encoder('002002002001'))->title);
	Assert::same('a lonely foo', $root->getChild($encoder('003'))->title);
	Assert::same(3, count($root->getChild($encoder('002'))->getChild($encoder('002002'))->getChildren()));
}


// return position string as is
function e1($position)
{
	return $position;
}


// return position string as is
function e1_foo($position)
{
	return $position . 'foobar';
}


// convert position string to "."-delimited string with numeric positions
function e2($position)
{
	$pcs = str_split($position, 3);
	array_walk($pcs, function(&$item) {
		$item = (int) $item;
	});
//	dump(implode('.', $pcs));
	return implode('.', $pcs);
}


// convert position string to ID
function e3($position)
{
	$pcs = str_split($position, 3);
	$acc = 0;
	$order = count($pcs);
	array_walk($pcs, function(&$item, $index) use (&$acc, $order) {
		$item = (int) $item;
		$acc += $item * pow(10, ($order - $index - 1));
	});
	return $acc;
}


function collidingRootsSubroutine(MaterializedPathTreeBuilder $builder, CollidingRootsScene $scene, $encoder)
{
	$root = $builder->build($scene->getData());
	$encoder = __NAMESPACE__ . '\\' . $encoder;
	Assert::same('root2', $root->title);
	Assert::same(2, count($root->getChildren()));
	Assert::same('hello', $root->getChild($encoder('001'))->title);
	Assert::same(FALSE, $root->getChild($encoder('002'))); // Note: the return value can change to NULL
	Assert::same('lonely', $root->getChild($encoder('003'))->title);
	Assert::same('hello child', $root->getChild($encoder('001'))->getChild($encoder('001001'))->title);
}


function noRootSubroutine(MaterializedPathTreeBuilder $builder, $encoder)
{
	$root = $builder->build((new UndefinedRootScene())->getData());
	$encoder = __NAMESPACE__ . '\\' . $encoder;
	Assert::same(NULL, $root->getContents());
	Assert::same(2, count($root->getChildren()));
	Assert::same('hello', $root->getChild($encoder('001'))->title);
	Assert::same('world', $root->getChild($encoder('002'))->title);
}


function missingRefSubroutine(MaterializedPathTreeBuilder $builder, $encoder)
{
	// when any part is missing, the path to the root is bridged with empty stub nodes
	$data = (new MissingRefScene())->getData();

	$root = $builder->build($data);
	$encoder = __NAMESPACE__ . '\\' . $encoder;

	Assert::same(3, count($root->getChildren()));
	Assert::same('hello', $root->getChild($encoder('001'))->title);
	Assert::same('lonely', $root->getChild($encoder('003'))->title);

	Assert::same('world', $root->getChild($encoder('001'))->getChild($encoder('001001'))->title);
	Assert::same(NULL, $root->getChild($encoder('001'))->getChild($encoder('001001'))->getChild($encoder('001001001'))->getContents()); // stub node
	Assert::same('foo', $root->getChild($encoder('001'))->getChild($encoder('001001'))->getChild($encoder('001001001'))->getChild($encoder('001001001001'))->title);
	Assert::same(FALSE, $root->getChild($encoder(2))); // Note: the return value can change to NULL
	// whole branch bridged
	Assert::same(NULL, $root->getChild($encoder('002'))->getContents());
	Assert::same(NULL, $root->getChild($encoder('002'))->getChild($encoder('002002'))->getContents());
	Assert::same(NULL, $root->getChild($encoder('002'))->getChild($encoder('002002'))->getChild($encoder('002002002'))->getContents());
	Assert::same('world\'s furthest leaf', $root->getChild($encoder('002'))->getChild($encoder('002002'))->getChild($encoder('002002002'))->getChild($encoder('002002002001'))->title);
}


function bridgingSubroutine(MaterializedPathTreeBuilder $builder, $encoder)
{
	// when reference to parent is missing, the whole branch is lost
	$data = (new BridgingScene())->getData();

	// same testing routine as for the DefaultScene
	testRoutine($builder, $data, $encoder);
}


function cutoffSubroutine0(MaterializedPathTreeBuilder $builder, $encoder)
{
	$builderWithCutoff = clone $builder;
	$builderWithCutoff->setHierarchy(function($data) use ($builderWithCutoff) {
		$position = $builderWithCutoff->getMember($data, 'position');
		if (substr($position, 0, 3) === '007') {
			return substr($position, 3);
		}
		throw new \RuntimeException('FOO!');
	});
	$encoder = __NAMESPACE__ . '\\' . $encoder;

	$data = (new CutoffScene())->getData();
	$data[] = ['id' => 3, 'position' => '003', 'title' => 'stray dog'];

	// must throw exception - prefix 007 not present in "stray dog" node
	Assert::error(function()use($builderWithCutoff, $data) {
		$builderWithCutoff->build($data);
	}, 'RuntimeException' /* RuntimeException::CLASS */, NULL, 3);
}


function cutoffSubroutine1(MaterializedPathTreeBuilder $builder, $encoder)
{
	$encoder = __NAMESPACE__ . '\\' . $encoder;
	/* @var $root Node */
	/* @var $rootCut Node */
	/* @var $node Node */
	/* @var $nodeCut Node */
	list($root, $node, $rootCut, $nodeCut) = cutoffDataSubroutine($builder, new CutoffScene());

	// without cutoff
	Assert::same(4, $node->getLevel()); // no cut-off happened, stub root inserted
	Assert::same(NULL, $root->getContents());
	Assert::same(NULL, $root->getChild($encoder('007'))->getContents());

	// test cutoff
	Assert::same(3, $nodeCut->getLevel());
	Assert::same(NULL, $rootCut->getContents());
	Assert::equal(FALSE, $rootCut->getChild($encoder('007')));
	Assert::equal(2, count($rootCut->getChildren()));
}


function cutoffSubroutine2(MaterializedPathTreeBuilder $builder, $encoder)
{
	$encoder = __NAMESPACE__ . '\\' . $encoder;
	/* @var $root Node */
	/* @var $rootCut Node */
	/* @var $node Node */
	/* @var $nodeCut Node */
	list($root, $node, $rootCut, $nodeCut) = cutoffDataSubroutine($builder, new CutoffScene2());

	// without cutoff
	Assert::same(4, $node->getLevel()); // no cut-off happened, stub root inserted
	Assert::same(NULL, $root->getContents());
	Assert::same('root', $root->getChild($encoder('007'))->title);

	// test cutoff
	Assert::same(3, $nodeCut->getLevel());
	Assert::same('root', $rootCut->title);
	Assert::equal(FALSE, $rootCut->getChild($encoder('007')));
	Assert::equal(2, count($rootCut->getChildren()));
}


function cutoffDataSubroutine(MaterializedPathTreeBuilder $builder, Scene $scene)
{
	$data = $scene->getData();
	$root = $builder->build($data);

	$it = new TreeSimpleFilterIterator(new TreeIterator($root), 'position', '007002002001');
	$it->rewind();
	/* @var $node Node */
	$node = $it->current();

	$builderWithCutoff = clone $builder;
	$builderWithCutoff->setHierarchy(function($data) use ($builderWithCutoff) {
		$position = $builderWithCutoff->getMember($data, 'position');
		if (substr($position, 0, 3) === '007') {
			return substr($position, 3);
		}
		throw new \RuntimeException('FOO!');
	});
	$rootCut = $builderWithCutoff->build($data);

	$it2 = new TreeSimpleFilterIterator(new TreeIterator($rootCut), 'position', '007002002001');
	$it2->rewind();
	/* @var $nodeCut Node */
	$nodeCut = $it2->current();

	return [$root, $node, $rootCut, $nodeCut];
}
