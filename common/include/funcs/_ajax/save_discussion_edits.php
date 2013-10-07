<?php
/**
* Save discussion edits
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
header("Content-type: text/plain; charset=utf-8;");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	$pdo = db_connect("");
	if (trim($_GET["discussion_content"]) !== ""){
		$edit_comment =  $pdo->prepare("update `airs_discussions` set `title` = ('" . addslashes($_GET["discussion_object"]) . "'), `body` = ('" . addslashes($_GET["discussion_content"]) . "') where `id` = '" . addslashes($_GET["id"]) . "'");
		if (!$edit_comment->execute()) {
			$act = "error:Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
		} else {
			require_once("Text/Wiki.php");
			require_once("../../conf/Wiki/rendering.php");
			$output = $wiki->transform(stripslashes($_GET["discussion_content"]), "Xhtml");
			$act = "edited~~~" . $_GET["id"] . "~~~" . $_GET["discussion_object"] . "~~~" . $output;
		}
	} else {
		if ($_GET["confirm"] == "true"){
			$remove_comment =  $pdo->prepare("delete from `airs_discussions` where `id` = '" . addslashes($_GET["id"]) . "'");
			if (!$remove_comment->execute()) {
				$act = "Si è verificato un errore durante la rimozione delle news:<br />" . $pdo->errorCode();
			} else {
				$select_last_message = $pdo->query("select max(`id`) as 'maxid' from `airs_discussions`");
				if ($select_last_message->rowCount() > 0){
					while($dato_last_message = $select_last_message->fetch()){
						$last_id = $dato_last_message["maxid"];
					}
				}
				$act = "removed~~~" . $_GET["id"] . "~~~" . $last_id;
			}
		} else {
			$act = "need_confirm~~~" . $_GET["id"];
		}
	}
	print $act;
}
?>