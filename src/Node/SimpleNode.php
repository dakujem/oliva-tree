<?php


namespace Oliva\Utils\Tree\Node;


/**
 * SimpleNode.
 *
 *
 * @property mixed $value
 *
 *
 * @author Andrej Rypak <andrej.rypak@viaaurea.cz>
 * @copyright Via Aurea, s.r.o.
 */
class SimpleNode extends NodeBase implements IDataNode
{
	public $value = NULL;


	public function __construct($value = NULL)
	{
		$this->setValue($value);
	}


	/**
	 * Get the node's value.
	 *
	 *
	 * @return mixed
	 */
	public function getValue()
	{
		return $this->value;
	}


	/**
	 * Set the node's value.
	 *
	 *
	 * @param mixed $value
	 * @return self fluent
	 */
	public function setValue($value)
	{
		$this->value = $value;
		return $this;
	}


	/**
	 * Alias for getValue().
	 *
	 *
	 * @return mixed the value of the node
	 */
	public function getContents()
	{
		return $this->getValue();
	}


	/**
	 * Alias for setValue().
	 *
	 *
	 * @param mixed $value
	 * @return self fluent
	 */
	public function setContents($value)
	{
		return $this->setValue($value);
	}

}
