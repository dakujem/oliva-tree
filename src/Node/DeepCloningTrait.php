<?php


namespace Oliva\Utils\Tree\Node;


/**
 * A trait that "fixes" cloning of nodes by recursively making a deep copy of descendants.
 *
 * This trait is intended to be used with INode implementations only!
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait DeepCloningTrait
{


	/**
	 * Make a copy of the children to copy the whole original branch.
	 */
	public function __clone()
	{
		// make a deep copy of child nodes - copy the whole original branch
		// this prevents problems when altering a clonned node's children
		$children = $this->getChildren();
		$clones = [];
		foreach ($children as $key => $child) {
			$clones[$key] = clone $child;
		}
		// intentionally do not remove the child nodes here (not necessary)
		foreach ($clones as $key => $clone) {
			$this->addChild($clone, $key); // intentionally replace the children by their clones with the same index
		}
		// the parent member and instance remain untouched,
		// because even though this node points to the parent of the node is is a clone of (the original branch),
		// it is not ist child, and is not part of that original branch
	}

}
