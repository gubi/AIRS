<?php
/**
* Content scraping common functions
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
function parse_content($txt){
	//$txt = preg_replace("/((http:\/\/|(www\.))([^\s,]*))/i", "<a href=\"http://$1\" target=\"_blank\" title=\"vedi (link esterno)\">" . reduceurl("$1", 30) . "</a>", $txt);
	$txt = preg_replace("'([-_a-z0-9]+(\.[-_a-z0-9]+)*@[-a-z0-9]+(\.[-a-z0-9]+)*\.[a-z]{2,6})'i", "<a href=\"mailto:$1\">$1</a>", $txt);
	return $txt;
}
function strip_script_tags($txt){
	$search = array("@<script[^>]*?>.*?</script>@si",	// Strip out javascript
				 "@<style[^>]*?>.*?</style>@siU",		// Strip style tags properly
				 "@<![\s\S]*?--[ \t\n\r]*>@"			// Strip multi-line comments including CDATA
				);
	$txt = preg_replace($search, '', $txt);
	return $txt;
}
?>