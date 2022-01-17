<?php


namespace Oliva\Utils\Tree\Iterator;

use InvalidArgumentException,
	Iterator;
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
	protected $root;

	/**
	 * Recursion mode.
	 */
	protected $recursion;

	/**
	 * Hybrid queue.
	 * @var array
	 */
	protected $queue = [];

	/**
	 * Initialization flag.
	 * @var bool
	 */
	protected $ready = FALSE;


	public function __construct(INode $root, $mode = self::BREADTH_FIRST_RECURSION)
	{
		$this->setRecursion($mode);
		$this->setRoot($root);
	}


	/**
	 * Return the recursion mode.
	 *
	 *
	 * @return mixed TreeIterator constants
	 */
	public function getRecursion()
	{
		return $this->recursion;
	}


	public function current(): ?INode
	{
		$this->init();
		if (empty($this->queue)) {
			return NULL;
		}
		list(, $node) = reset($this->queue);
		return $node;
	}


	public function next(): void
	{
		$this->init();
		if (!empty($this->queue)) {
			list(, $node) = array_shift($this->queue);
			if ($this->getRecursion() !== self::NON_RECURSIVE) {
				// adds children to queue according to recursion mode - to the front for DF, at the end for BF
				$this->enqueue($node->getChildren());
			}
		}
	}


    #[\ReturnTypeWillChange]
	public function key() // :mixed
	{
		$this->init();
		if (empty($this->queue)) {
			return NULL;
		}
		list($index, ) = reset($this->queue);
		return $index;
	}


	public function valid(): bool
	{
		$this->init();
		return !empty($this->queue);
	}


	public function rewind(): void
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


	/**
	 * Set recursion mode.
	 *
	 *
	 * @param string|NULL $mode
	 * @return TreeIterator fluent
	 * @throws InvalidArgumentException
	 */
	protected function setRecursion($mode = self::BREADTH_FIRST_RECURSION)
	{
		if (
				$mode !== self::NON_RECURSIVE &&
				$mode !== self::DEPTH_FIRST_RECURSION &&
				$mode !== self::BREADTH_FIRST_RECURSION
		) {
			throw new InvalidArgumentException('Invalid recursion mode.');
		}
		$this->recursion = $mode;
		return $this;
	}


	protected function init()
	{
		if (!$this->ready) {
			$this->enqueue($this->root->getChildren());
//			$this->enqueue([$this->root]); //TODO tu je zrejme chyba - otazne - mal by iterator iterovat aj cez root?
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
	protected function enqueue(array $nodes)
	{
		if ($this->getRecursion() !== self::DEPTH_FIRST_RECURSION || empty($this->queue)) {
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
