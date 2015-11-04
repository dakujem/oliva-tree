<?php


namespace Oliva\Utils\Tree\Comparator;

use Oliva\Utils\Tree\Node\IDataNode;


/**
 * NodeComparator.
 *
 * Compare nodes data-wise.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class NodeComparator implements INodeComparator
{
	const STRICT_ALL = 0b1111;
	const STRICT_NONE = 0;
	const STRICT_INDICES = 0b1000;
	const STRICT_OBJECTS = 0b0100;
	const STRICT_ARRAYS = 0b0010;
	const STRICT_SCALARS = 0b0001;

	/** @var bool $recursive perform recursive comparison, compare children */
	protected $recursive = TRUE;

	/** @var int $strictness set up which data types are compared to equality and which to identity */
	protected $strictness = 0b1011; // self::STRICT_INDICES | self::STRICT_SCALARS | self::STRICT_ARRAYS; // [PHP 5.6]

	/** @var bool $compareChildIndices compare indices of children? */
	protected $compareChildIndices = TRUE;

	/** @var bool $compareNodeTypes compare the types of the nodes? */
	protected $compareNodeTypes = FALSE;


	/**
	 * @param bool|NULL $recursive [default TRUE] compare the children. When set to FALSE, only compares the current node's data.
	 * @param  int|NULL $strictness [default STRICT_INDICES | STRICT_SCALARS | STRICT_ARRAYS]
	 *                              flag to check strictly (use the identity === operator) for a certain types of node data
	 *                              STRICT_INDICES - use === for indices (only when $compareChildIndices equals TRUE)
	 *                              STRICT_OBJECTS - use identity check === for objects
	 *                              STRICT_ARRAYS - use identity check === for arrays
	 *                              STRICT_SCALARS - use identity check === for scalars and NULL
	 *                              use bitwise OR operator "|" to set multiple types of strictness.
	 * 								By default, objects are only compared to equality.
	 * @param bool|NULL $compareChildIndices [default TRUE] compare the children indices as well, only usable with $recursive equal TRUE
	 * @param bool|NULL $compareNodeTypes [default FALSE] compare the node's class names with get_class() function
	 */
	public function __construct($recursive = NULL, $strictness = NULL, $compareChildIndices = NULL, $compareNodeTypes = NULL)
	{
		if ($recursive !== NULL) {
			$this->recursive = (bool) $recursive;
		}
		if ($strictness !== NULL) {
			$this->strictness = (int) $strictness;
		}
		if ($compareChildIndices !== NULL) {
			$this->compareChildIndices = (bool) $compareChildIndices;
		}
		if ($compareNodeTypes !== NULL) {
			$this->compareNodeTypes = (bool) $compareNodeTypes;
		}
	}


	/**
	 * Compare two nodes. The result is dependent on the current setup (see the constructor).
	 * By default, all nodes are compared recursively, indices of children are compared strictly,
	 * all data types except objects are compared strictly (===), objects are checked loosly (==)
	 * and the classes of the nodes are not compared.
	 *
	 *
	 * @param IDataNode $node1
	 * @param IDataNode $node2
	 * @return bool
	 */
	public function compare(IDataNode $node1, IDataNode $node2)
	{
		return $this->compareNodes(
						$node1, //
						$node2, //
						$this->getRecursionSetup(), //
						$this->getStrictnessSetup(), //
						$this->getCompareChildIndicesSetup(), //
						$this->getCompareNodeTypesSetup(), //
						[$this, 'compareData'] // the internal callback used for comparison
		);
	}


	/**
	 * Compare two nodes using your provided callback for data comparison.
	 * The result is dependent on the current setup (see the constructor) and the callback.
	 * By default, all nodes are compared recursively, indices of children are compared strictly
	 * and the classes of the nodes are not compared.
	 *
	 * The callback receives the node's data as the first two parameters and the strictness setup as the third one.
	 *
	 *
	 * @param IDataNode $node1
	 * @param IDataNode $node2
	 * @param callable $callback
	 * @return bool
	 */
	public function callbackCompare(IDataNode $node1, IDataNode $node2, callable $callback)
	{
		return $this->compareNodes(
						$node1, //
						$node2, //
						$this->getRecursionSetup(), //
						$this->getStrictnessSetup(), //
						$this->getCompareChildIndicesSetup(), //
						$this->getCompareNodeTypesSetup(), //
						$callback // externally provided callback
		);
	}


	/**
	 * Perform the node comparison.
	 *
	 * @internal
	 */
	protected function compareNodes(IDataNode $node1, IDataNode $node2, $recursive, $strictness, $compareChildIndices, $compareNodeTypes, $compareFunction)
	{
		$same = !$compareNodeTypes || get_class($node1) === get_class($node2);
		if ($same && $recursive) {
			$childrenOfNode1 = $node1->getChildren();
			$childrenOfNode2 = $node2->getChildren();
			$same = count($childrenOfNode1) === count($childrenOfNode2);
			while ($same && ($keyPair1 = each($childrenOfNode1)) && ($keyPair2 = each($childrenOfNode2))) {
				$same = $same &&
						(!$compareChildIndices || ($strictness & self::STRICT_INDICES ? $keyPair1[0] === $keyPair2[0] : $keyPair1[0] == $keyPair2[0])) &&
						$this->compareNodes($keyPair1[1], $keyPair2[1], $recursive, $strictness, $compareChildIndices, $compareNodeTypes, $compareFunction) // recursion
				;
			}
		}
		return $same && $compareFunction($node1->getContents(), $node2->getContents(), $strictness);
	}


	/**
	 * Compare data od two nodes.
	 *
	 * With strictness setup it is possible to select whether the equality == or identity === operator is used for certain data types.
	 *
	 *
	 * @param mixed $dataOfNode1
	 * @param mixed $dataOfNode2
	 * @param int $strictness
	 * @return boolean the comparison result
	 */
	protected function compareData($dataOfNode1, $dataOfNode2, $strictness)
	{
		if (!$this->checkStrictly($strictness, $dataOfNode1) && !$this->checkStrictly($strictness, $dataOfNode2)) {
			// do not check strictly for the data types, allow implicit conversion
			return $dataOfNode1 == $dataOfNode2;
		}
		// do a strict identity check
		return $dataOfNode1 === $dataOfNode2;
	}


	/**
	 * Determine, whether to check strictly for the type of given $var according to $strictness setup.
	 *
	 *
	 * @param int $strictness
	 * @param mixed $var
	 * @return bool
	 */
	protected function checkStrictly($strictness, $var)
	{
		if (is_object($var)) {
			return (bool) ($strictness & self::STRICT_OBJECTS);
		}
		if (is_array($var)) {
			return (bool) ($strictness & self::STRICT_ARRAYS);
		}
		return (bool) ($strictness & self::STRICT_SCALARS);
	}


	public function getRecursionSetup()
	{
		return $this->recursive;
	}


	public function getStrictnessSetup()
	{
		return $this->strictness;
	}


	public function getCompareChildIndicesSetup()
	{
		return $this->compareChildIndices;
	}


	public function getCompareNodeTypesSetup()
	{
		return $this->compareNodeTypes;
	}

}
