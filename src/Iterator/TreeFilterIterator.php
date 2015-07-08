<?php


namespace Oliva\Utils\Tree\Iterator;

use Exception,
	FilterIterator;


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

	protected $filteringConditions = NULL;
	protected $outerConditionMode = self::MODE_AND;
	protected $innerConditionMode = self::MODE_OR;


	public function __construct(TreeIterator $iterator, array $filteringConditions, $outerConditionMode = self::MODE_AND, $innerConditionMode = self::MODE_OR)
	{
		parent::__construct($iterator);
		$this->filteringConditions = $filteringConditions;
		$this->outerConditionMode = $outerConditionMode === self::MODE_OR ? self::MODE_OR : self::MODE_AND;
		$this->innerConditionMode = $innerConditionMode === self::MODE_AND ? self::MODE_AND : self::MODE_OR;
	}


	public function accept()
	{
		if (empty($this->filteringConditions)) {
			return TRUE;
		}
		$node = $this->getInnerIterator()->current();
		$accept = $this->outerConditionMode === self::MODE_AND ? TRUE : FALSE;


		//TODO dorobit inner mode - moznost definovat viac hodnot pre jeden kluc
		foreach ($this->filteringConditions as $param => $expectedValue) {
			try {
				$comparisonResult = $node->$param === $expectedValue;
			} catch (Exception $e) {
				$comparisonResult = FALSE;
			}
			$accept = $this->outerConditionMode === self::MODE_AND ? $accept && $comparisonResult : $accept || $comparisonResult;
			if ($this->outerConditionMode === self::MODE_OR && $accept) {
				break;
			}
		}


		return $accept;
	}


	protected function walkConditions(array $conditions, $mode)
	{

	}

}
