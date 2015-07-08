<?php


namespace Oliva\Utils\Tree\Iterator;

use FilterIterator;


/**
 * TreeCallbackFilterIterator.
 * 
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class TreeCallbackFilterIterator extends FilterIterator
{
	protected $filteringCallback = NULL;
	protected $callbackParams = NULL;


	public function __construct(TreeIterator $iterator, callable $filteringCallback, ...$params)
	{
		parent::__construct($iterator);
		$this->filteringCallback = $filteringCallback;
		$this->callbackParams = $params;
	}


	public function accept()
	{
		return call_user_func_array(
				$this->filteringCallback, //
				array_merge([$this->getInnerIterator()->current(), $this->getInnerIterator()->key()], !empty($this->callbackParams) ? $this->callbackParams : []) //
		);
	}

}
