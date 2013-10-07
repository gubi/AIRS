<?php
/**
* Update user personal settings data
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

header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_blowfish.php");

if(isset($_POST["user_id"]) && trim($_POST["user_id"]) !== ""){
	$pdo = db_connect("");
	
	$_POST["user_email_password"] = PMA_blowfish_encrypt($_POST["user_email_password"], $_COOKIE["iack"]);
	
	$edit_user =  $pdo->prepare("update `airs_users` set `birth` = ('" . utf8_decode(addslashes($_POST["user_birthdate"])) . "'), `mail_account` = ('" . addslashes($_POST["user_email_account"]) . "'), `mail_password` = ('" . addslashes($_POST["user_email_password"]) . "'), `username` = ('" . addslashes($_POST["user_username"]) . "'), `newsletter_frequency` = ('" . addslashes($_POST["user_update_frequency"]) . "'), `session_length` = ('" . addslashes($_POST["user_session_length"]) . "') where `id` = '" . addslashes($_POST["user_id"]) . "'");
	if (!$edit_user->execute()) {
		$act = "error:Si &egrave; verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		$act = "edited:" . $_POST["user_id"];
	}
	print $act;
}
?>