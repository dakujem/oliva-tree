<?php


namespace Oliva\Utils\Tree\Builder;


/**
 * CallbackTrait.
 *
 *
 * @author Andrej Rypak <xrypak@gmail.com>
 */
trait CallbackTrait
{


	/**
	 * Tell whether the passed parameter is a callback acceptable by tree builders.
	 *
	 * NOTE:	When a string is passed, it has to contain a backslash character in order to be considered an acceptable callback.
	 * 			This is to solve conflicts between regular object/array member names and global-namespace functions, for example "file".
	 * 			In this case, "file" would be considered an object/array member, while "\file" would be considered a callback.
	 * 			This is also true for global namespace object methods like "Foo::bar", use "\Foo::bar" to pass a callback.
	 *
	 *
	 * @param mixed $callback the parameter to check
	 * @return bool TRUE when an acceptable callback is in $callback
	 */
	public function isAcceptableCallback($callback)
	{
        if (is_string($callback)) {
            return is_callable($callback, true) && strpos($callback, '\\') !== false;
        }
        return is_callable($callback);
	}

}
