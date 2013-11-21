<?php
/**
* Rename a Mailbox dir
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
		$current_dir = trim($_GET["dir"]);
		$current_dir_arr = explode(".", trim($_GET["dir"]));
		$current_dir_name = $current_dir_arr[count($current_dir_arr) - 1];
		
		if(isset($_GET["new"]) && trim($_GET["new"]) !== "" && trim($_GET["new"]) !== $current_dir_name){
			require_once("../../../conf/mail_data.php");
			
			array_pop($current_dir_arr);
			$connection_dir = implode(".", $current_dir_arr);
			$current_dir_arr[] = trim($_GET["new"]);
			$new_dir = implode(".", $current_dir_arr);
			
			if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $connection_dir, $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
				if(@imap_renamemailbox($inbox, "{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "}" . $current_dir, "{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "}" . $new_dir)){
					print "ok";
				} else {
					print imap_last_error();
				}
				imap_close($inbox);
			} else {
				print imap_last_error();
			}
		} else {
			print "ok";
		}
	} else {
		print "no id";
	}
} else {
	print "no user";
}
?>