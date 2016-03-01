<?php


namespace Oliva\Utils\Tree\Node;


/**
 * @deprecated use the two traits directly.
 *
 * The base INode implementation trait for tree structures.
 * Includes all necessary tree nodes handling.
 *
 * Use in a class to implement the INode interface and provide som other convenient methods.
 * @see INode
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait BaseNodeTrait
{

	use BareNodeTrait,
	 ConvenientNodeTrait;

}
