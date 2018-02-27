<?php


namespace Oliva\Utils\Tree\Modifier;

use Oliva\Utils\Tree\Builder\CallbackTrait,
	Oliva\Utils\Tree\Node\INode;


/**
 * RecursiveTreeWriter.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class RecursiveTreeWriter implements IModifier
{

	//
	//
	use CallbackTrait;
	//
	//


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


	public function __construct($parentMember = NULL, $idMember = NULL)
	{
		parent::__construct();
		$this->parentMember = $parentMember != NULL ? $parentMember : self::$parentMemberDefault; // intentionally !=
		$this->idMember = $idMember != NULL ? $idMember : self::$idMemberDefault; // intentionally !=
	}


	public function modify(INode $node)
	{
		return $this->modInternal($node);
	}


	private function modInternal(INode $node, $parentId = NULL)
	{
		$this->writeParentMember($node, $parentId);
		foreach ($node->getChildren() as $childNode) {
			$this->modInternal($childNode, $this->readIdMember($node));
		}
	}


	private function writeParentMember(INode $node, $value)
	{
		$parentMember = $this->parentMember;
		$node->$parentMember = $value;
		return $node;
	}


	private function readIdMember(INode $node)
	{
		$idMember = $this->idMember;
		return $node->$idMember;
	}

}
