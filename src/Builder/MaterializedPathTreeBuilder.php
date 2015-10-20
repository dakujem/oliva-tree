<?php


namespace Oliva\Utils\Tree\Builder;

use RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * Materialized path tree builder.
 * Builds data from a linear data structure, where each node holds its
 * position within the tree in its hierarchy member.
 *
 *
 * - explode the position to parents
 * - get a parent identification
 * - get a parent position
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class MaterializedPathTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	/**
	 * The default hierarchy member.
	 * @var string
	 */
	public static $hierarchyMemberDefault = 'position';

	/**
	 * The Node's member carrying the hierarchy information.
	 * @var string
	 */
	public $hierarchyMember;


	public function __construct($hierarchyMember = NULL)
	{
		$this->hierarchyMember = $hierarchyMember !== NULL ? $hierarchyMember : self::$hierarchyMemberDefault;
	}


	/**
	 * Build the tree from linear data.
	 * The root node contains no data and is always created, unless data item with NULL position is provided.
	 *
	 * Note: duplicity in position member of items is not detected and may result in unexpected behaviour.
	 *
	 *
	 * @param array|Traversable $data traversable data containing node data items
	 * @return INode the root node
	 */
	public function build($data)
	{
		// data check
		$this->checkData($data);

		/* @var $nodeCache INode[] cache of already processed nodes */
		$nodeCache = [];
		foreach ($data as $item) {

			// NOTE: I'm using the word "position" in meaning of "node's hierarchy member value"
			//TODO preprocess the hierarchy member
			$itemPosition = $this->getMember($item, $this->getHierarchyMemberName());

			if (!isset($nodeCache[$itemPosition])) {

				// create a new node and insert it into the node cache
				$nodeCache[$itemPosition] = $this->createNode($item);
				$currentNode = $nodeCache[$itemPosition];


				// get the parent's position
				$parentPos = $itemPosition !== NULL ? $this->getParentIdentification($itemPosition) : NULL;

				dump([$itemPosition, $parentPos]);


				dump(['parent of ' . $itemPosition, $parentPos, isset($nodeCache[$parentPos]) ? 'exists' : 'not found']);

				if ($itemPosition !== NULL && isset($nodeCache[$parentPos])) {

					// set parent or bridge the gap to the root
					// parent has already been processed, link the child
					$nodeCache[$parentPos]->addChild($currentNode, $this->createChildIndex($itemPosition, $parentPos, $currentNode));
				} elseif ($itemPosition !== NULL) {
					// Bridge the gap between the current node and the nearest parent:
					//
					// When processing a node that does not connect directly to an already processed node (in cache),
					// we need to create stub nodes (empty) and connect through them until we connect to a node in cache:
					// anyExistingNode -> stub -> stub -> ... -> currentNode
					// The bridging is terminated when it reaches the root level or a cached node.
					// If the data provided is consistent, the stub nodes will be replaced by well-formed nodes later on.
					$stubParentPos = $parentPos;
					$childPosition = $itemPosition;
					$childNode = $currentNode;
					do {
						$nodeCache[$stubParentPos] = $stubParent = $this->createNode();
						$stubParent->addChild($childNode, $this->createChildIndex($childPosition, $stubParentPos, $childNode));
						$childNode = $stubParent;
						$childPosition = $stubParentPos;
						$stubParentPos = $stubParentPos !== NULL ? $this->getParentIdentification($stubParentPos) : NULL;
					} while (!isset($nodeCache[$stubParentPos]));
					// connect to the existing node,
					// EXCEPT when the above cycle terminated due to reaching the root
					if ($stubParent !== $nodeCache[$stubParentPos]) {
						$nodeCache[$stubParentPos]->addChild($childNode, $this->createChildIndex($childPosition, $stubParentPos, $childNode));
					}
				} else {
					// root found, nothing to do here
					echo 'ROOT FOUND!';
//					dump($item);
				}
			} else {
				// the node already exists in the node cache (due to data failure or gap bridging)
				// replace the (stub) node and copy node relations
				$nodeCache[$itemPosition] = $this->replaceNode($nodeCache[$itemPosition], $item);
			}
		}

		// post process nodes
		//TODO sorting & more
//		$this->autoSort && $this->sortChildren($root);

		return isset($nodeCache[NULL]) ? $nodeCache[NULL] : NULL;
	}


	private function getParentIdentification($hierarchy)
	{
		return $this->getParentIdentification_delimited($hierarchy);
	}


	private function getParentIdentification_delimited($hierarchy)
	{
		$str = substr($hierarchy, 0, strrpos($hierarchy, '.'));
		if ($str === FALSE || strlen($str) == 0) {
			return NULL;
		}
		return $str;
	}


	private function getParentIdentification_fixed($hierarchy)
	{
		$str = substr($hierarchy, 0, -3);
		if ($str === FALSE || strlen($str) == 0) {
			return NULL;
		}
		return $str;
	}


	private function createChildIndex($hierarchy, $parentHierarchy, INode $node)
	{
//		return $node->id;
		$usePositionAsIndex = TRUE;
		$cutoff = '';
		return $usePositionAsIndex ? $cutoff . $hierarchy : NULL;
	}


	protected function getHierarchyMemberName()
	{
		return $this->hierarchyMember;
	}


	/**
	 * Sorts INode's children by key.
	 *
	 *
	 * @param INode $node the root node
	 * @return void
	 */
	protected function sortChildren(INode $node)
	{
		$children = $node->getChildren();
		$node->removeChildren();
		foreach ($this->sortNodes($children) as $index => $child) {
			$this->sortChildren($child); // recursion
			$node->addChild($child, $index);
		}
	}


	/**
	 * Sorts nodes by key using ksort().
	 *
	 *
	 * @param INode[] $nodes array of INode objects
	 * @return INode[] sorted array of INode objects
	 */
	protected function sortNodes(array $nodes)
	{
		ksort($nodes);
		return $nodes;
	}


	protected function copyNodesRelationsAndReplace(INode $source, INode $destination)
	{
		foreach ($source->getChildren() as $index => $child) {
			/* @var $child INode */
			$destination->addChild($child, $index);
		}
		$parent = $source->getParent();
		if ($parent instanceof INode && $parent !== $source) {
			$parent->addChild($destination, $parent->getChildIndex($source));
		}
	}


	protected function replaceNode(INode $source, $data = NULL)
	{
		$destination = $this->createNode($data);
		foreach ($source->getChildren() as $index => $child) {
			/* @var $child INode */
			$destination->addChild($child, $index);
		}
		$parent = $source->getParent();
		if ($parent instanceof INode && $parent !== $source) {
			$parent->addChild($destination, $parent->getChildIndex($source));
		}
		return $destination;
	}

}
