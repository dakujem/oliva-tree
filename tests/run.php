<?php

// runs a test found in QUERY_STRING, displays a list of avalable tests otherwise


$selectedTest = filter_input(INPUT_SERVER, 'QUERY_STRING', FILTER_SANITIZE_STRING);
$extension = 'phpt';

// find files with phpt extension
$availableTests = array_filter(scandir('.'), function($filename) use($extension) {
	return substr($filename, -(strlen($extension)) - 1) === '.' . $extension;
});
// remove extension
array_walk($availableTests, function(&$filename) use ($extension) {
	$filename = substr($filename, 0, -(strlen($extension)) - 1);
});


if (!empty($selectedTest) && in_array($selectedTest, $availableTests)) {
	// run the selected test
	require_once(/* __DIR__ . '/' . */ $selectedTest . '.' . $extension);
} else {
	// print available test (links)
	print "<fieldset><legend>Available tests:</legend><ul>";
	foreach ($availableTests as $filename) {
		print '<li><a href="?' . $filename . '">' . $filename . "</a></li>";
	}
	print "</ul></fieldset>";
}

