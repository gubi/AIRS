<?php
/**
* Checks if url is valid or not
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
function is_valid_url($url){
	$url = @parse_url($url);
	
	if (!$url) {
		return false;
	}
	
	$url = array_map('trim', $url);
	$url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
	$path = (isset($url['path'])) ? $url['path'] : '';
	
	if ($path == ''){
		$path = '/';
	}
	
	$path .= (isset ($url['query'])) ? "?$url[query]" : '';
	
	if (isset ($url['host']) AND $url['host'] != gethostbyname ($url['host'])){
		if (PHP_VERSION >= 5){
			$headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
		} else {
			$fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);

			if (!$fp) {
				return false;
			}
			fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
			$headers = fread ($fp, 128);
			fclose ($fp);
		}
		$headers = (is_array ($headers)) ? implode ("\n", $headers) : $headers;
		return (bool) preg_match ('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
	}
	return false;
}
if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	header("Content-type: text/plain");
	print is_valid_url($_GET["uri"]);
}
?>