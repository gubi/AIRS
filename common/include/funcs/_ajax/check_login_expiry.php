<?php
/**
* Check if user login is expired
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
header("Content-type: text/plain");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	$pdo = db_connect("");
	// Se non è settato il cookie oppure il cookie ha valore vuoto
	if (!isset($_COOKIE["iac"]) || trim($_COOKIE["iac"]) == ""){
		// Disconnette l'utente
		$disconnected_user = $pdo->prepare("update `airs_users` set `is_connected` = '0' where `username` = '" . addslashes($_GET["user"]) . "'");
		if ($disconnected_user->execute()){
			// Segna il contenuto come in standby
			$booking_page = $pdo->prepare("update `airs_content` set `is_modifying_standby`= '1' where `name` = '" . addslashes($_GET["page"]) . "' and `subname` = '" . addslashes($_GET["subpage"]) . "' and `sub_subname` = '" . addslashes($_GET["sub_subpage"]) . "'");
			if (!$booking_page->execute()){
				print join(", ", $booking_page->errorInfo());
				exit();
			} else {
				print "expired";
			}
		}
	} else {
		$check_user_connection = $pdo->query("select `is_connected` from `airs_users` where `username` = '" . addslashes($_GET["user"]) . "'");
		while($dato_check_user_connection = $check_user_connection->fetch()){
			if($dato_check_user_connection["is_connected"] == "0"){
				print "expired";
			} else {
				// Reimposta il contenuto come non in standby
				$booking_page = $pdo->prepare("update `airs_content` set `is_modifying_standby`= '0' where `name` = '" . addslashes($_GET["page"]) . "' and `subname` = '" . addslashes($_GET["subpage"]) . "' and `sub_subname` = '" . addslashes($_GET["sub_subpage"]) . "'");
				if (!$booking_page->execute()){
					print join(", ", $booking_page->errorInfo());
					exit();
				} else {
					print "allowed";
				}
			}
		}
	}
}
?>