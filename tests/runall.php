<?php

// runs all test found current directory

$extension = 'phpt';
$dir = '.';

// find files with phpt extension
$availableTests = array_filter(scandir($dir), function($filename) use($extension) {
	return substr($filename, -(strlen($extension)) - 1) === '.' . $extension;
});

$startTime = microtime(TRUE);
foreach ($availableTests as $test) {
	// run the selected test
	$time = microtime(TRUE);
	print '<hr/>';
	require_once($dir . '/' . $test);
	print '<br/><pre>~~~ Test: ' . substr($test, 0, -(strlen($extension)) - 1) . ' | Runtime: ' . (microtime(TRUE) - $time ) . 's</pre>';
}
print '<hr/><pre>All finished at: ' . date('Y-m-d H:i:s') . ' | Runtime: ' . (microtime(TRUE) - $startTime ) . 's</pre>';

