<?php
/**
* Generates tags page
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

$pdo = db_connect("editorss");
if (strlen($_GET["id"]) == 0){
	// Elenco dei tags
	$content_title = "Tags nel sistema";
	$content_subtitle = "Statistiche d'uso";
	$tag_list = $pdo->query("select `tags` from `editorss_feeds` union select `tags` from `editorss_feeds_news`");
	if ($tag_list->rowCount() > 0){
		$content_body .= <<<Tag_list
		<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
		<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
		<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
		<script language="javascript" type="text/javascript">
		$(document).ready(function() {
			$("#table_tag_list").flexigrid({
				colModel : [
					{display: 'TAG', name : 'tags', width : 120, sortable : false, align: 'left'},
					{display: 'PRESENZE TOTALI', name : 'total_presence', width : 32, sortable : false, align: 'right'}
				],
				url: "common/include/funcs/_ajax/get_tag_list.php",
				dataType: "json",
				sortorder: "asc",
				usepager: true,
				useRp: true,
				rp: 5
			});
		});
		</script>
		<table cellpadding="0" cellspacing="0" id="table_tag_list"></table>
Tag_list;
		while ($dato_tag = $tag_list->fetch()){
			if (strlen($dato_tag["tags"]) > 0){
				$tags[] = $dato_tag["tags"];
			}
		}
		foreach($tags as $tag){
			$tag .= "<li>";
		}
	}
} else {
	// Scheda del tag
	require_once("common/include/funcs/_taglia_stringa.php");
	$content_title = "Scheda del tag \"" . str_replace("_", " ", $GLOBALS["page"]) . "\"";
	
	$db["pdo"] = db_connect("");
	$db["pdoa"] = db_connect("air");
	$db["pdoe"] = db_connect("editorss");
	$params["editorss_feeds"]["db"] = "pdoe";
	$params["editorss_feeds"]["type"] = "Feed";
	$params["editorss_feeds"]["component"]["name"] = "EditoRSS";
	$params["editorss_feeds"]["component"]["type"] = "Feeds";
	$params["editorss_feeds_news"]["db"] = "pdoe";
	$params["editorss_feeds_news"]["type"] = "News";
	$params["editorss_feeds_news"]["component"]["name"] = "EditoRSS";
	$params["editorss_feeds_news"]["component"]["type"] = "News";
	$params["air_research"]["db"] = "pdoa";
	$params["air_research"]["type"] = "AIR";
	$params["air_research"]["component"]["name"] = "AIR";
	$params["air_research"]["component"]["type"] = "Ricerche";
	
	foreach ($params as $table => $data){
		$the_query = "select * from `" . $table . "` where lower(`tags`) like '%" . addslashes(str_replace("_", " ", strtolower($GLOBALS["page"]))) . "%'";
		$tag_list = $db[$data["db"]]->query($the_query);
		if($tag_list->rowCount() > 0){
			while ($dato_tag = $tag_list->fetch()){
				$details_query = $db[$params[$table]["db"]]->query("select * from `" . $table . "` where `id` = '" . $dato_tag["id"] . "'");
				if($details_query->rowCount() > 0){
					while($dato_details_query = $details_query->fetch()){
						$founded_link .= "<li>" . $params[$table]["type"] . ": <a href=\"./" . $params[$table]["component"]["name"] . "/" . $params[$table]["component"]["type"] . "/" . $dato_tag["id"] . "\" title=\"" . htmlentities($dato_details_query["title"]) . "\">" . taglia_stringa($dato_details_query["title"], 75, "...") . "</a></li>";
					}
				}
			}
		}
	}
	
	$content_body = <<<Table
	<fieldset>
		<legend class="label">Tag</legend>
		Il tag è stato trovato nelle seguenti posizioni:
		<ul>$founded_link</ul>
		<br />
		
		<br />
		<br />
	</fieldset>
Table;
}
require_once("common/include/conf/replacing_object_data.php");
?>