<?php

// runs a test found in QUERY_STRING, displays a list of avalable tests otherwise


$selectedTest = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING);
$extension = 'phpt';
$dir = '.';

// find files with phpt extension
$availableTests = array_filter(scandir($dir), function($filename) use($extension) {
	return substr($filename, -(strlen($extension)) - 1) === '.' . $extension;
});
// remove extension
array_walk($availableTests, function(&$filename) use ($extension) {
	$filename = substr($filename, 0, -(strlen($extension)) - 1);
});


if (!empty($selectedTest) && in_array($selectedTest, $availableTests)) {
	// run the selected test
	$time = microtime(TRUE);
	require_once($dir . '/' . $selectedTest . '.' . $extension);
	print '<hr/><pre>Test: ' . $selectedTest . ' | Finished at: ' . date('Y-m-d H:i:s') . ' | Runtime: ' . (microtime(TRUE) - $time ) . 's</pre>';
} else {
	// print available test (links)
	print "<fieldset><legend>Available tests:</legend><ul>";
	foreach ($availableTests as $filename) {
		print '<li><a href="?' . $filename . '">' . $filename . '</a></li>';
	}
	if (file_exists($dir . '/' . 'runall.php')) {
		print '<li></li><li><a href="runall.php">--- <i>run all ---</i></a></li>';
	}
	print "</ul></fieldset>";
}

