<?php


namespace Oliva\Utils\Tree\Builder;

use RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * Recursive tree builder.
 * Builds data from a linear data structure.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class RecursiveTreeBuilder extends TreeBuilder implements ITreeBuilder
{
	const DEFAULT_ID_MEMBER = 'id';
	const DEFAULT_PARENT_MEMBER = 'parent';

	/**
	 * The Node's member carrying its ID.
	 * @var string
	 */
	protected $idMember = self::DEFAULT_ID_MEMBER;

	/**
	 * The Node's member carrying parent's ID.
	 * @var string
	 */
	protected $parentMember = self::DEFAULT_PARENT_MEMBER;

	/**
	 * Implicit root IDs.
	 * @var array
	 */
	protected $implicitRoots = [0, '0', '',];


	public function __construct($parentMember = self::DEFAULT_PARENT_MEMBER, $idMember = self::DEFAULT_ID_MEMBER)
	{
		$this->parentMember = $parentMember;
		$this->idMember = $idMember;
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
				$parentNode->addChild($node);
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
