<?php


namespace Oliva\Utils\Tree\Node;


/**
 * SimpleNode.
 *
 *
 * Note:	when clonning a SimpleNode instance, the value is NOT clonned! Only a shallow copy is created.
 * 			Use cloneContents() after clonning to clone objects.
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

	//-----------------------------------------------------------------
	//------------------------- clonning ------------------------------


	/**
	 * Make a copy of the contents.
	 *
	 * This is useful when clonning a branch or a node.
	 *
	 *
	 * @return self fluent
	 */
	public function cloneContents()
	{
		if (is_object($this->getContents())) {
			$this->setContents(clone $this->getContents());
		}
		return $this;
	}
}
