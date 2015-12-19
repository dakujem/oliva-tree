<?php


namespace Oliva\Utils\Tree\Builder;

use RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * Path tree builder.
 *
 * @deprecated use directly MaterializedPathTreeBuilder instead. This class is only provided for backward compatibility.
 *
 *
 *
 * Builds data from a linear data structure, where each node holds its
 * position within the tree in its hierarchy member.
 * Fixed number of characters is used for each level.
 * A node with NULL position (or position shorter than needed for first level)
 * can be provided and will be set as the root.
 *
 * NOTE:	This is a special case of the Materialized Path Tree data model,
 * 			where the positions are presented in fixed length per level and
 * 			with decimal numbers used (no encoding).
 *
 * Example:
 * With 3 characters per level, node with position "002001" is the first
 * child of the second child of the root with position "002". The root has NULL position.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class PathTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	/**
	 * The default number of characters per each level of the tree.
	 * @var int
	 */
	public static $charsPerLevelDefault = 3;

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

	/**
	 * The number of characters per each level of the tree.
	 * @var int
	 */
	public $charsPerLevel;

	/**
	 * Sort the nodes by hierarchy member automatically?
	 * With this option set to FALSE the children nodes will be left in the original order within its parent.
	 * @var bool
	 */
	public $autoSort;

	/**
	 * This string will stop gap-bridging when it reaches the cut-off value.
	 * It is useful when building sub-trees not starting from the root.
	 * When not empty, it has to be the prefix of all nodes' hierarchy members.
	 * @var string
	 */
	public $hierarchyCutoff;


	public function __construct($hierarchyMember = NULL, $charsPerLevel = NULL, $autoSort = TRUE, $hierarchyCutoff = '')
	{
		parent::__construct();
		$this->hierarchyMember = $hierarchyMember !== NULL ? $hierarchyMember : self::$hierarchyMemberDefault;
		$this->charsPerLevel = $charsPerLevel > 0 ? $charsPerLevel : self::$charsPerLevelDefault;
		$this->autoSort = !!$autoSort;
		$this->hierarchyCutoff = (string) $hierarchyCutoff;
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
		$nodes = [];
		foreach ($data as $item) {
			$rawPosition = $this->getMember($item, $hierarchyMember);
			$cutoff = substr($rawPosition, 0, strlen($this->hierarchyCutoff));
			if (!empty($this->hierarchyCutoff) && $cutoff !== $this->hierarchyCutoff) {
				throw new RuntimeException(sprintf('Item hierarchy member "%s" does not start with the set hierarchy cut-off string "%s".', $rawPosition, $this->hierarchyCutoff), 3);
			}
			$itemPosition = substr($rawPosition, strlen($this->hierarchyCutoff));

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
				$nodes[$parentPos]->addChild($node, $usePositionAsIndex ? $cutoff . $itemPosition : NULL);
			} else {
				// bridge the gap between the current node and the nearest parent
				$pos = $parentPos;
				$childPosition = $itemPosition;
				$childNode = $node;
				while (strlen($pos) >= $charsPerLevel && strlen($pos) >= strlen($cutoff) && !isset($nodes[$pos])) {
					$nodes[$pos] = $newNode = $this->createNode();
					$newNode->addChild($childNode, $usePositionAsIndex ? $cutoff . $childPosition : NULL);
					$childNode = $newNode;
					$childPosition = $pos;
					$pos = substr($pos, 0, -$charsPerLevel);
				}
				if (!isset($nodes[$pos])) {
					$root->addChild($childNode, $usePositionAsIndex ? $cutoff . $childPosition : NULL);
				} else {
					$nodes[$pos]->addChild($childNode, $usePositionAsIndex ? $cutoff . $childPosition : NULL);
				}
			}
		}
		$this->autoSort && $this->sortChildren($root);
		return $root;
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


	public function hierarchyMemberToLevel($member)
	{
		return strlen($member) / $this->charsPerLevel;
	}

}
