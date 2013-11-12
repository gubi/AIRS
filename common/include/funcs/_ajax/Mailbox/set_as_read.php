<?php
/**
* Set an e-mail as read
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
		require_once("../../../conf/mail_data.php");
		
		$current_dir = trim($_GET["dir"]);
		if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $current_dir . "", $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
			$threads = @imap_thread($inbox);
			
			if(is_array($threads)){
				foreach ($threads as $key => $val) {
					$tree = explode(".", $key);
					if ($tree[1] == 'num') {
						$header = @imap_headerinfo($inbox, $val);
						$msg_id = $header->Msgno;
						
						$status = imap_clearflag_full($inbox, $header->Msgno, '\\Unseen');
						$status = imap_setflag_full($inbox, $header->Msgno, '\\Seen');
					}
				}
			}
			print "ok";
			imap_close($inbox);
		} else {
			print imap_last_error();
		}
	} else {
		print "no dir";
	}
} else {
	print "no user";
}
?>