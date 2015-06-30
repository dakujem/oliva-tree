<?php


namespace Oliva\Utils\Tree\Iterator;

use FilterIterator;
use Nette\MemberAccessException;


/**
 * TreeSimpleFilterIterator.
 *
 * 
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class TreeSimpleFilterIterator extends FilterIterator
{
	private $key;
	private $value;


	public function __construct(TreeIterator $iterator, $key, $value)
	{
		parent::__construct($iterator);
		$this->key = $key;
		$this->value = $value;
	}


	public function accept()
	{
		$node = $this->getInnerIterator()->current();
		try {
			return $node->{$this->key} === $this->value;
		} catch (MemberAccessException $e) {
			return FALSE;
		}
	}

}
