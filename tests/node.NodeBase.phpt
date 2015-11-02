<?php

/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */


namespace Oliva\Test\NodeBase;

require_once __DIR__ . '/bootstrap.php';

use ArrayIterator;
use Tester,
	Tester\Assert;
use Oliva\Utils\Tree\Node\SimpleNode,
	Oliva\Utils\Tree\Node\NodeBase,
	Oliva\Utils\Tree\Comparator\NodeComparator;


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
		Assert::same([0, 1, 2], $childrenIndices); // sanity check

		Assert::same($childrenIndices, $root->getChildrenIndices());
		Assert::same($children[0], $root->getChild($root->getChildrenIndices()[0]));

		$impostor = (new SimpleNode(10));
		$getChildIndexFalsy = FALSE; // if getChildIndex return on failure is changed to NULL, change here as well
		Assert::same($getChildIndexFalsy, $root->getChildIndex($impostor, FALSE));
		$impostor->setParent($root);
		Assert::same(0, $root->getChildIndex($impostor, FALSE));
		Assert::same($getChildIndexFalsy, $root->getChildIndex($impostor, TRUE));
		Assert::same($getChildIndexFalsy, $root->getChildIndex($impostor));

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

		$root->addChildren(new ArrayIterator($children));
		Assert::same($childrenIndices, $root->getChildrenIndices());

		foreach (array_reverse($children) as $child) {
			$root->removeChild($child);
		}
		Assert::same(0, $root->getChildrenCount());


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

		Assert::same([$child, $root], $grandchild->getAncestors()); // the order of expected parents is important
		Assert::same([$root], $child->getAncestors());


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
		Assert::same([], $child->getAncestors());
		Assert::same([$child], $grandchild->getAncestors());
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
		Assert::same([$child, $root->getChild(1), $root], $grandchild->getAncestors());
		Assert::same([$child], $root->getChild(1)->getChildren());

		$leaf = new SimpleNode(1000);
		$leaf->setParent($root); // NOTE: setParent does NOT set the parent-child relation, only sets the child-parent part. Use $parent->addChild($child) for this purpose.
		Assert::same($root, $leaf->getParent());
		Assert::same([$root], $leaf->getAncestors());
		$leaf->detach();
		Assert::same(NULL, $leaf->getParent());
		Assert::same([], $leaf->getAncestors());

		$grandchild->addChild($leaf);
		Assert::same($grandchild, $leaf->getParent());
		Assert::same([$grandchild, $child, $root->getChild(1), $root], $leaf->getAncestors());
		Assert::same([$root, $root->getChild(1), $child, $grandchild], $leaf->getPath());
	}


	public function testFluentInterface()
	{
		$root = new SimpleNode('0');

		$root
				->addNode('1')
				/**/->addNode('1.1')
				/*   */->addLeaf('1.1.1')
				/*   */->addLeaf('1.1.2')
				/*   */->getParent()
				/**/->addLeaf('1.2')
				/**/->addNode('1.3')
				/*   */->addLeaf('1.3.1')
				/*   */->addLeaf('1.3.2')
				/*   */->getParent()
				/**/->getParent()
				->addLeaf('2', 17);

		Assert::same('1', $root->getChild(0)->getContents());
		Assert::same('1.1.1', $root->getChild(0)->getChild(0)->getChild(0)->getContents());
		Assert::same('1.1.2', $root->getChild(0)->getChild(0)->getChild(1)->getContents());
		Assert::same('1.2', $root->getChild(0)->getChild(1)->getContents());
		Assert::same('1.3', $root->getChild(0)->getChild(2)->getContents());
		Assert::same('1.3.2', $root->getChild(0)->getChild(2)->getChild(1)->getContents());
		Assert::equal(FALSE, $root->getChild(1));
		Assert::equal('2', $root->getChild(17)->getContents());
	}


	public function testClonning()
	{
		$root = new SimpleNode('0');
		$root
				->addNode('1')
				->addLeaf('1.1')
				->getParent()
				->addLeaf('2')
		;
		$clonedRoot = clone $root;


		// Note:	normal == comparison cannot be used here for the PHP recursion limitations (or is it an implementation bug?)
		Assert::equal(FALSE, $clonedRoot === $root); // as expected of clonning, the nodes are not identical
//		Assert::equal(TRUE, $clonedRoot == $root); //   but are equal // Fatal Error Nesting level too deep - recursive dependency?
		// I can't check this, but let's continue
		/**/

		// however, by default, we need to clone the whole node structure (the branch), not the current node only!
		Assert::equal(FALSE, $root->getChild(0) === $clonedRoot->getChild(0)); // must not be identical!
		Assert::equal(FALSE, $root->getChild(0)->getChild(0) === $clonedRoot->getChild(0)->getChild(0)); // must not be identical!
		Assert::equal(FALSE, $root->getChild(1) === $clonedRoot->getChild(1)); // must not be identical!
		/**/

		// the data has to be identical
		$comparator = new NodeComparator(TRUE, NodeComparator::STRICT_ALL, TRUE, TRUE);
		Assert::equal(TRUE, $comparator->compare($root, $clonedRoot));
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


