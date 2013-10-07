<?php
/**
* Translate a block of text
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
function translate($text, $from = "en", $to = "it"){
	$filename = "http://mymemory.translated.net/api/get?q=" . urlencode($text) . "&langpair=" . $from . "|" . $to . "&ip=66.249.71.11&email=gubi.ale@gotanotherway.com";
	$handle = fopen($filename, "r");
	$contents = stream_get_contents($handle);

	//print_r(json_decode($contents, true));
	$translated = json_decode($contents, true);
	//return "http://mymemory.translated.net/api/get?q=" . urlencode($text) . "&langpair=" . $from . "|" . $to;
	return $translated["responseData"]["translatedText"];
}
//print translate($_GET["term"], "it", "en");
?>