<?php


namespace Oliva\Utils\Tree\Builder;

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

}
