<?php
/**
* Restore a previous chronology version
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

if (isset($_GET["the_id"]) && trim($_GET["the_id"]) !== "" && isset($_GET["page"]) && trim($_GET["page"]) !== ""){
	require_once("../../.mysql_connect.inc.php");
	$pdo = db_connect("");
	
	$chronology_content = $pdo->query("select * from airs_chronology where `id` = '" . addslashes($_GET["the_id"]) . "'");
	if ($chronology_content->rowCount() > 0){
		while ($dato_chronology_content = $chronology_content->fetch()){
			$title = stripslashes($dato_chronology_content["title"]);
			$subtitle = stripslashes($dato_chronology_content["subtitle"]);
			$body = stripslashes($dato_chronology_content["body"]);
		}
	}
	$edit_content = $pdo->prepare("update airs_content set `title` =?, `subtitle` =?, `body` =?, `chronology_version`=? where `name`=? and `subname` =? and sub_subname =?");
	if ($edit_content->execute(array(addslashes($title), addslashes($subtitle), addslashes($body), addslashes($_GET["the_id"]), addslashes($_GET["page"]), addslashes($_GET["subpage"]), addslashes($_GET["sub_subpage"])))){
		print "ok";
	} else {
		print join(", ", $edit_content->errorInfo());
	}
}
?>