<?php


namespace Oliva\Test;

require_once __DIR__ . '/../vendor/autoload.php';
require_once './DataWrappers/DataWrapper.php';
require_once './DataWrappers/LogWrapper.php';

use Tracy\Debugger,
	Tester\Environment;

define('ROOT', __DIR__);
define('SCENES', ROOT . '/Scenes');

// tester
Environment::setup();

// debugging
Debugger::$strictMode = TRUE;
Debugger::enable();


// dump shortcut
function dump($var, $return = FALSE)
{
	return Debugger::dump($var, $return);
}
