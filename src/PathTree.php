<?php


namespace Oliva\Utils\Tree;


/**
 * PathTree.
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class PathTree extends Tree
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


	public function __construct(array $data, $hierarchyMember = 'position', $charsPerLevel = 3)
	{
		$this->hierarchyMember = $hierarchyMember;
		$this->charsPerLevel = $charsPerLevel;
		$root = self::transform($data, $this->hierarchyMember, $this->charsPerLevel);
		parent::__construct($root);
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
	public static function transform(array $data, $hierarchyMember = 'position', $charsPerLevel = 3, $usePositionAsIndex = TRUE)
	{
		$root = new Node();
		$nodes = array();
		foreach ($data as $item) {
			if (is_object($item) && property_exists($item, $hierarchyMember)) {
				$itemPosition = $item->$hierarchyMember;
			} elseif (is_array($item) && key_exists($hierarchyMember, $item)) {
				$itemPosition = $item[$hierarchyMember];
			} else {
				// data error - skip
				continue;
			}

			if (!isset($nodes[$itemPosition])) {
				// insert the node into the check table
				$nodes[$itemPosition] = $node = new Node($item);
			} else {
				// the node has been inserted before - due to data failure or gap bridging
				// write data and continue
				$node = $nodes[$itemPosition]->setObject($item);
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
					$nodes[$pos] = $newNode = new Node;
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


	public static function hierarchyMemberTolevel($member, $charsPerLevel = 3)
	{
		return strlen($member) / $charsPerLevel;
	}

}
