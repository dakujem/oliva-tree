<?php


namespace Oliva\Utils\Tree\Builder;


/**
 * MaterializedPathTreeBuilderFactory - provides factory for basic Materialized tree use cases
 * that require non-trivial configuration of the builder.
 *
 * As the aim of MaterializedPathTreeBuilder is to be as versatile as possible
 * and its configuration is not always a trivial task, this factory is provided.
 *
 *
 * @see MaterializedPathTreeBuilder
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class MaterializedPathTreeBuilderFactory
{


	/**
	 * Create and configure a builder for trees that are delimited,
	 * but the hierarchy of nodes only carries information about the node's ancestors,
	 * not its own position as its parent's child.
	 *
	 * The node's unique reference (usually ID) is added at the end of hierarchy string.
	 *
	 *
	 * @param string $hierarchyMember member containing the hierarchy information
	 * @param string $delimiterString usually a . , | / or a space " "
	 * @param string $reference name of the reference attribute, usually ID
	 * @param mixed $index index processor for the builder, see MaterializedPathTreeBuilder constructor
	 * @param bool $sortNodes sorting flag, see MaterializedPathTreeBuilder constructor
	 * @return MaterializedPathTreeBuilder
	 */
	public static function createDelimitedReferenceVariant($hierarchyMember = 'position', $delimiterString = '.', $reference = 'id', $index = NULL, $sortNodes = FALSE)
	{
		return self::createDelimitedVariant($hierarchyMember, $delimiterString, $reference, $index, $sortNodes);
	}


	/**
	 * Create and configure a builder for trees that fix-length hierarchy strings,
	 * but the hierarchy of nodes only carries information about the node's ancestors,
	 * not its own position as its parent's child.
	 *
	 * The node's unique reference (usually ID) is added at the end of hierarchy string.
	 *
	 *
	 * @param string $hierarchyMember member containing the hierarchy information
	 * @param int $delimiterLength length of the hierarchy for each level
	 * @param string $reference name of the reference attribute, usually ID
	 * @param mixed $index index processor for the builder, see MaterializedPathTreeBuilder constructor
	 * @param bool $sortNodes sorting flag, see MaterializedPathTreeBuilder constructor
	 * @return MaterializedPathTreeBuilder
	 */
	public static function createFixedLengthReferenceVariant($hierarchyMember = 'position', $delimiterLength = 3, $reference = 'id', $index = NULL, $sortNodes = FALSE)
	{
		$builder = new MaterializedPathTreeBuilder(NULL, $delimiterLength, $index, $sortNodes);
		list($hierarchyGetter, $referenceGetter) = self::getGetters($builder, $hierarchyMember, $reference);
		$hierarchy = function($data) use($delimiterLength, $hierarchyGetter, $referenceGetter) {
			$pos = call_user_func($hierarchyGetter, $data);
			$ref = call_user_func($referenceGetter, $data);
			if (strlen($ref) < $delimiterLength) {
				$ref = str_pad($ref, $delimiterLength, '0', STR_PAD_LEFT);
			} elseif (strlen($ref) > $delimiterLength) {
				$ref = substr($ref, 0, $delimiterLength);
			}
			return $pos . $ref;
		};
		$builder->setHierarchy($hierarchy);
		return $builder;
	}


	/**
	 * Create and configure a builder for trees that are delimited,
	 * and contain sequence (position) information or references.
	 * Uses robust hierarchy getter that repairs the hierarchy strings when not well formed.
	 *
	 * Use this factory when the hierarchy member is not well formed in the data set:
	 * - has unnecessary delimiter at the end or beginning , e.g.   ".1.2.1" or "1.2.1."
	 * - has multiple delimiters in a row, e.g.   "1..2...1"
	 *
	 *
	 * @param string $hierarchyMember member containing the hierarchy information
	 * @param string $delimiterString usually a . , | / or a space " "
	 * @param mixed $index index processor for the builder, see MaterializedPathTreeBuilder constructor
	 * @param bool $sortNodes sorting flag, see MaterializedPathTreeBuilder constructor
	 * @return MaterializedPathTreeBuilder
	 */
	public static function createRobustDelimitedVariant($hierarchyMember = 'position', $delimiterString = '.', $index = NULL, $sortNodes = FALSE)
	{
		return self::createDelimitedVariant($hierarchyMember, $delimiterString, NULL, $index, $sortNodes);
	}


	/**
	 * @internal
	 */
	protected static function createDelimitedVariant($hierarchyMember, $delimiterString, $reference, $index, $sortNodes)
	{
		$builder = new MaterializedPathTreeBuilder(NULL, $delimiterString, $index, $sortNodes);
		list($hierarchyGetter, $referenceGetter) = self::getGetters($builder, $hierarchyMember, $reference);
		$hierarchy = MaterializedPathTreeHelper::robustHierarchyGetter($delimiterString, $hierarchyGetter, $referenceGetter);
		$builder->setHierarchy($hierarchy);
		return $builder;
	}


	/**
	 * @internal
	 */
	protected static function getGetters($builder, $hierarchyMember, $reference)
	{
		$hierarchyGetter = function($data) use ($builder, $hierarchyMember) {
			return $builder->getMember($data, $hierarchyMember);
		};
		$referenceGetter = $reference !== NULL ? function($data) use ($builder, $reference) {
			return $builder->getMember($data, $reference);
		} : NULL;
		return [$hierarchyGetter, $referenceGetter];
	}

}
