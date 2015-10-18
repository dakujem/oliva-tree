<?php


namespace Oliva\Utils\Tree\Node;

use IteratorAggregate;
use Oliva\Utils\Tree\Iterator\TreeIterator;


/**
 * The base INode implementation for tree structures.
 * Includes all necessary tree nodes handling.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
abstract class NodeBase implements INode, IteratorAggregate
{
	protected $children = [];
	protected $parent = NULL;


	/**
	 * TRUE for a root node (when no parent has been set).
	 *
	 * 
	 * @return type
	 */
	public function isRoot()
	{
		return !$this->getParent();
	}


	/**
	 * TRUE for a leaf node (a node with no children).
	 *
	 *
	 * @return boolean
	 */
	public function isLeaf()
	{
		return $this->getChildrenCount() === 0;
	}


	/**
	 * TRUE when the node is first of the siblings.
	 *
	 *
	 * @return boolean
	 */
	public function isFirst()
	{
		$parent = $this->getParent();
		if ($parent !== NULL) {
			$siblings = $this->getSiblings(FALSE);
			return $this === array_shift($siblings);
		}
		return FALSE;
	}


	/**
	 * TRUE when the node is last of the siblings.
	 *
	 *
	 * @return boolean
	 */
	public function isLast()
	{
		$parent = $this->getParent();
		if ($parent !== NULL) {
			$siblings = $this->getSiblings(FALSE);
			return $this === array_pop($siblings);
		}
		return FALSE;
	}


	/**
	 * Returns all the node's siblings.
	 * Excludes this node by default.
	 *
	 * 
	 * @param bool $excludeThisNode = TRUE (default) this node will be ignored in sibling nodes
	 * @return array|FALSE returns siblings in an array or FALSE if no parent is present.
	 */
	public function getSiblings($excludeThisNode = TRUE)
	{
		$parent = $this->getParent();
		if ($parent !== NULL) {
			$children = $parent->getChildren();
			if ($excludeThisNode) {
				unset($children[$parent->getChildIndex($this)]);
			}
			return $children;
		}
		return FALSE;
	}


	/**
	 * Set the node's direct parent.
	 *
	 *
	 * @param INode $node
	 * @return NodeBase fluent
	 */
	public function setParent(INode $node = NULL)
	{
		$this->parent = $node;
		return $this;
	}


	/**
	 * Returns the node's direct parent.
	 *
	 *
	 * @return type
	 */
	public function getParent()
	{
		return $this->parent;
	}


	/**
	 * Return all the parents from the direct parent node to the root node.
	 *
	 *
	 * @return type
	 */
	public function getParents()
	{
		$parent = $this->getParent();
		if ($parent !== NULL) {
			return array_merge([$parent], $parent->getParents());
		} else {
			return [];
		}
	}


	/**
	 * Return all the parents from the root node to the direct parent of the node.
	 *
	 *
	 * @return type
	 */
	public function getPath()
	{
		return array_reverse($this->getParents());
	}


	/**
	 * Return the level of the node, e.g. the distance to the root, the depth.
	 * Alias of getDepth().
	 *
	 *
	 * @return int
	 */
	public function getLevel()
	{
		return $this->getDepth();
	}


	/**
	 * Return the depth of the node, e.g. the distance to the root.
	 *
	 *
	 * @return int
	 */
	public function getDepth()
	{
		return count($this->getParents());
	}


	/**
	 * Detach the node from the tree structure by unlinking the node from its parent.
	 * The node becomes a root node.
	 *
	 *
	 * @return NodeBase fluent
	 */
	public function detach()
	{
		$parent = $this->getParent();
		if ($parent !== NULL) {
			$parent->removeChild($parent->getChildIndex($this));
			$this->setParent(NULL);
		}
		return $this;
	}


	/**
	 * Get a single child.
	 *
	 * 
	 * @param scalar $index
	 * @return INode|FALSE returns FALSE when there is no child node under the index
	 */
	public function getChild($index)
	{
		if (!isset($this->children[$index])) {
			return FALSE;
		}
		return $this->children[$index];
	}


	/**
	 * Returns the index of the child within the children array.
	 *
	 *
	 * @param INode $node
	 * @return int|FALSE returns FALSE if the node is not a child of this node.
	 */
	public function getChildIndex(INode $node, $strict = TRUE)
	{
		return array_search($node, $this->children, $strict);
	}


	/**
	 * Returns all the node's children.
	 *
	 *
	 * @return array
	 */
	public function getChildren()
	{
		return $this->children;
	}


	/**
	 * Return child node iterator.
	 * By default iterates only on direct descendants (due to the IteratorAggregate interface).
	 *
	 * CAN also ITERATE through all the descendant nodes RECURSIVELY in two modes.
	 *
	 * @see TreeIterator for recursion modes
	 *
	 * 
	 * @param string|NULL $recursion
	 * @return TreeIterator
	 */
	public function getIterator($recursion = TreeIterator::NON_RECURSIVE)
	{
		return new TreeIterator($this, $recursion);
	}


	/**
	 * @return int
	 */
	public function getChildrenCount()
	{
		return count($this->children);
	}


	/**
	 * Replace the entire children array with a new one.
	 * 
	 *
	 * @param array $children
	 * @return NodeBase fluent
	 */
	public function addChildren(array $children)
	{
		foreach ($children as $child) {
			$this->addChild($child);
		}
		return $this;
	}


	/**
	 * Appends a child into the children array.
	 * If an index is specified, it is possible to override an existing child node with the same index!
	 *
	 * This node becomes the direct parent of the inserted child node.
	 *
	 * Note: adding to NULL index is only possible by adding to '' (empty string) index.
	 *
	 *
	 * @param INode $node
	 * @param scalar $index = NULL index
	 * @return NodeBase the new/inserted node
	 */
	public function addChild(INode $node, $index = NULL)
	{
		if (!$node instanceof static) {
			$node = new static($node);
		}
		$node->setParent($this);
		if ($index === NULL) {
			$this->children[] = $node;
		} else {
			$this->children[$index] = $node;
		}
		return $node;
	}


	/**
	 * Remove a child node by index.
	 * 
	 *
	 * @param type $index
	 * @return NodeBase
	 */
	public function removeChild($index)
	{
		if (isset($this->children[$index])) {
			unset($this->children[$index]);
		}
		return $this;
	}


	/**
	 * Removes all the node's children.
	 *
	 *
	 * @return NodeBase fluent
	 */
	public function removeChildren()
	{
		$this->children = [];
		return $this;
	}

}
