<?php


namespace Oliva\Utils\Tree\Node;

use IteratorAggregate;
use Oliva\Utils\Tree\Iterator\TreeIterator;
use Traversable;


/**
 * The base INode implementation for tree structures.
 * Includes all necessary tree nodes handling.
 *
 * Uses traits.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
abstract class NodeBase implements INode, IteratorAggregate
{

	use ComplexNodeTrait;


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
	public function getIterator($recursion = TreeIterator::NON_RECURSIVE): Traversable
	{
		return new TreeIterator($this, $recursion);
	}


	/**
	 * @deprecated this method's name may lead to misunderstnding that a node can have multiple parents (thus not be a tree node),
	 *             use getAncestors() instead
	 *
	 * Return all the parents from the direct parent node to the root node.
	 * Alias of getAncestors().
	 *
	 *
	 * @return INode[]
	 */
	public function getParents()
	{
		return $this->getAncestors();
	}


	/**
	 * @deprecated for no real use
	 *
	 * Return all the parents from the root node to the direct parent of the node.
	 *
	 *
	 * @return INode[]
	 */
	public function getPath()
	{
		return array_reverse($this->getAncestors());
	}


	/**
	 * @deprecated for no real use
	 *
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

}
