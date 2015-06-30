<?php


namespace Oliva\Utils\Tree\Node;


/**
 * SimpleNode.
 *
 *
 * @author Andrej Rypak <andrej.rypak@viaaurea.cz>
 * @copyright Via Aurea, s.r.o.
 */
class SimpleNode extends NodeBase
{
	public $value = NULL;


	public function __construct($value = NULL)
	{
		$this->setValue($value);
	}


	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	public function getValue()
	{
		return $this->value;
	}

}
