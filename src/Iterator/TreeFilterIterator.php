<?php


namespace Oliva\Utils\Tree\Iterator;

use Exception,
	FilterIterator;


/**
 * TreeFilterIterator.
 *
 * The $filteringConditions array contains key/value pairs each node is compared against to be accepted.
 * If the value of the pair is an array, an inner iteration is applied using the values of the array.
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
		return $this->outerConditionsWalk($this->getInnerIterator()->current());
	}


	protected function outerConditionsWalk($node)
	{
		$mode = $this->outerConditionMode;
		$conditions = $this->filteringConditions;
		$accept = $mode === self::MODE_AND ? TRUE : FALSE;

		foreach ($conditions as $param => $expectedValue) {
			try {
				if (is_array($expectedValue)) {
					// inner walk
					$comparisonResult = $this->innerConditionsWalk($param, $expectedValue, $node);
				} else {
					$comparisonResult = $node->$param === $expectedValue;
				}
			} catch (Exception $e) {
				$comparisonResult = FALSE;
			}
			$accept = $this->aggregateAccept($mode, $accept, $comparisonResult);
			if ($mode === self::MODE_OR && $accept) {
				break;
			}
		}
		return $accept;
	}


	protected function innerConditionsWalk($param, array $conditions, $node)
	{
		$mode = $this->innerConditionMode;
		$accept = $mode === self::MODE_AND ? TRUE : FALSE;
		foreach ($conditions as $expectedValue) {
			try {
				$comparisonResult = $node->$param === $expectedValue;
			} catch (Exception $e) {
				$comparisonResult = FALSE;
			}
			$accept = $this->aggregateAccept($mode, $accept, $comparisonResult);
			if ($mode === self::MODE_OR && $accept) {
				break;
			}
		}
		return $accept;
	}


	protected function aggregateAccept($mode, $accept, $comparisonResult)
	{
		return $mode === self::MODE_AND ? $accept && $comparisonResult : $accept || $comparisonResult;
	}

}
