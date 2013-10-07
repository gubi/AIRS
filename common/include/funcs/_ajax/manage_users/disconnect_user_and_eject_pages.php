<?php
/**
* Disconnect user and eject it from its page editing
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

if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	$pdo = db_connect("");
	
	$disconnect_user = $pdo->query("update `airs_users` set `is_connected` = '0' where `username` = '" . addslashes($_GET["user"]) . "'");
	if (!$disconnect_user->execute()) {
		print "error::Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		$edit_page = $pdo->prepare("update `airs_content` set `is_modifying`= '0', `modifying_by`= '', `is_modifying_standby`= '0' where `modifying_by` = '" . addslashes($_GET["user"]) . "'");
		if (!$edit_page->execute()) {
			print "error::Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
		} else {
			print "ok::";
			$user_data = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($_GET["user"]) . "'");
			while ($dato_user_data = $user_data->fetch()){
				if(substr(strtolower($dato_user_data["name"]), -1) == "a") {
					$action_txt = " è stata disconnessa::";
					$connected_txt = "connessa";
				} else {
					$action_txt = " è stato disconnesso::";
					$connected_txt = "connesso";
				}
				print ucwords(strtolower($dato_user_data["name"] . " " . $dato_user_data["lastname"])) . $action_txt . $connected_txt;
			}
		}
	}
}
?>
