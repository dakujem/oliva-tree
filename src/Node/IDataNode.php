<?php


namespace Oliva\Utils\Tree\Node;


/**
 * An interface usable alongside INode to advertise that the node can return it's data contents.
 * This can be used for example for comparisons or for detaching the data from the tree structure.
 *
 * @see INode
 * 
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface IDataNode extends INode
{


	public function getContents();

}
