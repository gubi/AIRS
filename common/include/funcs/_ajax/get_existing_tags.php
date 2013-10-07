<?php
/**
* Check if tag exists
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
header("Content-type: text/plain");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$pdo = db_connect("");
	$tables = array(
				"airs_rdf_files",
				"editorss_feeds",
				"editorss_feeds_news"
				);
	$t = 0;
	foreach ($tables as $tabella){
		$t++;
		$query .= "select distinct `tag` from `" . $tabella . "` where lower(`tag`) like '" . addslashes(strtolower($_GET["term"])) . "%'";
		if ($t > 0 && $t < count($tables)){
			$query .= " union ";
		}
	}
	$select_tag = $pdo->query($query);
	if ($select_tag->rowCount() > 0){
		while ($dato_select_tag = $select_tag->fetch()){
			if (strstr($dato_select_tag["tag"], ",")){
				$tags = explode(",", $dato_select_tag["tag"]);
				foreach($tags as $tt){
					$tag[] = trim($tt);
				}
			} else {
				$tag[] = trim($dato_select_tag["tag"]);
			}
		}
	}
	if (count($tag) > 0){
		$tag = array_slice($tag, 0, 3);
		$tag = array_unique($tag);
		sort($tag);
		print json_encode($tag);
	}
}
?>