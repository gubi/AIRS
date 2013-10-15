<?php
/**
* Register an user to System
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
header("Content-type: text/plain; charset=utf-8;");

if (isset($_COOKIE["ias"])){
	require_once("../_blowfish.php");
	require_once("../mail.send.php");
	require_once("../../.mysql_connect.inc.php");
	
	$config = parse_ini_file("../../conf/airs.conf", 1);
	$key = $config["system"]["key"];
	$decrypted_cookie = PMA_blowfish_decrypt($_COOKIE["ias"], $key);
	$parsed_cookie = explode("~", $decrypted_cookie);
	$email = $parsed_cookie[0];
	$date = $parsed_cookie[1];
	
	$datetime1 = new DateTime($date);
	$datetime2 = new DateTime(date("d-m-Y"));
	$interval = $datetime1->diff($datetime2);
	if ($interval->format("%a") <= 15){
		chdir("../../lib/gnu_pgp");
		require_once("../../lib/gnu_pgp/process.php");
		
		$action = "Genera";
		$user_name = ucwords(strtolower($_POST["user_name"] . " " . $_POST["user_lastname"]));
		$user_email = $email;
		$comment = $_POST["user_key_comment"];
		$passphrase = $_POST["user_key"];
		$recipientName = $_POST["recipientName"];
		$recipientEmail = $_POST["recipientEmail"];
		$user_key_pubring = $_POST["user_key_pubring"];
		
		$gpg->userName = $user_name;
		$gpg->userEmail = $user_email;
		$gpg->recipientName = $recipientName;
		$gpg->recipientEmail = $recipientEmail;
		$gpg->message = $comment;
		
		if(trim($user_key_pubring) == "") {
			// Generate key
			$result = $gpg->gen_key($user_name, $comment, $user_email, $passphrase);
		} else {
			// Import key
			$result = $gpg->import_key($user_key_pubring);
		}
		if(!$result){
			print $gpg->error;
			exit();
		} else {
			$gpg->userEmail = $user_email;
			$result = $gpg->export_key();
			if(!$result){
				echo $gpg->error;
				exit();
			} else {
				$public_key = $gpg->public_key;
				
				$result = $gpg->list_keys();
				if(!$result){
					echo $gpg->error;
					exit();
				} else {
					$key_data = key_data($gpg->keyArray);
					//print_r($key_data);
					$pub = $key_data[1];
					$fpr = $key_data[2];
					$sub = $key_data[3];
					
					$fingerprint = join(" ", str_split(join(" ", str_split($fpr["user_id"], 4)), 25));
					
					$key_id = $pub["key_id"];
					foreach($pub as $key_k => $key_v){
						$key_table[] = "`key_" . str_replace("key_id", "id", $key_k) . "` = '" . mb_convert_encoding($key_v, "ASCII", "HTML-ENTITIES") . "'";
					}
				}
			}
			$kt = join(", ", $key_table);
			
			$encrypted_key = PMA_blowfish_encrypt($_POST["user_key"], $key);
			$encrypted_password = PMA_blowfish_encrypt($_POST["user_password"], $encrypted_key);
			
			$pdo = db_connect("");
			$edit_user = $pdo->prepare("update `airs_users` set `name` = '" . addslashes($_POST["user_name"]) . "', `lastname` = '" . addslashes($_POST["user_lastname"]) . "', `username`= '" . addslashes($_POST["user_username"]) . "', `password` = '" . addslashes($encrypted_password) . "', `encryption_key` = '" . addslashes($encrypted_key) . "', `is_active` = '1', " . $kt . " where `email` = '" . addslashes($user_email) . "'");
			if (!$edit_user->execute()) {
				print "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
			} else {
				setcookie("ias", "null", time() - 9999, "/");
				
				$to = $_POST["user_name"] . " " . $_POST["user_lastname"] . " <" . $email . ">";
				$subject = "Registrazione al sistema AIRS";
				
				if (substr($_POST["user_name"], -1) == "a"){
					$welcome_txt = "Benvenuta";
				} else {
					$welcome_txt = "Benvenuto";
				}
				$text = $welcome_txt . " nel sistema AIRS!.\n\n";
				$text .= "Il processo di registrazione è appena stato completato con successo e d'ora in avanti potrai accedere al sistema e usufruire delle sue funzionalità .\n";
				$text .= "Inoltre è stata creata la tua chiave di cifratura PGP, attraverso la quale potrai cifrare documenti ed e-mail, firmarli digitalmente e stabilire una tua rete di fiducia con altri utenti.\n\n";
				$text .= "Dettagli della tua chiave di cifratura:\n";
				$text .= str_repeat("-", 100) . "\n\n";
				$text .= "gpg: chiave " . $pub["key_id"] . " contrassegnata come di prossima fiducia\n";
				$text .= "chiavi pubbliche e segrete create e firmate.\n\n";
				$text .= $pub["type"] . "   " . $pub["length"] . "D/" . $pub["key_id"] ." " . $pub["creation_date"] . "\n";
				$text .= "      Key fingerprint = " . $fingerprint . "\n";
				$text .= "uid                  " . mb_convert_encoding($pub["user_id"], "ASCII", "HTML-ENTITIES") . "\n";
				$text .= $sub["type"] . "   " . $sub["length"] . "g/" . $sub["key_id"] ." " . $sub["creation_date"] . "\n\n";
				$text .= "gpg: chiave " . $pub["key_id"] . " contrassegnata come di prossima fiducia\n";
				$text .= "chiave pubblica e segreta create e firmate.\n\n";
				$text .= str_repeat("-", 100) . "\n\n";
				$text .= "A seguire la tua chiave pubblica:\n\n";
				$text .= $public_key;
				
				ob_start();
				$send_mail_status = send_mail($to, $subject, $text);
				$output = ob_get_clean();
				
				print trim($output);
			}
		}
	}
}
?>