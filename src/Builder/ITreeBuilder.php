<?php


namespace Oliva\Utils\Tree\Builder;

use Oliva\Utils\Tree\Node\INode;


/**
 * Tree Builder interface.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface ITreeBuilder
{


	/**
	 * Construct tree root from arbitrary data.
	 * 
	 *
	 * @param mixed $data
	 * @return INode the root node of the tree
	 */
	public function build($data);

}
