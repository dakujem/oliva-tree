<?php


namespace Oliva\Utils\Tree;

use Exception,
	Iterator,
	IteratorAggregate;
use Nette\InvalidStateException;
use Oliva\Utils\Tree\Node\INode,
	Oliva\Utils\Tree\Node\NodeBase;


/**
 * Tree.
 *
 * 
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class Tree implements ITree, \IteratorAggregate
{
	/**
	 *
	 * @var INode
	 */
	protected $root;


	public function __construct(INode $root = NULL)
	{
		if ($root !== NULL) {
			$this->setRoot($root);
		}
	}


	/**
	 * Set the root node.
	 * 
	 *
	 * @param \Oliva\Tree\INode $root
	 * @return \Oliva\Tree\Tree
	 */
	public function setRoot(INode $root)
	{
		$this->root = $root;
		return $this;
	}


	/**
	 * Return the root node.
	 *
	 *
	 * @return \Oliva\Tree\INode
	 */
	public function getRoot()
	{
		return $this->root;
	}


	/**
	 * Returns RECURSIVE tree node iterator. Can also iterate non-recursively.
	 *
	 * @see TreeIterator for iteration modes
	 * 
	 *
	 * @param string|NULL $recursion
	 * @return \Traversable
	 */
	public function getIterator($recursion = TreeIterator::BREADTH_FIRST_RECURSION)
	{
		if ($this->root instanceof NodeBase) {
			return $this->root->getIterator($recursion);
		} elseif ($this->root instanceof IteratorAggregate) {
			throw new Exception(' nie som si isty, ci je toto mozne podporovat... ');
			return $this->root->getIterator();
		} elseif ($this->root instanceof Iterator) {
			throw new Exception(' nie som si isty, ci je toto mozne podporovat... ');
			return $this->root;
		}
		throw new InvalidStateException('Cannot retrieve an iterator for the root node. To use this feature, the root node has to implement getIterator() method or be an iterator itself. This can be achieved by descending the node from NodeBase or implement IteratorAggregate or Iterator intefaces.');
	}


	/**
	 * Returns iterator with conditions to compare nodes against.
	 *
	 * $i = $tree->getFilterIterator(['id' => 5, 'position' => '002003001'], TreeFilterIterator::MODE_AND);
	 *
	 *
	 * @param array $conditions
	 * @param string $mode
	 * @param string|NULL $recursion
	 * @return \Oliva\Tree\TreeFilterIterator
	 */
	public function getFilterIterator(array $conditions, $mode = TreeFilterIterator::MODE_AND, $recursion = TreeIterator::BREADTH_FIRST_RECURSION)
	{
		return new TreeFilterIterator($this->getIterator($recursion), $conditions, $mode);
	}


	/**
	 * Return iterator with filtering callback.
	 * The first argument passed to the callback is the node, the second is the index.
	 *
	 * 	$i = $tree->getFilteringCallbackIterator(function(\Oliva\Tree\NodeBase $node, $index) {
	 * 		try {
	 * 			return $index === '002' || $node->id === 4;
	 * 		} catch (MemberAccessException $e) {
	 * 			return FALSE;
	 * 		}
	 * 	});
	 *
	 *
	 * @param \Oliva\Tree\callable $filteringCallback
	 * @param string|NULL $recursion
	 * @param ...
	 * @return \Oliva\Tree\TreeFilteringCallbackIterator
	 */
	public function getCallbackFilterIterator(callable $filteringCallback, $recursion = TreeIterator::BREADTH_FIRST_RECURSION, ...$params)
	{
		return new TreeCallbackFilterIterator($this->root->getIterator($recursion), $filteringCallback, ...$params);
	}


	/**
	 * Find a node by specific key/value pair.
	 *
	 * 
	 * @param scalar $key
	 * @param mixed $val
	 * @param string|NULL $recursion
	 * @return INode|NULL
	 */
	public function find($key, $val, $recursion = TreeIterator::BREADTH_FIRST_RECURSION)
	{
		foreach (new TreeSimpleFilterIterator($this->root->getIterator($recursion), $key, $val) as $node) {
			return $node;
		}
		return NULL;
	}


	/**
	 * Alias for getDepthFirst().
	 *
	 *
	 * @return array
	 */
	public function getLinear()
	{
		return $this->getBreadthFirst();
	}


	/**
	 * Returns an array of nodes in depth-first manner. Useful for grids, lists etc.
	 *
	 *
	 * @return array
	 */
	public function getDepthFirst()
	{
		return self::depthFirstTransform($this->root);
	}


	/**
	 * Returns an array of nodes in bredth-first manner.
	 *
	 *
	 * @return array
	 */
	public function getBreadthFirst()
	{
		return self::breadthFirstTransform($this->root);
	}


	/**
	 * Returns an array of nodes in depth-first manner. Useful for grids, lists etc.
	 *
	 *
	 * @return array
	 */
	public static function depthFirstTransform(INode $root)
	{
		return self::df($root);
	}


	/**
	 * Returns an array of nodes in bredth-first manner.
	 *
	 * 
	 * @return array
	 */
	public static function breadthFirstTransform(INode $root)
	{
		return array_merge(array($root), self::bf($root));
	}


	private static function df(INode $node)
	{
		$list = array($node);
		foreach ($node->getChildren() as $child) {
			$list = array_merge($list, self::df($child));
		}
		return $list;
	}


	private static function bf(INode $node)
	{
		$list = $node->getChildren();
		foreach ($list as $child) {
			$list = array_merge($list, self::bf($child));
		}
		return $list;
	}

}
