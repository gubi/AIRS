<?php
/**
* Make url human friendly
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
* @SLM_status	testing
*/
function make_url_friendly($url){
	//$url = strtolower($url);
	$find = array(' ', 
			      '&', 
			      '\r\n', 
			      '\n', 
			      '/', 
			      '\\', 
			      '+');
		
	$url = str_replace($find, '-', $url); 
	$find = array(' ', 
			      'é', 
			      'è', 
			      'ë', 
			      'ê'); 
	$url = str_replace($find, 'e', $url);
	$find = array(' ', 
			      'ó', 
			      'ò', 
			      'ô', 
			      'ö'); 
	$url = str_replace($find, 'o', $url); 
	$find = array(' ', 
			      'á', 
			      'à', 
			      'â', 
			      'ä');
	$url = str_replace($find, 'a', $url); 
	$find = array(' ', 
			      'í', 
			      'ì', 
			      'î', 
			      'ï'); 
	$url = str_replace($find, 'i', $url); 
	$find = array(' ', 
			      'ú', 
			      'ù', 
			      'û', 
			      'ü'); 
	$url = str_replace($find, 'u', $url); 
	$find = array('/[^A-Za-z0-9\-<>\.]/', 
			      '/[\-]+/', 
			      '/<[^>]*>/'); 
	$repl = array('', 
			      '_', 
			      '');
	$url = preg_replace($find, $repl, $url); 
	
	return $url; 
}
?>