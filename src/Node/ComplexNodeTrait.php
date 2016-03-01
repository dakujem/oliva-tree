<?php


namespace Oliva\Utils\Tree\Node;


/**
 * ComplexNodeTrait.
 * All that you need to implement a node with the full feature set.
 * 
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait ComplexNodeTrait
{

	use BareNodeTrait,
	 ConvenientNodeTrait,
	 FluentNodeTrait,
	 DeepCloningTrait;

}
