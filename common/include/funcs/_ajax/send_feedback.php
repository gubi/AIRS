<?php
/**
* Send user froodback to developers
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
require_once("../mail.send.php");

if (isset($_GET["page"]) && trim($_GET["page"]) !== "" && isset($_GET["user"]) && isset($_GET["comment"]) && trim($_GET["comment"]) !== ""){
	$logpdo = db_connect("system_logs");
	$query_add = $logpdo->prepare("insert into `airs_users_feedback` (`page`, `user`, `comment`, `data`, `ora`) values(?, ?, ?, ?, ?)");
	$query_add->bindParam(1, addslashes(urldecode($_GET["page"])));
	$query_add->bindParam(2, addslashes($_GET["user"]));
	$query_add->bindParam(3, addslashes(urldecode($_GET["comment"])));
	$query_add->bindParam(4, date("Y-m-d"));
	$query_add->bindParam(5, date("H:i:s"));
	if (!$query_add->execute()) {
		print "Non posso inserire il log della mail.\n";
		print "Codice errore: " . $query_add->errorCode() . "\n";
		print "Informazioni: " . join(", ", $query_add->errorInfo()) . "\n";
	} else {
		$pdo = db_connect("");
		$developers = $pdo->query("select * from `airs_users` where `level` = '3'");
		if($developers->rowCount() > 0){
			while($dato_dev = $developers->fetch()){
				$to = $dato_dev["name"] . " " . $dato_dev["lastname"] . " <" . $dato_dev["email"] . ">";
				$subject = "Un utente ha segnalato un bug";
				if(strlen($_GET["user"]) > 0){
					$an_user = $dato_dev["username"];
				} else {
					$an_user = "un utente";
				}
				$text = "Ciao " . ucfirst(strtolower($dato_dev["name"])) . ",\n" . $an_user . " ha segnalato un bug nel sistema.\nA seguire tutti i dati disponibili:\n\n" .  str_repeat("-", 100) . "\n\n";
				$text .= "> *URI*: ". urldecode($_GET["page"]) . "\n";
				$text .= "> *Messaggio dell'utente*: \n>> ". stripslashes(str_replace("\n", "\n>> ", urldecode($_GET["comment"]))) . "\n\n" . str_repeat("-", 100) . "\n\n";
				//print $subject . "\n\n" . $text;
				
				ob_start();
				$send_mail_status = @send_mail($to, $subject, $text);
				$output = ob_get_clean();
				
				print trim($output);
			}
		}
	}
}
?>