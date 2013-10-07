<?php
/**
* Re-login user after inserting encryotion key
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

if (isset($_GET["k1"]) && trim($_GET["k1"]) !== "" && isset($_GET["k2"]) && trim($_GET["k2"]) !== ""){
	require_once("../_blowfish.php");
	
	$key = "inran_dev_2011";
	$encrypted_key = PMA_blowfish_encrypt(urldecode($_GET["k2"]), $key);
	$crypted_user = PMA_blowfish_encrypt(urldecode($_GET["u"]), $encrypted_key);
	if (urldecode($_GET["k1"]) !== $encrypted_key){
		print "rejected";
	} else {
		$pdo = db_connect("");
		$select_check = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($_GET["u"]) . "' and encryption_key = '" . addslashes($encrypted_key) . "' limit 1");
		if ($select_check->rowCount() > 0){
			while($dato_check = $select_check->fetch()){
				$hour = time() + $dato_check["session_length"];
			}
			$booking_page = $pdo->prepare("update `airs_content` set `is_modifying_standby`= '0' where `name` = '" . addslashes($_GET["page"]) . "' and `subname` = '" . addslashes($_GET["subpage"]) . "' and `sub_subname` = '" . addslashes($_GET["sub_subpage"]) . "'");
			if (!$booking_page->execute()){
				print join(", ", $booking_page->errorInfo());
				exit();
			}
			setcookie("iac", "null", time() - 9999,"",  ".airs.inran.it");
			setcookie("iack", "null", time() - 9999,"",  ".airs.inran.it");
			setcookie("iac", $crypted_user, $hour,"",  ".airs.inran.it");
			setcookie("iack", $encrypted_key, $hour,"",  ".airs.inran.it");
			
			$reconnected_user = $pdo->prepare("update `airs_users` set `is_connected` = '1' where `username` = '" . addslashes($_GET["u"]) . "'");
			if ($reconnected_user->execute()){
				print "allowed";
			}
		}
	}
} else {
	print "rejected";
}

?>