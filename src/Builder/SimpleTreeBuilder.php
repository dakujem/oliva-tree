<?php


namespace Oliva\Utils\Tree\Builder;

use Traversable,
	RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * Simple tree builder. Builds trees from data that is already structured.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class SimpleTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	/**
	 * The default children member.
	 * @var string
	 */
	public static $childrenMemberDefault = 'children';

	/**
	 * The Node's member containing children.
	 *
	 * @var string
	 */
	public $childrenMember;


	public function __construct($childrenMember = NULL)
	{
		$this->childrenMember = $childrenMember != NULL ? $childrenMember : self::$childrenMemberDefault; // intentionally !=
	}


	/**
	 * Create an instance of INode from data that is already in a tree structure.
	 * 
	 *
	 * @param array|object $rootNodeData the root node data containing children (if any)
	 * @return INode
	 */
	public function build($rootNodeData)
	{
		return $this->buildNode($rootNodeData);
	}


	/**
	 * Create an instance of INode from data that is already in a tree structure.
	 * 
	 *
	 * @param array|object $nodeData node data containing children (if any)
	 * @return INode
	 */
	public function buildNode($nodeData)
	{
		$this->checkData($nodeData);
		$childrenMember = $this->childrenMember;

		// get children data - if present
		try {
			$childrenDataItems = $this->getMember($nodeData, $childrenMember);
			if (is_object($nodeData)) {
				unset($nodeData->$childrenMember);
			} elseif (is_array($nodeData)) {
				unset($nodeData[$childrenMember]);
			}
		} catch (RuntimeException $e) {
			$childrenDataItems = NULL;
		}

		// create the node
		$node = $this->createNode($nodeData);

		// if children data is present, process children and add them to the freshly created node
		if (is_array($childrenDataItems) || $childrenDataItems instanceof Traversable) {
			foreach ($childrenDataItems as $index => $childDataItem) {
				$child = $this->build($childDataItem); // recursion
				$node->addChild($child, $index);
			}
		}

		// return the node
		return $node;
	}


	/**
	 * Check the input data for correct type.
	 *
	 *
	 * @param array|object $root
	 * @throws RuntimeException
	 */
	protected function checkData($root)
	{
		if (is_scalar($root)) {
			throw new RuntimeException('The data must be an array or an object containing the root data and children data (if any), ' . gettype($root) . ' provided.');
		}
	}

}
