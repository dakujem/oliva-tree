<?php


namespace Oliva\Utils\Tree\Iterator;

use FilterIterator;
use Nette\MemberAccessException;


/**
 * TreeFilterIterator.
 *
 * The $filteringConditions array contains key/value pairs each node is compared against to be accepted.
 * Either AND or OR matching system can be used for condfitions.
 *
 * 
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class TreeFilterIterator extends FilterIterator
{
	const MODE_AND = 'and';
	const MODE_OR = 'or';

	private $filteringParams = NULL;
	private $mode = self::MODE_AND;


	public function __construct(TreeIterator $iterator, array $filteringConditions, $mode)
	{
		parent::__construct($iterator);
		$this->filteringParams = $filteringConditions;
		$this->mode = $mode === self::MODE_OR ? self::MODE_OR : self::MODE_AND;
	}


	public function accept()
	{
		if (empty($this->filteringParams)) {
			return TRUE;
		}
		$node = $this->getInnerIterator()->current();
		$accept = $this->mode === self::MODE_AND ? TRUE : FALSE;
		foreach ($this->filteringParams as $param => $expectedValue) {
			try {
				$comparisonResult = $node->$param === $expectedValue;
			} catch (MemberAccessException $e) {
				$comparisonResult = FALSE;
			}
			$accept = $this->mode === self::MODE_AND ? $accept && $comparisonResult : $accept || $comparisonResult;
			if ($this->mode === self::MODE_OR && $accept) {
				break;
			}
		}
		return $accept;
	}

}
