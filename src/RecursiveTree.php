<?php


namespace Oliva\Utils\Tree;

use RuntimeException;
use Oliva\Utils\Tree\Node\Node;


/**
 * RecursiveTree.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class RecursiveTree extends Tree
{
	/**
	 * The Node's member carrying its ID.
	 * @var string
	 */
	public $idMember = 'id';

	/**
	 * The Node's member carrying parent's ID.
	 * @var string
	 */
	public $parentMember = 'parent';


	public function __construct(array $data, $parentMember = 'parent', $idMember = 'id')
	{
		$this->parentMember = $parentMember;
		$this->idMember = $idMember;
		$root = self::transform($data, $this->parentMember, $this->idMember);
		parent::__construct($root);
	}


	/**
	 * Transforms linear data into a tree structure.
	 * The root node contains no data.
	 *
	 * @param array $data an array of objects
	 * @param string $parentMember
	 * @param string $idMember
	 */
	public static function transform(array $data, $parentMember = 'parent', $idMember = 'id')
	{
		$nodes = array();
		$rootId = NULL;
		$rootFound = FALSE;
		foreach ($data as $item) {
			if (is_object($item) && property_exists($item, $idMember) && property_exists($item, $parentMember)) {
				$id = $item->$idMember;
				$parent = $item->$parentMember;
			} elseif (is_array($item) && key_exists($idMember, $item) && key_exists($parentMember, $item)) {
				$id = $item[$idMember];
				$parent = $item[$parentMember];
			} else {
				// data error - skip
				continue;
			}
			if ($id === $parent) {
				$rootId = $id;
				$rootFound = TRUE;
			}

			if (!isset($nodes[$id])) {
				// insert the node into the check table
				$nodes[$id] = $node = new Node($item);
			} else {
				// the node has been inserted before
				$node = $nodes[$id]->setObject($item);
			}
			if (!isset($nodes[$parent])) {
				// insert the node into the check table
				$nodes[$parent] = $parentNode = new Node();
			} else {
				// the node has been inserted before
				$parentNode = $nodes[$parent];
			}

			if ($id === $parent) {
				$rootId = $id;
				$rootFound = TRUE;
			} else {
				$parentNode->addChild($node);
			}
		}
		if ($rootFound) {
			$root = $nodes[$rootId];
		} elseif (isset($nodes[0])) {
			$root = $nodes[0];
		} elseif (isset($nodes['0'])) {
			$root = $nodes['0'];
		} elseif (isset($nodes[''])) {
			$root = $nodes[''];
		} elseif (isset($nodes[NULL])) {
			$root = $nodes[NULL];
		} else {
			throw new RuntimeException('No root node present.');
		}
		return $root;
	}

}
