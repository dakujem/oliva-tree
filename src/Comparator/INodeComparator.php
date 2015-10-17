<?php


namespace Oliva\Utils\Tree\Comparator;

use Oliva\Utils\Tree\Node\IDataNode;


/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface INodeComparator
{


	/**
	 * Compare two nodes. The result is implementation dependent.
	 *
	 * The implementation can check for either:
	 * - data equality - the nodes are built with equivalent data
	 * - data identity - the nodes are built with exatly the same data
	 *
	 * The implementation can also do other type of comparison.
	 *
	 *
	 * @param IDataNode $node1
	 * @param IDataNode $node2
	 * @return bool|int
	 */
	public function compare(IDataNode $node1, IDataNode $node2);

}
