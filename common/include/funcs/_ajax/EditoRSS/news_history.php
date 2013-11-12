<?php
/**
* List news
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
header("Content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");

$k = -1;
$pde = db_connect("editorss");
$feed_data = $pde->query("select * from `editorss_feeds`");
if($feed_data->rowCount() > 0){
	while ($dato_feed = $feed_data->fetch()){
		$k++;
		$res[$k]["name"] = trim($dato_feed["title"]);
		
		$news_data = $pde->query("select count(`id`) as 'tot_id' from `editorss_feeds_news` where `parent_id` = '" . addslashes($dato_feed["id"]) . "'");
		while ($dato_news = $news_data->fetch()){
			$res[$k]["y"] = (int)$dato_news["tot_id"];
			$major[] = (int)$dato_news["tot_id"];
		}
	}
	$major_value = max($major);
}
foreach($res as $rk => $rv){
	if($rv["y"] == $major_value){
		$res[$rk]["sliced"] = true;
	}
}
$json = json_encode($res);
if($_GET["debug"] == "true"){
	print_r($res);
	print $json;
}
?>