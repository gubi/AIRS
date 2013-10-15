<?php
/**
* Generates form for edit news
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$pdo = db_connect("editorss");
if (!isset($_GET["q"])){
	$content_subtitle = "Elenco delle news relative ai feeds indicizzati";
	
	$check_type = "news";
	require_once("common/tpl/EditoRSS/list.tpl");
} else {
	$check_feed = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . addslashes($GLOBALS["page_q"]) . "'");
	if ($check_feed->rowCount() > 0){
		while ($dato_check_feed = $check_feed->fetch()) {
			$the_id = $dato_check_feed["id"];
			$rss_title = $dato_check_feed["title"];
			$rss_uri = $dato_check_feed["uri"];
			$rss_description = $dato_check_feed["description"];
			$rss_group = $dato_check_feed["group"];
			if (strlen($rss_group) > 0){
				$groups = explode(",", $rss_group);
				foreach($groups as $li_group){
					$rss_li_group .= "<li>" . $li_group . "</li>";
				}
			}
			$rss_tag = $dato_check_feed["tags"];
			if (strlen($rss_tag) > 0){
				$tags = explode(",", $rss_tag);
				foreach($tags as $li_tag){
					$rss_li_tag .= "<li>" . $li_tag . "</li>";
				}
			}
		}
		unset($_GET["q"]);
		require_once("common/tpl/EditoRSS/news_form.tpl");
	} else {
		require_once("common/tpl/__404.tpl");
	}
}
?>
