<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */

namespace Oliva\Test;

require_once __DIR__ . '/bootstrap.php';

use Tester,
	Tester\Assert;
use Oliva\Utils\Tree\Node\SimpleNode,
	Oliva\Utils\Tree\Node\NodeBase;


/**
 * @see \Oliva\Utils\Tree\Node\NodeBase
 */
class NodeBaseTest extends Tester\TestCase
{


	public function testChildren()
	{
		$root = new SimpleNode(0);

		$children = [
			new SimpleNode(10),
			new SimpleNode(20),
			new SimpleNode(30),
		];

		foreach ($children as $child) {
			$root->addChild($child);
		}
		Assert::same(3, $root->getChildrenCount());
		Assert::same($children, $root->getChildren());

		$childrenIndices = array_keys($children);
		foreach ($childrenIndices as $index) {
			$root->removeChild($index);
		}
		Assert::same(0, $root->getChildrenCount());
		Assert::same([], $root->getChildren());

		$root->addChildren($children);
		Assert::same(3, $root->getChildrenCount());

		$root->removeChildren();
		Assert::same(0, $root->getChildrenCount());
		Assert::same([], $root->getChildren());

		foreach (array_reverse($childrenIndices) as $index) {
			$root->addChild($children[$index], $index);
		}
		Assert::same(3, $root->getChildrenCount());

		foreach ($childrenIndices as $index) {
			Assert::same($children[$index], $root->getChild($index));
		}

		foreach ($childrenIndices as $index) {
			Assert::same($index, $root->getChildIndex($children[$index]));
		}

		$index = reset($childrenIndices);
		$node = $root->getChild($index);
		Assert::same($children[$index], $node);

		$siblingKeys = array_reverse($childrenIndices);
		$returnedSiblingsAll = $node->getSiblings(FALSE);
		Assert::same($siblingKeys, array_keys($returnedSiblingsAll));
		foreach ($returnedSiblingsAll as $key => $sibling) {
			Assert::same($children[$key], $sibling);
		}
		unset($siblingKeys[array_search($index, $siblingKeys)]);
		$returnedSiblings = $node->getSiblings(TRUE);
		Assert::same($siblingKeys, array_keys($returnedSiblings));
		foreach ($returnedSiblings as $key => $sibling) {
			Assert::same($children[$key], $sibling);
		}
	}


	public function testPosition()
	{
		$root = new SimpleNode(0);

		Assert::same(TRUE, $root->isRoot());
		Assert::same(TRUE, $root->isLeaf());

		$root->addChild(new SimpleNode(10), 2);

		Assert::same(TRUE, $root->isRoot());
		Assert::same(FALSE, $root->isLeaf());

		Assert::same(TRUE, $root->getChild(2)->isLast());
		Assert::same(TRUE, $root->getChild(2)->isFirst());

		$root->addChild(new SimpleNode(20), 1); // NOTE: the node is NOT added BEFORE the previous one, even though the index is smaller!
		Assert::same(FALSE, $root->getChild(2)->isLast());
		Assert::same(TRUE, $root->getChild(2)->isFirst());
		Assert::same(TRUE, $root->getChild(1)->isLast());
		Assert::same(FALSE, $root->getChild(1)->isFirst());

		$root->addChild(new SimpleNode(30), 3);
		Assert::same(TRUE, $root->getChild(3)->isLast());
		Assert::same(FALSE, $root->getChild(3)->isFirst());
		Assert::same(FALSE, $root->getChild(1)->isLast());
		Assert::same(FALSE, $root->getChild(1)->isFirst());
	}


	public function testTransitions()
	{
		$root = new SimpleNode(0);
		Assert::same(0, $root->getLevel());

		$indices = [1, 2, 3];
		$this->addChildren($root, $root->getLevel() + 1, $indices); // values 10, 20, 30
		$child = $root->getChild(2);
		$this->addChildren($child, $child->getLevel() + 1, $indices);  // values 100, 200, 300
		$grandchild = $child->getChild(3);
		$grandchildren = $child->getChildren();

		// The structure should now be like this:
		//   0
		//   |
		//   +-- 10
		//   |
		//   +-- 20
		//   |    |
		//   |    +-- 100
		//   |    |
		//   |    +-- 200
		//   |    |
		//   |    +-- 300
		//   |
		//   +-- 30

		Assert::same(1, $child->getLevel());
		Assert::same(2, $grandchild->getLevel());

		Assert::same(NULL, $root->getParent());
		Assert::same($root, $child->getParent());
		Assert::same($child, $grandchild->getParent());

		Assert::same([$child, $root], $grandchild->getParents()); // the order of expected parents is important
		Assert::same([$root], $child->getParents());


		// detach a node
		$child->detach();
		// Now the structure should now be like this:
		//   0
		//   |
		//   +-- 10
		//   |
		//   +-- 30
		//
		// And a detached node in $child
		//  20
		//   |
		//   +-- 100
		//   |
		//   +-- 200
		//   |
		//   +-- 300
		//
		Assert::same(FALSE, $root->getChild(2));
		Assert::same([], $child->getParents());
		Assert::same([$child], $grandchild->getParents());
		Assert::same(2, $root->getChildrenCount());
		Assert::same(3, $child->getChildrenCount());
		$vals = [];
		foreach ($root->getChildren() as $ch) {
			$vals[] = $ch->getValue();
		}
		Assert::same([10, 30], $vals);
		Assert::same($grandchildren, $child->getChildren());


		// attach node again to a different point in the tree
		$root->getChild(1)->addChild($child);
		// The structure should now be like this:
		//   0
		//   |
		//   +-- 10
		//   |    |
		//   |    +-- 20
		//   |         |
		//   |         +-- 100
		//   |         |
		//   |         +-- 200
		//   |         |
		//   |         +-- 300
		//   |
		//   +-- 30
		Assert::same([$child, $root->getChild(1), $root], $grandchild->getParents());
		Assert::same([$child], $root->getChild(1)->getChildren());

		$leaf = new SimpleNode(1000);
		$leaf->setParent($root); // NOTE: setParent does NOT set the parent-child relation, only sets the child-parent part. Use $parent->addChild($child) for this purpose.
		Assert::same($root, $leaf->getParent());
		Assert::same([$root], $leaf->getParents());
		$leaf->detach();
		Assert::same(NULL, $leaf->getParent());
		Assert::same([], $leaf->getParents());

		$grandchild->addChild($leaf);
		Assert::same($grandchild, $leaf->getParent());
		Assert::same([$grandchild, $child, $root->getChild(1), $root], $leaf->getParents());
		Assert::same([$root, $root->getChild(1), $child, $grandchild], $leaf->getPath());
	}


	protected function addChildren(NodeBase $node, $level, $countOrIndices = 3)
	{
		if (is_int($countOrIndices) && $countOrIndices > 0) {
			for ($i = 0; $i < $countOrIndices; $i++) {
				$node->addChild(new SimpleNode(($i + 1) * (10 ** $level)));
			}
		} elseif (is_array($countOrIndices)) {
			foreach ($countOrIndices as $index) {
				$node->addChild(new SimpleNode($index * (10 ** $level)), $index);
			}
		} else {
			throw new \LogicException;
		}
	}

}

// run the test
(new NodeBaseTest)->run();

