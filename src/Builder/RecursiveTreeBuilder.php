<?php


namespace Oliva\Utils\Tree\Builder;

use Exception,
	RuntimeException;
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
	protected $implicitRoots = [0, '0', '', NULL,];


	public function __construct($parentMember = self::DEFAULT_PARENT_MEMBER, $idMember = self::DEFAULT_ID_MEMBER)
	{
		$this->parentMember = $parentMember;
		$this->idMember = $idMember;
	}


	/**
	 * Transforms linear data into a tree structure.
	 * The root node contains no data.
	 *
	 * @param array $data an array of objects
	 * @param string $parentMember
	 * @param string $idMember
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
//			if (is_object($item) && property_exists($item, $idMember) && property_exists($item, $parentMember)) {
			//TODO overit, ci property_exists na dynamickych properties funguje (napr. lean mapper entity), overit ci $itm->non_existent_property hodi notice, ci sa to da odchytit
			if (is_object($item)) {
				try {
					$id = $item->$idMember;
					$parent = $item->$parentMember;
				} catch (Exception $e) {
					$this->dataError($item, $e);
					continue;
				}
			} elseif (is_array($item) && key_exists($idMember, $item) && key_exists($parentMember, $item)) {
				$id = $item[$idMember];
				$parent = $item[$parentMember];
			} else {
				$this->dataError($item);
				continue;
			}
			if ($id === $parent) {
				$rootId = $id;
				$rootFound = TRUE;
			}

			//TODO - what happens if root points to self?  $root->id == $root->parent

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

			if (!isset($nodes[$parent])) {
				// insert a stub node parent into the check table
				$nodes[$parent] = $parentNode = $this->createNode();
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
