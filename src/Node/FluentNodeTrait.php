<?php


namespace Oliva\Utils\Tree\Node;


/**
 * A trait that provides means for fluent tree building.
 *
 * This trait is intended to be used with INode implementations only!
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait FluentNodeTrait
{


	/**
	 * An alternative to addChild() method, provides a fluent interface for tree building,
	 * it will automatically create an instance of static and pass the data to the constructor
	 * and return the new node.
	 * Note the difference between addNode and addLeaf methods.
	 *
	 * This approach is useful for fluent tree building, like this:
	 *
	 * $root->addNode('1')
	 *           ->addNode('1.1')
	 *               ->addLeaf('1.1.1')
	 *               ->addLeaf('1.1.2')
	 *               ->getParent()
	 *           ->addLeaf('1.2')
	 *           ->addNode('1.3')
	 *               ->addLeaf('1.3.1')
	 *               ->addLeaf('1.3.2')
	 *               ->getParent()
	 *           ->getParent()
	 *      ->addLeaf('2');
	 *
	 *
	 * @param mixed $data any data that can be passed to the node's constructor
	 * @param int|string $index any node's index that can be used as array index
	 * @return INode the newly created node (!)
	 */
	public function addNode($data, $index = NULL)
	{
		return $this->addChild(new static($data), $index);
	}


	/**
	 * An alternative to addChild() method, provides a fluent interface for tree building,
	 * it will automatically create an instance of static and pass the data to the constructor
	 * and return the current node.
	 * Note the difference between addNode and addLeaf methods.
	 *
	 * This approach is useful for fluent tree building, like this:
	 *
	 * $root->addNode('1')
	 *           ->addNode('1.1')
	 *               ->addLeaf('1.1.1')
	 *               ->addLeaf('1.1.2')
	 *               ->getParent()
	 *           ->addLeaf('1.2')
	 *           ->addNode('1.3')
	 *               ->addLeaf('1.3.1')
	 *               ->addLeaf('1.3.2')
	 *               ->getParent()
	 *           ->getParent()
	 *      ->addLeaf('2');
	 *
	 *
	 * @param mixed $data any data that can be passed to the node's constructor
	 * @param int|string $index any node's index that can be used as array index
	 * @return INode the current node (!)
	 */
	public function addLeaf($data, $index = NULL)
	{
		$this->addChild(new static($data), $index);
		return $this;
	}

}
