<?php
/**
* Flag a message to read/unread
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
	if(isset($_GET["msg_id"]) && trim($_GET["msg_id"]) !== ""){
		require_once("../../../conf/mail_data.php");
		
		if(isset($_GET["check_dir"]) && strlen(trim($_GET["check_dir"])) > 0){
			$mail_mailbox_name = $_GET["check_dir"];
		} else {
			$mail_mailbox_name = $GLOBALS["mail_mailbox_name"];
		}
		if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $mail_mailbox_name . "", $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
			switch($_GET["type"]){
				case "read":
					$status = imap_clearflag_full($inbox, $_GET["msg_id"], '\\Unseen');
					$status = imap_setflag_full($inbox, $_GET["msg_id"], '\\Seen');
					break;
				case "unread":
					$status = imap_clearflag_full($inbox, $_GET["msg_id"], '\\Seen');
					$status = imap_setflag_full($inbox, $_GET["msg_id"], '\\Unseen');
					break;
			}
			$count_unseen = 0;
			$MC = imap_check($inbox);
			$result = imap_fetch_overview($inbox, "1:{$MC->Nmsgs}", 0);
			foreach ($result as $overview) {
				$headers = imap_headerinfo($inbox, $overview->msgno);
				if(trim($headers->Unseen) == "U"){
					$count_unseen++;
				}
			}
			print "ok:" . $count_unseen;
			imap_close($inbox);
		} else {
			print imap_last_error();
		}
	} else {
		print "no id";
	}
} else {
	print "no user";
}
?>