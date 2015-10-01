<?php


namespace Oliva\Utils\Tree\Builder;

use RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * Recursive tree builder.
 * Builds data from a linear data structure, where each node holds an ID
 * of its parent.
 *
 * Example:
 * Node(id:1,parent:2) is a child of node(id:2,parent:3), which is a child
 * of node(id:3,parent:NULL), which is the root node.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class RecursiveTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	/**
	 * The default id member.
	 * @var string
	 */
	public static $idMemberDefault = 'id';

	/**
	 * The default parent member.
	 * @var string
	 */
	public static $parentMemberDefault = 'parent';

	/**
	 * The Node's member carrying its ID.
	 * @var string
	 */
	public $idMember;

	/**
	 * The Node's member carrying parent's ID.
	 * @var string
	 */
	public $parentMember;

	/**
	 * Implicit root IDs.
	 * NULL ID is always considered a root.
	 * @var array
	 */
	public $implicitRoots = [0, '0', '',];


	public function __construct($parentMember = NULL, $idMember = NULL)
	{
		$this->parentMember = $parentMember != NULL ? $parentMember : self::$parentMemberDefault; // intentionally !=
		$this->idMember = $idMember != NULL ? $idMember : self::$idMemberDefault; // intentionally !=
	}


	/**
	 * Build the tree from linear data.
	 * The node with no (NULL) parent is considered to be the root. If node points to self, it is considered the root as well.
	 * If no root is found, a node with ID in $implicitRoots will be sought and used as the root if found (default 0, "0", "").
	 * Do not provide multiple trees and multiple roots, as the behaviour is not defined - the trees will overwrite one onother.
	 *
	 *
	 * @param array|Traversable $data traversable data containing node data items
	 * @return INode the root node
	 */
	public function build($data)
	{
		$this->checkData($data);
		$useIdAsIndex = TRUE;

		$parentMember = $this->parentMember;
		$idMember = $this->idMember;
		$nodes = array();
		$rootId = NULL;
		$rootFound = FALSE;
		foreach ($data as $item) {
			$id = $this->getMember($item, $idMember);
			$parent = $this->getMember($item, $parentMember);

			if ($parent === NULL || $id === $parent) {
				$rootId = $id;
				$parent = NULL;
				$rootFound = TRUE;
			}

			$node = $this->createNode($item);
			if (isset($nodes[$id])) {
				// a stub node has been inserted before - need to replace it
				/* @var $stub INode */
				$stub = $nodes[$id];
				foreach ($stub->getChildren() as $index => $child) {
					$node->addChild($child, $index);
				}
				$node->setParent($stub->getParent());
			}
			// insert the node into the check table
			$nodes[$id] = $node;

			if ($parent !== NULL) {
				if (!isset($nodes[$parent])) {
					// insert a stub node parent into the check table
					$nodes[$parent] = $parentNode = $this->createNode();
				} else {
					// the node has been inserted before
					$parentNode = $nodes[$parent];
				}
				$parentNode->addChild($node, $useIdAsIndex ? $id : NULL);
			}
		}
		if ($rootFound) {
			$root = $nodes[$rootId];
		} else {
			$root = NULL;
			foreach ($this->implicitRoots as $irid) {
				if (isset($nodes[$irid])) {
					$root = $nodes[$irid];
					break;
				}
			}
		}
		if ($root !== NULL) {
			return $root;
		}
		throw new RuntimeException('No root node present.');
	}

}
