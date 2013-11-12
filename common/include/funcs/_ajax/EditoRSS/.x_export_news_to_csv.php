<?php
/**
* Export data in CSV format
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
require_once("../../.mysql_connect.inc.php");
//header("Content-type: text/plain");
header("Content-type: application/force-download; charset=utf-8");
header("Content-Disposition: attachment; filename=news_editorss_" . date("d-m-Y") . ".csv");

if (isset($_GET["ids"]) && trim($_GET["ids"]) !== ""){
	$pdo = db_connect("editorss");
	
	$ids = explode(",", $_GET["ids"]);
	foreach($ids as $id){
		$idds[] = $id;
	}
	sort($idds);
	//id, group, title, description, uri, valid_resources
	print "\"ID\",\"TITOLO\",\"CORPO\",\"DATA DI PUBBLICAZIONEx\",\"LINK\",\"TAG\",\"ID FEED\",\"GRUPPO DI FEED\",\"TITOLO FEED\",\"DESCRIZIONE FEED\",\"URI FEED\",\"RISORSE VALIDE NEL FEED\"\r\n";
	foreach($idds as $id){
		$news_feed = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . addslashes($id) . "'");
		while($dato_news_feed = $news_feed->fetch()){
			print "\"" . $dato_news_feed["id"] . "\",\"" . $dato_news_feed["title"] . "\",\"" . utf8_encode(html_entity_decode($dato_news_feed["description"])) . "\",\"" . $dato_news_feed["date"] . "\",\"" . $dato_news_feed["link"] . "\",\"" . $dato_news_feed["tags"] . "\",\"" . $dato_news_feed["parent_id"] . "\",";
			
			$feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . addslashes($dato_news_feed["parent_id"]) . "'");
			while($dato_feed = $feed->fetch()){
				print "\"" . $dato_feed["group"] . "\",\"" . $dato_feed["title"] . "\",\"" . $dato_feed["description"] . "\",\"" . $dato_feed["uri"] . "\",\"" . $dato_feed["valid_resources"] . "\"\r\n";
			}
		}
	}
}
?>