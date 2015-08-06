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
	 * @var array|NULL [callable, params]
	 */
	protected $nodeCallback = NULL;

	/**
	 * @var array|NULL [callable, params]
	 */
	protected $dataErrorCallback = NULL;


	/**
	 * Register a callback for node creation.
	 * The first parameter of the callback is the data for the node.
	 * More parameters can be specified as arguments passed to this method's call.
	 *
	 * It is required, that the callback returns an instance of INode.
	 *
	  $builder->setNodeCallback(function($data = NULL) {
		return new Node($data);
	  });
	 *
	  $builder->setNodeCallback(function($data, $customParam1, $customParam2) use($builder) {  ...  }, $customParam1_value, $customParam2_value);
	 *
	 *
	 * @param callable $function
	 * @return self fluent
	 */
	public function setNodeCallback(callable $function = NULL, ...$params)
	{
		if ($function !== NULL) {
			$this->nodeCallback = [$function, $params];
		} else {
			$this->nodeCallback = NULL;
		}
		return $this;
	}


	/**
	 * Register a callback to handle data errors while building the tree.
	 * The first parameter of the callback is the data item for the node.
	 * The second parameter is the exception causing the tree building error.
	 * More parameters can be specified as arguments passed to this method's call.
	 *
	  $builder->setDataErrorCallback(function($item, $exception) { ... });
	 *
	  $builder->setDataErrorCallback(function($item, $exception, ...$customParams) use($builder) {  ...  }, $customParam1_value, $customParam2_value);
	 *
	 *
	 * @param callable $function
	 * @return self fluent
	 */
	public function setDataErrorCallback(callable $function = NULL, ...$params)
	{
		if ($function !== NULL) {
			$this->dataErrorCallback = [$function, $params];
		} else {
			$this->dataErrorCallback = NULL;
		}
		return $this;
	}


	/**
	 * Creates a node. If arguments are provided, they are passed to the constructor.
	 *
	 *
	 * @return INode
	 */
	protected function createNode($data = NULL)
	{
		if ($this->nodeCallback !== NULL) {
			return call_user_func_array($this->nodeCallback[0], array_merge([$data], $this->nodeCallback[1]));
		}
		return new $this->nodeClass($data);
	}


	/**
	 * You can throw an exception here or whatever...
	 *
	 *
	 * @return void
	 */
	protected function dataError($item, $exception = NULL)
	{
		if ($this->dataErrorCallback !== NULL) {
			return call_user_func_array($this->dataErrorCallback[0], array_merge([$item, $exception], $this->dataErrorCallback[1]));
		}
	}


	/**
	 * Check the input data for correct type.
	 *
	 *
	 * @param Traversable|array $data
	 * @throws RuntimeException
	 */
	protected function checkData($data)
	{
		if (!is_array($data) && !$data instanceof Traversable) {
			throw new RuntimeException('The data provided must be an array or must be traversable, ' . (is_object($data) ? 'an instance of ' . get_class($data) . ' provided' : gettype($data) . '') . '.');
		}
	}

}
