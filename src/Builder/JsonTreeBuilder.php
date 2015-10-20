<?php


namespace Oliva\Utils\Tree\Builder;

use stdClass,
	RuntimeException;
use Oliva\Utils\Tree\Node\INode;


/**
 * JSON tree builder. Builds trees from data that is already structured, encoded in JSON string.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class JsonTreeBuilder extends SimpleTreeBuilder implements ITreeBuilder
{


	/**
	 * Create an instance of INode from data that is already in a tree structure, encoded in JSON string.
	 * 
	 *
	 * @param string $json JSON string containing the root node data
	 * @return INode
	 */
	public function build($json)
	{
		return $this->buildNode(json_decode($json));
	}


	/**
	 * Get the value of a member of the $item. The $item can be either array or object.
	 *
	 *
	 * @param stdClass $item
	 * @param string $member
	 * @return mixed the value of the member
	 * @throws RuntimeException
	 */
	public function getMember($item, $member)
	{
		if ($item instanceof stdClass && isset($item->$member)) {
			$value = $item->$member;
		} else {
			$value = $this->dataError($item, $member, new RuntimeException($this->formatMissingMemberMessage($item, $member), 1));
		}
		return $value;
	}

}
