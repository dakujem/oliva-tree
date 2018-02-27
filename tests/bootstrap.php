<?php


namespace Oliva\Test;

define('ROOT', __DIR__);
define('SCENES', ROOT . '/Scenes');

require_once __DIR__ . '/../vendor/autoload.php';
require_once SCENES . '/Scene.php';
require_once './DataWrappers/DataWrapper.php';
require_once './DataWrappers/LogWrapper.php';

use Tracy\Debugger,
	Tester\Environment;

// tester
Environment::setup();

// debugging
if (function_exists('getallheaders') && !empty(getallheaders()) && class_exists('Tracy\Debugger')) {
	Debugger::$strictMode = TRUE;
	Debugger::enable();
	Debugger::$maxDepth = 10;
	Debugger::$maxLen = 500;
}


// dump shortcut
function dump($var, $return = FALSE)
{
	return Debugger::dump($var, $return);
}
