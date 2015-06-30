<?php


namespace Oliva\Utils\Tree\Node;


/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface INode
{


	/**
	 * Set the node's direct parent.
	 *
	 *
	 * @param \Oliva\Tree\INode $node
	 * @return \Oliva\Tree\INode fluent
	 */
	public function setParent(self $node = NULL);


	/**
	 * Returns the node's direct parent.
	 *
	 *
	 * @return \Oliva\Tree\INode
	 */
	public function getParent();


	/**
	 * Get a single child.
	 *
	 *
	 * @param scalar $index
	 * @return \Oliva\Tree\INode
	 */
	public function getChild($index);


	/**
	 * Returns the index of the child within the children array.
	 *
	 *
	 * @param \Oliva\Tree\INode $node
	 * @return int|FALSE returns FALSE if the node is not a child of this node.
	 */
	public function getChildIndex(self $node);


	/**
	 * Returns all the node's children.
	 *
	 *
	 * @return array
	 */
	public function getChildren();


	/**
	 * Appends a child into the children array.
	 * If an index is specified, it is possible to override an existing child node with the same index!
	 *
	 * This node becomes the direct parent of the inserted child node.
	 *
	 *
	 * @param \Oliva\Tree\INode $node
	 * @param scalar $index = NULL index
	 * @return \Oliva\Tree\INode the new/inserted node
	 */
	public function addChild(self $node, $index = NULL);


	/**
	 * Remove a child node by index.
	 *
	 *
	 * @param type $index
	 * @return \Oliva\Tree\INode
	 */
	public function removeChild($index);


	/**
	 * Removes all the node's children.
	 *
	 *
	 * @return \Oliva\Tree\INode fluent
	 */
	public function removeChildren();

}
