<?php
/**
* Generates last edits template
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

require_once("common/include/funcs/_converti_data.php");

$last_edits = $pdo->query("select * from `airs_content` where `restrict_to_level` <= '" . $GLOBALS["user_level"] . "' and `manual_content_page` = '' order by `date` desc limit 6");
if ($last_edits->rowcount() > 0){
	while ($dato_last_edits = $last_edits->fetch()){
		$page_link = $dato_last_edits["name"];
		$page_name = $dato_last_edits["name"];
		if (strlen($dato_last_edits["subname"]) > 0){
			$page_link .= "/" . $dato_last_edits["subname"];
			$page_name = $dato_last_edits["subname"];
			if (strlen($dato_last_edits["sub_subname"]) > 0){
				$page_link .= "/" . $dato_last_edits["sub_subname"];
				$page_name = $dato_last_edits["sub_subname"];
			}
		}
		$page_name = str_replace("_", " ", $page_name);
		$last_edits_li .= utf8_decode(stripslashes("<li><a href=\"" . $page_link . "\" title=\"" . $dato_last_edits["subtitle"] . "\">" . $page_name . "</a><br />" . $dato_last_edits["subtitle"] . "<br />Modificata il " . converti_data(date("<b>d M Y</b> \a\l\l\e H:i:s", strtotime($dato_last_edits["date"]))) . "</li>"));
	}
}
$content_body = <<<Last_edits
	<ul>$last_edits_li</ul>
Last_edits;
?>