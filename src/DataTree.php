<?php


namespace Oliva\Utils\Tree;

use Oliva\Utils\Tree\Builder\ITreeBuilder;


/**
 * DataTree - a tree that is constructed from linear data structures, such as database results.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class DataTree extends Tree
{
	/**
	 *
	 * @var ITreeBuilder
	 */
	protected $builder = NULL;


	public function __construct(array $data, ITreeBuilder $builder)
	{
		$this->builder = $builder;
		parent::__construct($builder->build($data));
	}


	/**
	 * Rebuild the tree using new data.
	 *
	 *
	 * @param type $newData
	 * @return type
	 */
	public function rebuild($newData)
	{
		return $this->setRoot($this->builder->build($newData));
	}

}
