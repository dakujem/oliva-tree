<?php


namespace Oliva\Utils\Tree\Builder;

use Traversable,
	RuntimeException;
use Oliva\Utils\Tree\Node\Node,
	Oliva\Utils\Tree\Node\INode;


/**
 * Recursive tree builder.
 *
 *
 * @todo:
 * 	- callbacks for node creation and data errors
 *
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
abstract class TreeBuilder
{
	/**
	 * Node class. An instance will be created upon transformation for each node.
	 * @var string
	 */
	protected $nodeClass = Node::CLASS;


	/**
	 * You can throw an exception here or whatever...
	 * 
	 * @return void
	 */
	protected function dataError($item, $exception = NULL)
	{

	}


	/**
	 * Creates a node. If arguments are provided, they are passed to the constructor.
	 *
	 *
	 * @return INode
	 */
	protected function createNode(...$params)
	{
		return new $this->nodeClass(...$params);
	}


	protected function checkData($data)
	{
		if (!is_array($data) && !$data instanceof Traversable) {
			throw new RuntimeException('The data provided must be an array or must be traversable, ' . (is_object($data) ? 'an instance of ' . get_class($data) . ' provided' : gettype($data) . '') . '.');
		}
	}

}
