<?php
/**
* Configuration file for Mailing functionality
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_blowfish.php");



$GLOBALS["mail_host"] = "mail.inran.it";
$GLOBALS["mail_port"] = 143;
$GLOBALS["mail_flag"] = "notls";
$GLOBALS["mail_mailbox_name"] = "INBOX";

$pdo = db_connect("");
$check = $pdo->query("select `mail_account`, `mail_password` from `airs_users` where `username` = '" . addslashes($_GET["user"]) . "'");
if($check->rowCount() > 0){
	while($dato_check = $check->fetch()){
		$GLOBALS["user_mail_account"] = $dato_check["mail_account"];
		$GLOBALS["user_mail_pssword"] = PMA_blowfish_decrypt($dato_check["mail_password"], $_COOKIE["iack"]);
	}
}
?>