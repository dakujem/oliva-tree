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
	 * Transforms linear data into a tree structure.
	 * The root node contains no data.
	 *
	 * Note: duplicity in '$hierarchyMember' member of items is not detected and may result in unexpected behaviour.
	 *
	 * @param array $data an array of objects
	 * @param type $hierarchyMember
	 * @param type $charsPerLevel
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
//			if (is_object($item) && property_exists($item, $hierarchyMember)) {
			//TODO overit, ci property_exists na dynamickych properties funguje (napr. lean mapper entity), overit ci $itm->non_existent_property hodi notice, ci sa to da odchytit
			if (is_object($item)) {
				try {
					$itemPosition = $item->$hierarchyMember;
				} catch (Exception $e) {
					$this->dataError($item, $e);
					continue;
				}
			} elseif (is_array($item) && key_exists($hierarchyMember, $item)) {
				$itemPosition = $item[$hierarchyMember];
			} else {
				$this->dataError($item);
				continue;
			}

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


	protected function hierarchyMemberTolevel($member)
	{
		return strlen($member) / $this->charsPerLevel;
	}

}
