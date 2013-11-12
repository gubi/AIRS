<?php
/**
* Force download of an attachment
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

function get_attached_file($inbox, $structure, $i, $msg_id, $fileName) {
	$encoding = $structure->parts[$i]->encoding;
	if($structure->parts[$i]->encoding == 3) { // 3 = BASE64
		$fileSource = base64_decode(imap_fetchbody($inbox, $msg_id, $i+1));
	} elseif($structure->parts[$i]->encoding == 4) { // 4 = QUOTED-PRINTABLE
		$fileSource = quoted_printable_decode(imap_fetchbody($inbox, $msg_id, $i+1));
	}
	$fp = fopen("../../../../../.tmp/files/" . $fileName, "w");
	fputs($fp, $fileSource);
	fclose($fp);
	$file = "../../../../../.tmp/files/" . $fileName;
        //download file
        header("Content-Description: File Transfer");
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=" . $fileName);
        header("Content-Transfer-Encoding: binary");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Pragma: public");
        header("Content-Length: " . filesize($file));
        ob_clean();
        flush();
	readfile($file);
	unlink($file);
}

$_GET = $_POST;
if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	if(isset($_GET["check_dir"]) && trim($_GET["check_dir"]) !== ""){
		if(isset($_GET["msg_id"]) && trim($_GET["msg_id"]) !== ""){
			if(isset($_GET["filename"]) && trim($_GET["filename"]) !== ""){
				require_once("../../../conf/mail_data.php");
				
				$connection_dir = trim($_GET["check_dir"]);
				$message_number = trim($_GET["msg_id"]);
				
				if($inbox = @imap_open("{" . $GLOBALS["mail_host"] . ":" . $GLOBALS["mail_port"] . "/" . $GLOBALS["mail_flag"] . "}" . $connection_dir, $GLOBALS["user_mail_account"], $GLOBALS["user_mail_pssword"])){
					$structure = imap_fetchstructure($inbox, $message_number);
					$attachments = array();
					if(isset($structure->parts) && count($structure->parts)) {
						for($i = 0; $i < count($structure->parts); $i++) {
							if($structure->parts[$i]->ifdparameters) {
								foreach($structure->parts[$i]->dparameters as $object) {
									if(strtolower($object->attribute) == "filename") {
										$attachments[$i]["is_attachment"] = true;
										$attachments[$i]["name"] = $object->value;
									}
								}
							}
							if($structure->parts[$i]->ifparameters) {
								foreach($structure->parts[$i]->parameters as $object) {
									if(strtolower($object->attribute) == "name") {
										$attachments[$i]["is_attachment"] = true;
										$attachments[$i]["name"] = $object->value;
									}
								}
							}
							
							if($attachments[$i]["name"] == $_POST["filename"]){
								get_attached_file($inbox, $structure, $i, $message_number, str_replace(" ", "_", $attachments[$i]["name"]));
							}
						}
					}
					//print_r($_POST);
					//print_r($attachments);
					
					imap_close($inbox);
				} else {
					print imap_last_error();
				}
			} else {
				print "no file";
			}
		} else {
			print "no message id";
		}
	} else {
		print "no directory";
	}
} else {
	print "no user";
}
?>