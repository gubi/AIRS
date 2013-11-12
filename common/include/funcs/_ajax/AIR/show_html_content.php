<?php
/**
* Returns html content from research result
* 
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/html; charset=utf-8;");
require_once("../../../.mysql_connect.inc.php");
function isUTF8($string) {
    return (utf8_encode(utf8_decode($string)) == $string);
}

if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	$pdo = db_connect("air");
	$page_card = $pdo->query("select * from `air_research_results` where `id` = '" . addslashes($_GET["id"]) . "'");
	if ($page_card->rowCount() > 0){
		while($dato_page_card = $page_card->fetch()){
			if (isset($_GET["cache"]) && trim($_GET["cache"]) == "true"){
				$html = stripslashes($dato_page_card["result_cache_html_content"]);
			} else {
				$html = stripslashes($dato_page_card["result_entire_html_content"]);
			}
			$html = preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", "", stripslashes($html));
			$html = mb_convert_encoding($html, "UTF-8", mb_detect_encoding($html));
			//$html2 = mb_detect_encoding($html);
			//$html = iconv("UTF-8", "ISO-8859-1", $html);

			print $html;
		}
	}
}
?>