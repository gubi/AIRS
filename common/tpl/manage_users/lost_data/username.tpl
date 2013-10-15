<?php
/**
* Generates form for recover username
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
* @package	AIRS_Manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
if(!isset($_POST["captcha_res"]) || $_POST["captcha_res"] !== $_COOKIE["rs"]){
	$random_no = rand(0, 99999);
	
	if($_POST["captcha_res"] !== $_COOKIE["res"]){
		$content_body = "<span style=\"color: #ff0000;\">Il risultato del captcha non è giusto!</span><br /><br />";
	}
	require_once("common/tpl/manage_users/lost_data/form_username.tpl");
} else {
	require_once("common/include/funcs/_blowfish.php");
	$key = $config["system"]["key"];
	$encrypted_key = PMA_blowfish_encrypt($_POST["key"], $key);
	$encrypted_pass = PMA_blowfish_encrypt($_POST["password"], $encrypted_key);
	
	$check_username = $pdo->query("select * from `airs_users` where `email` = '" . addslashes($_POST["email"]) . "' and `password` = '" . addslashes($encrypted_pass) . "' and `encryption_key` = '" . addslashes($encrypted_key) . "'");
	if($check_username->rowCount() > 0){
		while($dato_check_username = $check_username->fetch()){
			require_once("common/include/funcs/mail.send.php");
			
			$to = $dato_check_username["name"] . " " . $dato_check_username["lastname"] . " <" . $dato_check_username["email"] . ">";
			$subject = "Username per l'accesso al sistema AIRS";
			
			$text = ucwords(strtolower($dato_check_username["name"])) . ",\nhai richiesto il recupero dello username per accedere al sistema AIRS\n";
			$text .= "Il tuo username altro non è che \"" . $dato_check_username["username"] . "\"\n\n";
			
			ob_start();
			$send_mail_status = send_mail($to, $subject, $text);
			$output = ob_get_clean();
			$rand_time = rand(5, 10);
			
			if(trim($output) == "OK"){
				$content_title = "Controlla la posta";
				$content_subtitle = "I dati inseriti erano corretti";
				$content_body = ucwords(strtolower($dato_check_username["name"])) . ", ti è stata spedita un'e-mail all'indirizzo \"" . $dato_check_username["email"] . "\" con il tuo username.<br />";
				$content_body .= "Per favore, controlla la posta e ritorna ad effettuare l'accesso anche con i dati mancanti.<br /><br />";
				$content_body .= "Fra meno di 10 secondi questa pagina ritornerà alla pagina di accesso";
				header("refresh: $rand_time; url=./Speciale:Accedi");
			}
		}
	} else {
		$content_body = "<span style=\"color: #ff0000;\">Nessun utente associato con i dati forniti.<br />Prova a ricontrollare l'indirizzo e-mail.</span><br /><br />";
		require_once("common/tpl/manage_users/lost_data/form_username.tpl");
	}
}
require_once("common/include/conf/replacing_object_data.php");
?>