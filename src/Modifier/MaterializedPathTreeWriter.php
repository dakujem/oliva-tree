<?php


namespace Oliva\Utils\Tree\Modifier;

use Oliva\Utils\Tree\Builder\CallbackTrait,
	Oliva\Utils\Tree\Node\INode,
	RuntimeException;


/**
 * MaterializedPathTreeWriter.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class MaterializedPathTreeWriter implements IModifier
{

	//
	//
	use CallbackTrait;
	//
	//


	const RADIX_10 = 10;
	const RADIX_16 = 16;
	const RADIX_36 = 36;

	/**
	 * The default hierarchy member.
	 * @var string
	 */
	public static $hierarchyDefault = 'position';

	/**
	 * The delimiter default. Can be integer for fixed-length hierarchy or string for delimited hierarchy.
	 * @var int|string
	 */
	public static $delimiterDefault = 3;

	/**
	 * The delimiting processor.
	 * @var array[$callable, $delimiterParameter]
	 */
	protected $delimitingProcessor;

	/**
	 * The radix to which numbers are encoded (converted).
	 * @var int
	 */
	protected $numberRadix = self::RADIX_36;


	public function modify(INode $node)
	{
		return $this->modInternal($node);
	}


	public function setDelimiter($delimiter)
	{
		if (is_numeric($delimiter)) {
			$this->delimitingProcessor = [[$this, 'generateHierarchyMemberFixed'], $delimiter];
		} elseif ($this->isAcceptableCallback($delimiter)) {
			$this->delimitingProcessor = [$delimiter, NULL];
		} elseif (is_string($delimiter)) {
			$this->delimitingProcessor = [[$this, 'generateHierarchyMemberDelimited'], $delimiter];
		} else {
			throw new RuntimeException(sprintf('Invalid delimiter of type %s provided. Either provide an integer for fixed-length hierarchy delimiting, a string containing a delimiting character or a callable function that will return hierarchy member value.', is_object($delimiter) ? get_class($delimiter) : gettype($delimiter)), 4);
		}
		return $this;
	}


	public function getNumberRadix()
	{
		return $this->numberRadix;
	}


	public function setNumberRadix($numberRadix)
	{
		$this->numberRadix = min([max([(int) $numberRadix, 2]), 36]); // enforce range from 2 to 36
		return $this;
	}


	private function modInternal(INode $node, array $pathVector = [])
	{
		$this->writeHierarchyMember($node, $this->generateHierarchyMember($pathVector));
		$counter = 0;
		foreach ($node->getChildren() as $key => $childNode) {
			$this->modInternal($childNode, array_merge($pathVector, $this->getNodeVectorAddition($node, $pathVector, $key, $counter)));
			$counter += 1;
		}
	}


	private function writeHierarchyMember(INode $node, $value)
	{
		//TODO
		$hierarchy = $this->hierarchyMember;
		$node->$hierarchy = $value;
		return $node;
	}


	private function getNodeVectorAddition(INode $node, array $pathVector, $key, $counter)
	{
		//TODO use node ID, child key, counter or something else?
		return $counter;
	}


	private function generateHierarchyMember(array $pathVector)
	{
		//   fixed-length   vs   delimited
		//        numeric   vs   ascii (base36)


		$prefix = ''; //TODO prefix can be set when working with incomplete trees - branches

		$str = $prefix . call_user_func($this->delimitingProcessor[0], $pathVector, $this->delimitingProcessor[1], $this->numberRadix);
		if ($str === FALSE || strlen($str) === 0) {
			return NULL;
		}
		return $str;
	}


	/**
	 * @internal set in self::setDelimiter()
	 */
	private function generateHierarchyMemberDelimited(array $pathVector, $delimiter, $numberRadix)
	{
		return implode($delimiter, $this->encodeNumbersInVector($pathVector, $numberRadix));
	}


	/**
	 * @internal set in self::setDelimiter()
	 */
	private function generateHierarchyMemberFixed(array $pathVector, $width, $numberRadix)
	{
		$res = '';
		foreach ($this->encodeNumbersInVector($pathVector, $numberRadix) as $element) {
			$res.= $this->align($element, $width);
		}
		return $res;
	}


	private function encodeNumbersInVector(array $pathVector, $numberRadix)
	{
		if ($numberRadix !== self::RADIX_10) {
			array_walk($pathVector, function(&$element) use($numberRadix) {
				$element = $this->encodeNumber($element, $numberRadix);
			});
		}
		return $pathVector;
	}


	private function encodeNumber($val, $radix)
	{
		if (is_numeric($val) && $radix !== self::RADIX_10) {
			return base_convert($val, self::RADIX_10, $radix);
		}
		return $val;
	}


	private function align($val, $width)
	{
		return substr(sprintf('%' . (is_numeric($val) ? '0' : ' ') . (int) $width . '.0s', $val), 0, $width);
	}

}
