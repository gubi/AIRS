<?php
/**
* Converts ANSI colors to css color
*
* Based on PEAR/Console_color colorcodes
* http://pear.php.net/manual/en/package.console.console-color.colorcodes.php
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
function colorize($ansi_text){
	$css_text = str_replace(
		array("%k", // Black
			 "%r", // Red
			 "%g", // Green
			 "%b", // Blue
			 "%y", // Yellow
			 "%m", // Magenta
			 "%p", // Purple
			 "%c", // Cyan
			 "%w", // White
			 "%n" // ~ End
			),
		array("<span style=\"color: black;\">",
			 "<span class=\"removed\">",
			 "<span class=\"added\">",
			 "<span class=\"commit\">",
			 "<span style=\"color: yellow;\">",
			 "<span style=\"color: magenta;\">",
			 "<span style=\"color: purple;\">",
			 "<span style=\"color: cyan;\">",
			 "<span style=\"color: white;\">",
			 "</span>"
			), $ansi_text);
	return $css_text;
}
?>