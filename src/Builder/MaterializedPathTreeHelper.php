<?php


namespace Oliva\Utils\Tree\Builder;


/**
 * MaterializedPathTreeHelper.
 * 
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
class MaterializedPathTreeHelper
{


	public static function robustHierarchyGetter($delimiter, $hierarchyGetter, $referenceGetter = NULL)
	{
		return function($data) use ($delimiter, $hierarchyGetter, $referenceGetter) {
			$pos = call_user_func($hierarchyGetter, $data);
			if ($pos !== NULL) {
				$pcs = array_filter(explode((string) $delimiter, (string) $pos), function($item) {
					return $item !== NULL && $item !== '';
				});
				if ($referenceGetter !== NULL) {
					$pcs[] = call_user_func($referenceGetter, $data);
				}
				return implode((string) $delimiter, $pcs);
			}
			return $pos;
		};
	}

}
