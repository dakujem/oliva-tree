<?php


namespace Oliva\Utils\Tree;


/**
 * RecursiveTree - provided only for compatibility issues, do not use, will be removed!
 *
 * @deprecated - use new DataTree($data, new RecursiveTreeBuilder()) instead
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
		parent::__construct((new Builder\RecursiveTreeBuilder($parentMember, $idMember))->build($data));
	}

}
