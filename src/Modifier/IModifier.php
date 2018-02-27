<?php


namespace Oliva\Utils\Tree\Modifier;

use Oliva\Utils\Tree\Node\INode;


/**
 * @author Andrej Rypak <xrypak@gmail.com>
 */
interface IModifier
{


	public function modify(INode $node);

}
