<?php
/**
* Move a Mailbox dir
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");

if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	if(isset($_GET["dir"]) && trim($_GET["dir"]) !== ""){
		if(isset($_GET["id"]) && trim($_GET["id"]) !== ""){
			if(isset($_GET["move_to_folder"]) && trim($_GET["move_to_folder"]) !== ""){
				require_once("../../../conf/mail_data.php");
				
				$current_dir = trim($_GET["dir"]);
				$msgid = trim($_GET["id"]);
				$destination_folder = trim($_GET["move_to_folder"]);
				if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $current_dir . "", $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
					if(@imap_mail_copy($inbox, $msgid, $destination_folder, CP_MOVE)){
						print "ok";
					} else {
						print imap_last_error();
					}
					imap_expunge($inbox);
					imap_close($inbox);
				} else {
					print imap_last_error();
				}
			} else {
				print "no destination folder";
			}
		} else {
			print "no id";
		}
	} else {
		print "no dir";
	}
} else {
	print "no user";
}
?>