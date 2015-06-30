<?php


namespace Oliva\Utils\Tree\Iterator;


use Iterator;
use Nette\InvalidStateException;
use Oliva\Utils\Tree\Node\INode;

/**
 * TreeIterator.
 *
 * Can be non-recursive or recursive.
 *
 * Recursive breadth-first mode iterates on the current level first,
 * then iterates through children of the first node, then second and so on.
 * http://en.wikipedia.org/wiki/Breadth-first_search
 *
 * Recursive depth-first mode iterates to the furthest leaves first then continues to the next furthest node and so on.
 * http://en.wikipedia.org/wiki/Depth-first_search
 *
 * 
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class TreeIterator implements Iterator
{
	const NON_RECURSIVE = NULL;
	const DEPTH_FIRST_RECURSION = 'df';
	const BREADTH_FIRST_RECURSION = 'bf';

	/**
	 * The root node.
	 * @var INode
	 */
	private $root;

	/**
	 * Recursion mode.
	 */
	private $recursion;

	/**
	 * Hybrid queue.
	 * @var array
	 */
	private $queue = [];

	/**
	 * Initialization flag.
	 * @var bool
	 */
	private $ready = FALSE;


	public function __construct(INode $root, $mode = self::BREADTH_FIRST_RECURSION)
	{
		$this->setRecursion($mode);
		$this->setRoot($root);
	}


	/**
	 * @return INode
	 */
	public function current()
	{
		$this->init();
		if (empty($this->queue)) {
			return NULL;
		}
		list($index, $node) = reset($this->queue);
		return $node;
	}


	public function next()
	{
		$this->init();
		if (!empty($this->queue)) {
			list($index, $node) = array_shift($this->queue);
			if ($this->recursion !== self::NON_RECURSIVE) {
				// adds children to queue according to recursion mode - to the front for DF, at the end for BF
				$this->enqueue($node->getChildren());
			}
		}
	}


	public function key()
	{
		$this->init();
		if (empty($this->queue)) {
			return NULL;
		}
		list($index, $node) = reset($this->queue);
		return $index;
	}


	public function valid()
	{
		$this->init();
		return !empty($this->queue);
	}


	public function rewind()
	{
		$this->queue = [];
		$this->ready = FALSE;
	}


//	-------------- INTERNAL -----------------
//	-----------------------------------------


	protected function setRoot(INode $root)
	{
		$this->root = $root;
		return $this;
	}


	protected function setRecursion($mode = self::BREADTH_FIRST_RECURSION)
	{
		if (
				$mode !== self::NON_RECURSIVE &&
				$mode !== self::DEPTH_FIRST_RECURSION &&
				$mode !== self::BREADTH_FIRST_RECURSION
		) {
			throw new InvalidStateException('Invalid recursion mode.');
		}
		$this->recursion = $mode;
		return $this;
	}


	private function init()
	{
		if (!$this->ready) {
			$this->enqueue($this->root->getChildren());
			$this->ready = TRUE;
		}
	}


	/**
	 * Adds nodes to internal queue.
	 * In case the queue is empty, no recursion mode (during init) or BF mode is active, the nodes appended to the end.
	 * If DF mode is active, the nodes are prepended to the beginning.
	 *
	 * @param array $nodes
	 */
	private function enqueue(array $nodes)
	{
		if ($this->recursion !== self::DEPTH_FIRST_RECURSION || empty($this->queue)) {
			foreach ($nodes as $index => $node) {
				$this->queue[] = [$index, $node];
			}
		} else {
			foreach (array_reverse($nodes, TRUE) as $index => $node) {
				array_unshift($this->queue, [$index, $node]);
			}
		}
	}

}
