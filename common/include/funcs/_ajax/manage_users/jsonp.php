<?php

/**
* Set the callback variable to what jQuery sends over
*/
$callback = (string)$_GET['callback'];
if (!$callback) $callback = 'callback';

/**
* The $filename parameter determines what file to load from local source
*/
$filename = $_GET['filename'];
if (preg_match('/^[a-zA-Z\-]+\.json$/', $filename)) {
	$json = file_get_contents($filename);
} else {
	require_once("user_connection_history.php");
}

/**
* The $url parameter loads data from external sources
*/
$url = $_GET['url'];
	if ($url) {
		if (preg_match('/^http:\/\/[\/\w \.-]*\.xml$/', $url)) {
			$xml = simplexml_load_file($url);
			$json = json_encode($xml);
		} else if (preg_match('/^http:\/\/[\/\w \.-]*\.csv$/', $url)) {
			$csv = str_getcsv(file_get_contents($url));
			$json = json_encode($xml);
		}
}

// Send the output
header('Content-Type: text/javascript');
echo "$callback($json);";
?>