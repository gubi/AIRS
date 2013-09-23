<?php
/**
* Cut out a part of text block
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
function taglia_stringa($stringa, $max_char, $ellipses = " [...]", $separator = " "){
	if($ellipses == null){
		$ellipses = " [...]";
	}
	if (strlen($stringa) > $max_char){
		$stringa_tagliata = substr($stringa, 0, $max_char);
		$last_space = (strrpos($stringa_tagliata, $separator) > 0 ? strrpos($stringa_tagliata, $separator) : strlen($stringa_tagliata));
		$stringa_ok = substr($stringa_tagliata, 0, $last_space);
		
		return $stringa_ok . $ellipses;
	} else {
		return $stringa;
	}
}
function prendi_periodo($stringa, $init_char, $max_char){
	if (strlen($stringa) > $max_char){
		$stringa_tagliata = substr($stringa, $init_char, $max_char);
		
		return "..." . $stringa_tagliata . " [...]";
	} else {
		return $stringa;
	}
}
?>