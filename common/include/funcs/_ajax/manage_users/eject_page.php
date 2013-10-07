<?php
/**
* Eject user from page editing
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
* @package	AIRS_manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");

if(isset($_GET["page_id"]) && trim($_GET["page_id"]) !== ""){
	$pdo = db_connect("");
	
	$edit_page = $pdo->prepare("update `airs_content` set `is_modifying`= '0', `modifying_by`= '', `is_modifying_standby`= '0' where `id` = '" . addslashes($_GET["page_id"]) . "'");
	if (!$edit_page->execute()) {
		print "error::Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		print "ok::";
		$page_data = $pdo->query("select `title` from `airs_content` where `id` = '" . addslashes($_GET["page_id"]) . "'");
		while ($dato_page_data = $page_data->fetch()){
			print $dato_page_data["title"];
		}
	}
}
?>
