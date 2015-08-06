<?php


namespace Oliva\Utils\Tree\Builder;

use Oliva\Utils\Tree\Node\INode;


/**
 * Materialized path tree builder.
 * Builds data from a linear data structure.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class PathTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	/**
	 * The Node's member carrying the hierarchy information.
	 * @var string
	 */
	public $hierarchyMember = 'position';

	/**
	 * The number of characters per each level of tree.
	 * @var int
	 */
	public $charsPerLevel = 3;


	public function __construct($hierarchyMember = 'position', $charsPerLevel = 3)
	{
		$this->hierarchyMember = $hierarchyMember;
		$this->charsPerLevel = $charsPerLevel;
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
		$this->checkData($data);

		$hierarchyMember = $this->hierarchyMember;
		$charsPerLevel = $this->charsPerLevel;
		$usePositionAsIndex = TRUE;

		$root = $this->createNode();
		$nodes = array();
		foreach ($data as $item) {
			$itemPosition = $this->getMember($item, $hierarchyMember);

			if ($itemPosition === NULL || strlen($itemPosition) < $this->charsPerLevel) {
				// root node found
				$newRoot = $this->createNode($item);
				$this->copyNodesRelationsAndReplace($root, $newRoot);
				$root = $newRoot;
				continue;
			} elseif (!isset($nodes[$itemPosition])) {
				// insert the node into the check table
				$nodes[$itemPosition] = $node = $this->createNode($item);
			} else {
				// the node has been inserted before - due to data failure or gap bridging
				// replace the node, copy node relations and continue
				$oldNode = $nodes[$itemPosition];
				$nodes[$itemPosition] = $this->createNode($item);
				$this->copyNodesRelationsAndReplace($oldNode, $nodes[$itemPosition]);
				continue;
			}

			$parentPos = substr($itemPosition, 0, -$charsPerLevel);
			if (isset($nodes[$parentPos])) {
				// parent has already been processed, link the child
				$nodes[$parentPos]->addChild($node, $usePositionAsIndex ? $itemPosition : NULL);
			} else {
				// bridge the gap between the current node and the nearest parent
				$pos = $parentPos;
				$childPosition = $itemPosition;
				$childNode = $node;
				while (strlen($pos) >= $charsPerLevel && !isset($nodes[$pos])) {
					$nodes[$pos] = $newNode = $this->createNode();
					$newNode->addChild($childNode, $usePositionAsIndex ? $childPosition : NULL);
					$childNode = $newNode;
					$childPosition = $pos;
					$pos = substr($pos, 0, -$charsPerLevel);
				}
				if (!isset($nodes[$pos])) {
					$root->addChild($childNode, $usePositionAsIndex ? $childPosition : NULL);
				} else {
					$nodes[$pos]->addChild($childNode, $usePositionAsIndex ? $childPosition : NULL);
				}
			}
		}
		return $root;
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


	public function hierarchyMemberToLevel($member)
	{
		return strlen($member) / $this->charsPerLevel;
	}

}
