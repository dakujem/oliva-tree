<?php


namespace Oliva\Utils\Tree;


/**
 * PathTree - provided only for compatibility issues, do not use, will be removed!
 *
 * @deprecated - use new DataTree($data, new PathTreeBuilder()) instead
 *
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
		parent::__construct((new Builder\PathTreeBuilder($hierarchyMember, $charsPerLevel))->build($data));
	}

}
