<?php
/**
* Login/out users
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
require_once("common/include/funcs/_blowfish.php");

$crypted_user_key = $_COOKIE["iack"];
// Se c'è già il cookie setta il livello utente
if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){
	$decrypted_user = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);
	$GLOBALS["decrypted_user"] = $decrypted_user;
	
	$select_level = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "' and `is_active` = '1'");
	if ($select_level->rowCount() > 0){
		while($dato_user_level = $select_level->fetch()){
			$GLOBALS["user_level"] = $dato_user_level["level"];
			$GLOBALS["connection_count"] = $dato_user_level["connection_count"] + 1;
			$GLOBALS["name"] = ucwords($dato_user_level["name"]);
			$GLOBALS["logout_redirect"] = $dato_user_level["logout_redirect"];
		}
	}
} else {
	$GLOBALS["user_level"] = 0;
}
if ($GLOBALS["function_page"] == $i18n["page_name_special_logout"]){
	// salva l'uscita
	$logpdo = db_connect("system_logs");
	$query_add = $logpdo->prepare("insert into `airs_login` (`user`, `ip`, `data`, `ora`, `referer`, `action`) values(?, ?, ?, ?, ?, ?)");
	$query_add->bindParam(1, addslashes($GLOBALS["decrypted_user"]));
	$query_add->bindParam(2, $_SERVER['REMOTE_ADDR']);
	$query_add->bindParam(3, date("Y-m-d"));
	$query_add->bindParam(4, date("H:i:s"));
	$query_add->bindParam(5, addslashes($GLOBALS["referer_page"]));
	$query_add->bindValue(6, "logout");
	if (!$query_add->execute()) {
		print $i18n["error_no_logout_log"] . ".<br />";
		print $i18n["error_error_code_string"] . ": " . $query_add->errorCode() . "<br />";
		print $i18n["info_string"] . ": " . join(", ", $query_add->errorInfo()) . "<br />";
		exit();
	} else {
		$query_update = $pdo->prepare("update `airs_users` set `is_connected` = '0' where `username` = '" . addslashes($GLOBALS["decrypted_user"]) . "'");
		if (!$query_update->execute()) {
			print $i18n["error_no_logout_log"] . ".<br />";
			print $i18n["error_error_code_string"] . ": " . $query_update->errorCode() . "<br />";
			print $i18n["info_string"] . ": " . join(", ", $query_update->errorInfo()) . "<br />";
			exit();
		} else {
			/*
			// Inserisce la notifica
			$titolo_notifica = $dato_check["user"] . " si &egrave; disconnesso";
			$testo_notifica = "L'utente <b>" . $dato_check["user"] . "</b>  &egrave; uscito dal sistema";
			
			$query_add = "insert into airs_notifications (`title`, `text`, `la_data`, `ora`) values('" . $titolo_notifica . "', '" . addslashes($testo_notifica) . "', '" . date("Y-m-d") . "', '" . date("H:i") . "')";
			$ri_add = mysql_db_query( $database, $query_add, $conn );
			if (!$ri_add) {
				print mysql_error() . "\n\n" . $query_add;
				exit();
			}
			*/
			setcookie("iac", "null", time() - 9999, "/", ".airs.inran.it");
			setcookie("iack", "null", time() - 9999, "/", ".airs.inran.it");
			if($GLOBALS["logout_redirect"] == "main") {
				header("Location: " . $absolute_path);
			} else {
				header("Location: " . $absolute_path . $GLOBALS["referer_page"]);
			}
		}
	}
}
if (isset($_POST["username"]) && trim($_POST["username"]) !== "" && isset($_POST["password"]) && trim($_POST["password"]) !== ""){
	// Testa se ci sono cookie del login
	// se ci sono redireziona direttamente alla pagina members 
	if(isset($_COOKIE["iac"])){
		//header("Location: " . $GLOBALS["current_uri"]);
	}
	
	$encrypted_key = PMA_blowfish_encrypt($_POST["key"], $config["system"]["key"]);
	
	$select_check = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($_POST["username"]) . "' and `is_active` = '1'");
	if ($select_check->rowCount() == 0){
		print $i18n["error_wrong_user_pass"] . ".<br />";
		//require_once("common/tpl/___login_error1.tpl");
	} else {
		while ($dato_check = $select_check->fetch()){
			$_POST["password"] = stripslashes($_POST["password"]);
			$dato_check["password"] = stripslashes($dato_check["password"]);
			
			// Riporta l'errore se la password è sbagliata
			if ($_POST["password"] != PMA_blowfish_decrypt($dato_check["password"], $encrypted_key)) {
			//if ($_POST["password"] != $dato_check['password']) {
				//header("Location: Speciale:Accedi/Errore_2");
				//print PMA_blowfish_encrypt($_POST["password"], $config["system"]["key"]);
				//print "k: " . $encrypted_key . " <br />p: " . PMA_blowfish_encrypt($_POST["password"], $encrypted_key) . "<br />";
				//print "Non posso inserire il log dell'uscita.<br />";
				print $i18n["error_wrong_user_pass"] . ".<br />";
				//require_once("common/tpl/___login_error2.tpl");
				//exit();
			} else {
				if ($dato_check["encryption_key"] != $encrypted_key){
					//header("Location: Speciale:Accedi/Errore_2");
					require_once("common/tpl/___login_error3.tpl");
				} else {
					// Riporta l'errore se l'utente non è stato attivato
					
					// se il login e' ok aggiunge il cookie
					$log_error_type = "";
					
					$crypted_user = PMA_blowfish_encrypt($_POST["username"], $encrypted_key);
					$_POST["username"] = stripslashes($_POST["username"]);
					$hour = time() + $dato_check["session_length"];
					
					// iac = INRAN AIRS COOKIE
					setcookie("iac", $crypted_user, $hour, "/", ".airs.inran.it");
					setcookie("iack", $encrypted_key, $hour, "/", ".airs.inran.it");
					
					// salva l'accesso
					$logpdo = db_connect("system_logs");
					$query_add = $logpdo->prepare("insert into `airs_login` (`user`, `ip`, `data`, `ora`, `referer`, `action`) values(?, ?, ?, ?, ?, ?)");
					$query_add->bindParam(1, addslashes($_POST["username"]));
					$query_add->bindParam(2, $_SERVER["REMOTE_ADDR"]);
					$query_add->bindParam(3, date("Y-m-d"));
					$query_add->bindParam(4, date("H:i:s"));
					$query_add->bindParam(5, addslashes($GLOBALS["referer_page"]));
					$query_add->bindValue(6, "login");
					if (!$query_add->execute()) {
						print $i18n["error_no_login_log"] . ".<br />";
						print $i18n["error_error_code_string"] . ": " . $query_add->errorCode() . "<br />";
						print $i18n["info_string"] . ": " . join(", ", $query_add->errorInfo()) . "<br />";
						exit();
					} else {
						$query_check = $pdo->query("select `connection_count` from `airs_users` where username = '" . addslashes($_POST["username"]) . "'");
						while($dato_check = $query_check->fetch()){
							$GLOBALS["connection_count"] = $dato_check["connection_count"] + 1;
						}
						$query_update = $pdo->prepare("update `airs_users` set `is_connected` = '1', `connection_count` = '" . $GLOBALS["connection_count"] . "' where username = '" . addslashes($_POST["username"]) . "'");
						if (!$query_update->execute()) {
							print $i18n["error_cannot_update_user_status"] . ".<br />";
							print $i18n["error_error_code_string"] . ": " . $query_add->errorCode() . "<br />";
							print $i18n["info_string"] . ": " . join(", ", $query_add->errorInfo()) . "<br />";
							print $connection_count;
							exit();
						} else {
							$_POST["password"] = md5($_POST["password"]);
							
							/*
							// Inserisce la notifica
							$titolo_notifica = $dato_check["username"] . " ha effettuato l'accesso";
							$testo_notifica = "L'utente <b>" . $dato_check["username"] . "</b>  ha appena effettuato l'accesso al sistema.";
							
							$query_add = "insert into airs_notifications (`title`, `text`, `la_data`, `ora`) values('" . addslashes($titolo_notifica) . "', '" . addslashes($testo_notifica) . "', '" . date("Y-m-d") . "', '" . date("H:i") . "')";
							$ri_add = mysql_db_query( $database, $query_add, $conn );
							if (!$ri_add) {
								print mysql_error() . "\n\n" . $query_add;
								exit();
							}
							// Cancella le sessioni dei giorni precedenti
							if ($dato_check["la_data"] !== date("Y-m-d")){
								$query_remove = "delete from airs_login_data where la_data != '" . date("Y-m-d") . "' and user = '" . $decrypted_user . "'";
								$ri_remove = mysql_db_query( $database, $query_remove, $conn );
								if (!$ri_remove) {
									print mysql_error();
									exit();
								}
							}
							*/
							if ($_POST["ref"] == $i18n["page_name_special_login"]){
								$ref = "";
							} else {
								$ref = $absolute_path. $_POST["ref"];
							}
							header("Location: " . $ref);
						}
					}
				}
			}
		}
	}
}
?>
