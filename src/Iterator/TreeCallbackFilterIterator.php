<?php


namespace Oliva\Utils\Tree\Iterator;

use Nette\Utils\Callback;
use FilterIterator;


/**
 * TreeCallbackFilterIterator.
 * 
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class TreeCallbackFilterIterator extends FilterIterator
{
	private $filteringCallback = NULL;
	private $params = NULL;


	public function __construct(TreeIterator $iterator, callable $filteringCallback, ...$params)
	{
		parent::__construct($iterator);
		$this->filteringCallback = $filteringCallback;
		$this->params = $params;
	}


	public function accept()
	{
		return Callback::invokeArgs(
						$this->filteringCallback, //
						array_merge([$this->getInnerIterator()->current(), $this->getInnerIterator()->key()], !empty($this->params) ? $this->params : []) //
		);
	}

}
