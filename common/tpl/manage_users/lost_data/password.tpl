<?php
/**
* Generates form for recover password
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
	require_once("common/tpl/manage_users/lost_data/form_password.tpl");
} else {
	require_once("common/include/funcs/_blowfish.php");
	$key = $config["system"]["key"];
	$encrypted_key = PMA_blowfish_encrypt($_POST["key"], $key);
	$token = PMA_blowfish_encrypt($_POST["email"] . "~" . date("d-m-Y H:i:s"), $key);
	$rand_time = rand(5, 10);
	
	$check_username = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($_POST["username"]) . "' and `email` = '" . addslashes($_POST["email"]) . "' and `encryption_key` = '" . addslashes($encrypted_key) . "'");
	if($check_username->rowCount() > 0){
		while($dato_check_username = $check_username->fetch()){
			require_once("common/include/funcs/mail.send.php");
			
			$name = strtolower($dato_check_username["name"]);
			if (substr($name, -1) == "a"){
				$redirected_txt = "reindirizzata";
			} else {
				$redirected_txt = "reindirizzato";
			}
			$to = $dato_check_username["name"] . " " . $dato_check_username["lastname"] . " <" . $dato_check_username["email"] . ">";
			$subject = "Recupero password per l'accesso al sistema AIRS";
			
			$text = ucwords(strtolower($dato_check_username["name"])) . ",\nhai richiesto il recupero della password per accedere al sistema AIRS.\n";
			$text .= "Per resettare la password fai click su questo collegamento: http://airs.inran.it/Sistema/Reset_password\n";
			$text .= "e inserisci il seguente codice di verifica:\n\n";
			$text .= ">> " . $token . "\n\n\n";
			$text .= "Ti verrà richiesto di digitare la passphrase personale.\n\n";
			$text .= "* Se non ricordi la passphrase segui questo link per ricreare una chiave di cifratura: http://airs.inran.it/Sistema/Passphrase_dimenticata\n";
			$text .= "* Per maggiori informazioni riguardo alla chiave di cifratura: http://airs.inran.it/Sicurezza/Chiave_di_cifratura\n";
			
			ob_start();
			$send_mail_status = send_mail($to, $subject, $text);
			$output = ob_get_clean();
			$rand_time = rand(5, 10);
			
			if(trim($output) == "OK"){
				$content_title = "Controlla la posta";
				$content_subtitle = "I dati inseriti erano corretti";
				$content_body = ucwords(strtolower($dato_check_username["name"])) . ", ti è stata spedita un'e-mail all'indirizzo \"" . $dato_check_username["email"] . "\" con i passi da seguire per reimpostare la password.<br /><br />";
				$content_body .= "Fra meno di 10 secondi verrai " . $redirected_txt . " alla pagina di recupero password";
				header("refresh: $rand_time; url=./Reset_password");
			}
		}
	} else {
		$content_body = "<span style=\"color: #ff0000;\">Nessun utente associato con i dati forniti.<br />Prova a ricontrollare l'indirizzo e-mail.</span><br /><br />";
		require_once("common/tpl/manage_users/lost_data/form_password.tpl");
	}
}
require_once("common/include/conf/replacing_object_data.php");
?>