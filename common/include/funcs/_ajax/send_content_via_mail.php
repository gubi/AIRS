<?php
/**
* Send content-list in row format via e-mail
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
require_once("../_converti_data.php");
require_once("../mail.send.php");

if (isset($_GET["what"]) && trim($_GET["what"]) !== "" && isset($_GET["where"]) && trim($_GET["where"]) !== ""){
	$pdo = db_connect("");
	$chronology = $pdo->query("select * from airs_" . addslashes($_GET["where"]) . " where `id` = '" . addslashes($_GET["the_id"]) . "'");
	if ($chronology->rowCount() > 0){
		while ($dato_chronology = $chronology->fetch()){
			if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){
				require_once("../_blowfish.php");
				$decrypted_user = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);
				$user = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
				if ($user->rowCount() > 0){
					while($dato_user = $user->fetch()){
						$name = $dato_user["name"];
						$lastname = $dato_user["lastname"];
						$email = $dato_user["email"];
					}
				}
				$to = $name . " " . $lastname . " <" . $email . ">";
				$subject = "Cronologia della pagina \"" . $dato_chronology["name"] . "\" del " . converti_data(date("d F Y", strtotime($dato_chronology["date"])), "it", "month_first", "");
				
				$text = ucfirst(strtolower($name)) . ",\nhai inoltrato una richiesta di invio di una versione della cronologia della pagina \"" . $dato_chronology["name"] . "\".\nA seguire tutti i dati disponibili:\n\n" .  str_repeat("-", 100) . "\n\n";
				$text .= "> *Titolo*: ". utf8_decode(stripslashes($dato_chronology["title"])) . "\n";
				$text .= "> *Sottotitolo*: ". utf8_decode(stripslashes($dato_chronology["subtitle"])) . "\n";
				$text .= "> *Corpo*:\n>> " . str_replace("\n", "\n>> ", utf8_decode(stripslashes($dato_chronology["body"]))) . "\n> \n";
				$text .= ">*Ragione della modifica*: " . utf8_decode(stripslashes($dato_chronology["reason"])) . "\n";
				$text .= ">*Utente che ha effettuato le modifiche*: " . utf8_decode(stripslashes($dato_chronology["user"])) . "\n";
				$text .= ">*Data*: " . converti_data(date("d F Y \a\l\l\e H:i:s", strtotime($dato_chronology["date"])), "it", "month_first", "") . "\n\n" . str_repeat("-", 100) . "\n\n";
				//print $subject . "\n\n" . $text;
				
				send_mail($to, $subject, $text);
			}
		}
	}
}
?>