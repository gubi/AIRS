<?php
/**
* Starts or block scanning of feeds
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

if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	if (isset($_GET["action"]) && trim($_GET["action"]) !== ""){
		$pdo = db_connect("editorss");
		
		switch(trim($_GET["type"])){
			case "feed":
				$primary_table = "editorss_feeds";
				
				break;
			case "news":
				$primary_table = "editorss_feeds_news";
				
				break;
		}
		if (isset($_GET["type"]) && strlen(trim($_GET["type"])) > 0){
			$edit_feed_action =  $pdo->prepare("update `" . $primary_table . "` set `automation_status` = ('" . addslashes($_GET["action"]) . "') where `id` = '" . addslashes($_GET["id"]) . "'");
			if (!$edit_feed_action->execute()) {
				print "Si &egrave; verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
			} else {
				print $_GET["action"];
			}
		} else {
			print "no type";
		}
	} else {
		print "no action";
	}
}
?>