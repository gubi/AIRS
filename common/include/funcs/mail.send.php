<?php
/**
* Generate template and send e-mail
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
//header("Content-type: text/html; charset=utf-8");

function send_mail($to, $subject, $message, $html_message = null, $attachment = null){
	$config = parse_ini_file($_SERVER["DOCUMENT_ROOT"] . "common/include/conf/airs.conf", 1);
	
	//Richiede PEAR Mail e Mail-mime installati
	if(!class_exists("Mail")){
		include("Mail.php");
		include("Mail/mime.php");
	}
	$mail_id = uniqid("id");
	
	// Firma
	$text_namaste = "\n\nNamastè :)";
	$text_signature = $namaste . "\n-- =\n\n" . $config["company"]["mail_text_signature"] . "\n\n" . $config["company"]["license_txt"];
	if($html_message !== null){
		$html_signature = $config["company"]["mail_html_signature"];
		$html_template = preg_replace("#(<\?php.*?\?>)#s", "", file_get_contents($_SERVER["DOCUMENT_ROOT"] . "common/tpl/Mailbox/system_mail_html_template.tpl"));
		$html = str_replace(
			array(
				"{SUBJECT}",
				"{ABSOLUTE_URI}",
				"{MESSAGE}",
				"{SIGNATURE}",
				"{COPYRIGHT_DATA}",
				"<hr />"
			),
			array(
				$subject,
				$config["system"]["default_host_uri"],
				$html_message,
				$html_signature,
				$config["company"]["license_html"],
				'<center><img class="separator" src="' . $config["system"]["default_host_uri"] . 'common/media/img/menu_hr.png" /></center>'
			), 
			$html_template
		);
	}
	$crlf = "\n";
	
	$hdrs["Reply-To"] = $config["mail"]["Reply-To"];
	$hdrs["From"] = $config["mail"]["From"];
	$hdrs["Errors-To"] = $config["mail"]["Errors-To"];
	$hdrs["MIME-Version"] = "1.0";
	$hdrs["Subject"] = $subject;
	
	$textparams["text_charset"] = $config["mail"]["charset"];
	$textparams["content_type"] = "text/plain";
	$textparams["text_encoding"] = "quoted/printable";
	
	$htmlparams["charset"] = $config["mail"]["charset"];
	$htmlparams["content_type"] = "text/html";
	$htmlparams["encoding"] = "quoted/printable";
	
	//$mime = new Mail_mime(array("eol" => $crlf));
	$mime = new Mail_mime();
	if($html_message !== null){
		$db_body = $html_message . "\n\n---\n\n" . $message;
	} else {
		$db_body = $html_message;
	}
	$mime->setTXTBody(quoted_printable_decode($message));
	if($html_message !== null){
		$mime->setHTMLBody(quoted_printable_decode($html));
	}
	if($attachment !== null) {
		if(is_array($attachment)) {
			$file = $attachment[0];
			$content_type = $attachment[1];
			$file_name = $attachment[2];
		} else {
			if(is_file($attachment)) {
				$file = file_get_contents($attachment);
				$mime_type = mime_content_type($attachment);
			} else {
				$file = $attachment;
				$mime_type = "text/plain";
			}
			$info = pathinfo($attachment);
			$file_name = $info["basename"];
		}
		$mime->addAttachment ($file, $mime_type, $file_name, 0);
	}
	$body = $mime->get();
	$hdrs = $mime->headers($hdrs);
	$smtpinfo["host"] = $config["mail"]["smtp_host"];
	$smtpinfo["port"] = $config["mail"]["smtp_port"];
	$smtpinfo["auth"] = (($config["mail"]["smtp_auth"] == "true") ? true : false);
	$smtpinfo["username"] = $config["mail"]["smtp_username"];
	$smtpinfo["password"] = $config["mail"]["smtp_password"];
	$smtpinfo["debug"] = (($config["mail"]["debug"] == "true") ? true : false);
	
	$mail=& Mail::factory("smtp", $smtpinfo);
	$mail->send($to, $hdrs, $body);
	
	if (PEAR::isError($mail)) {
		print "FAILED: " . $mail->getMessage() . "\n";
	} else {
		$logpdo = db_connect("system_logs");
		$query_add = $logpdo->prepare("insert into `airs_mail` (`id`, `to`, `subject`, `body`, `data`, `ora`) values(?, ?, ?, ?, ?, ?)");
		$query_add->bindParam(1, addslashes($id));
		$query_add->bindParam(2, addslashes($to));
		$query_add->bindParam(3, addslashes($subject));
		$query_add->bindParam(4, addslashes(utf8_decode($db_body)));
		$query_add->bindParam(5, date("Y-m-d"));
		$query_add->bindParam(6, date("H:i:s"));
		if (!$query_add->execute()) {
			print "Non posso inserire il log della mail.\n";
			print "Codice errore: " . $query_add->errorCode() . "\n";
			print "Informazioni: " . join(", ", $query_add->errorInfo()) . "\n";
		} else {
			print "OK\n";
		}
	}
}
?>