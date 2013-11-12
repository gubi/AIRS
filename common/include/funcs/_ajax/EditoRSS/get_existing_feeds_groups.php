<?php
/**
* Detect if feed exists
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
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$pdo = db_connect("editorss");
	$tables = array(
				"editorss_feeds"
				);
	$t = 0;
	foreach ($tables as $tabella){
		$t++;
		$query .= "select distinct `group` from `" . $tabella . "` where lower(`group`) like '%" . addslashes(strtolower($_GET["term"])) . "%'";
		if ($t > 0 && $t < count($tables)){
			$query .= " union ";
		}
	}
	$select_group = $pdo->query($query);
	if ($select_group->rowCount() > 0){
		while ($dato_select_group = $select_group->fetch()){
			if (strstr($dato_select_group["group"], ",")){
				$groups = explode(",", $dato_select_group["group"]);
				foreach($groups as $tt){
					$group[] = trim($tt);
				}
			} else {
				$group[] = trim($dato_select_group["group"]);
			}
		}
	}
	if (count($group) > 0){
		$group = array_slice($group, 0, 3);
		$group = array_unique($group);
		sort($group);
		print json_encode($group);
	}
}
?>