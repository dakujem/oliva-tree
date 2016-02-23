<?php


namespace Oliva\Utils\Tree\Node;

use Traversable;


/**
 * The base INode implementation trait for tree structures.
 * Includes all necessary tree nodes handling.
 *
 * Use in a class to implement the INode interface and provide som other convenient methods.
 * @see INode
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait BaseNodeTrait
{
	protected $children = [];
	protected $parent = NULL;


	/**
	 * TRUE for a root node (when no parent has been set).
	 *
	 *
	 * @return bool
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
		if ($this->getParent() !== NULL) {
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
	 * @return INode[]|FALSE returns siblings in an array or FALSE if no parent is present.
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
	 * Note:	Calling $node->setParent($parent) DOES NOT ADD the node to its parent's children array!
	 * 			To do that, call $parent->addChild($node) instead.
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
	 * Returns the node's direct parent (direct ancestor).
	 *
	 *
	 * @return INode|NULL
	 */
	public function getParent()
	{
		return $this->parent;
	}


	/**
	 * Returns the node's n-th ancestor.
	 * Returns NULL when the ancestor does not exist.
	 *
	 * Note:
	 *	- the 1-st ancestor is the direct parent
	 *	- the 0-th ancestor is self
	 *
	 *
	 * @return INode|NULL
	 */
	public function getNthAncestor($n)
	{
		$index = (int) $n;
		if ($index === 0) {
			return $this;
		} elseif ($index === 1) {
			return $this->getParent();
		}
		$ancestors = $this->getAncestors();
		return isset($ancestors[$index - 1]) ? $ancestors[$index - 1] : NULL;
	}


	/**
	 * Return all the ancestors from the direct parent node to the root node.
	 *
	 *
	 * @return INode[]
	 */
	public function getAncestors()
	{
		$ancestors = [];
		$current = $this;
		while (($parent = $current->getParent()) instanceof INode) {
			$ancestors[] = $parent;
			$current = $parent;
		}
		return $ancestors;
	}


	/**
	 * Returns the root connected to the node.
	 * When the node itself is a root, it is returned.
	 *
	 *
	 * @return INode
	 */
	public function getRoot()
	{
		if ($this->isRoot()) {
			return $this;
		}
		$parents = $this->getAncestors();
		return end($parents); // $parents cannot be empty
	}


	/**
	 * Return the depth of the node, e.g. the distance to the root.
	 *
	 *
	 * @return int
	 */
	public function getDepth()
	{
		return count($this->getAncestors());
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
	 * Get a single child (direct descendant).
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
	 * @return INode[]
	 */
	public function getChildren()
	{
		return $this->children;
	}


	/**
	 * Return the indices of all children.
	 *
	 *
	 * @return array
	 */
	public function getChildrenIndices()
	{
		return array_keys($this->children);
	}


	/**
	 * Get a distant descendant by a vector.
	 * Each element of the vector will be used to get the descendant of the current descendant,
	 * creating a chain of getChild() calls.
	 *
	 * Calling
	 * $node->getDescendant([1,2,3]);
	 * equals to a chained call
	 * $node->getChild(1)->getChild(2)->getChild(3);
	 * provided all the children exist under the indices in the tree structure.
	 *
	 * Note: this method returns self when $indexVector is empty!
	 *
	 *
	 * @param array $indexVector array of indices
	 * @return INode|NULL returns NULL when the node is not found
	 */
	public function getDescendant(array $indexVector)
	{
		$descendant = $this;
		while (!empty($indexVector) && $descendant instanceof INode) {
			$index = array_shift($indexVector);
			$descendant = $descendant->getChild($index);
		}
		return $descendant instanceof INode ? $descendant : NULL;
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
	 * Note: using keys can overwrite any previously added nodes with the same indices.
	 *
	 *
	 * @param array|Traversable $children an array or a traversable object.
	 * @param bool $useKeys = FALSE indicate whether the $children keys are supposed to be used as indices
	 * @return NodeBase fluent
	 */
	public function addChildren($children, $useKeys = FALSE)
	{
		foreach ($children as $key => $child) {
			$this->addChild($child, $useKeys ? $key : NULL);
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
		$node->setParent($this);
		if ($index === NULL) {
			$this->children[] = $node;
		} else {
			$this->children[$index] = $node;
		}
		return $node;
	}


	/**
	 * Remove a child node by index or by an INode instance.
	 *
	 * Note:	this method does not tell whether the action was successful or not.
	 * 			To be sure, use methods getChild() or getChildIndex() before or after the call.
	 *
	 *
	 * @param int|string|INode $index an array index or an instance of INode
	 * @return NodeBase
	 */
	public function removeChild($index)
	{
		if ($index instanceof INode) {
			$index = $this->getChildIndex($index);
			if ($index === FALSE) {
				return $this;
			}
		}
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
