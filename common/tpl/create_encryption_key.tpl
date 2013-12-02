<?php
/**
* Generates new PGP key form
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

$end = false;
if (isset($_POST["create_new_key_btn"])){
	require_once("common/include/funcs/_blowfish.php");
	$key = $rsa_encrypted;
	$encrypted_pass = PMA_blowfish_encrypt($_POST["password"], $_COOKIE["iack"]);
	
	if ($_POST["newkey"] == $_POST["key"]){
		$check_user_key = $pdo->query("select * from airs_users where `password` = '" . addslashes($encrypted_pass) . "'");
		if ($check_user_key->rowCount() > 0){
			$crypted_user_key = PMA_blowfish_encrypt($_POST["newkey"], $key);
			$crypted_user_pass = PMA_blowfish_encrypt($_POST["password"], $crypted_user_key);
			
			while ($dato_check_user_key = $check_user_key->fetch()){
				$user_id = $dato_check_user_key["id"];
			}
			$query_update = $pdo->prepare("update airs_users set `encryption_key` = '" . addslashes($crypted_user_key) . "', `password` = '" . addslashes($crypted_user_pass) . "' where `username` = '" . addslashes($decrypted_user) . "'");
			if (!$query_update->execute()) {
				require_once("common/tpl/__mail_not_sent.tpl");
			} else {
				setcookie("iac", "null", time() - 9999, "/");
				header("Location: " . $absolute_path . $GLOBALS["referer_page"]);
				$end = true;
			}
		} else {
			$error_msg_pass = "<span class=\"error\">La password inserita non è valida</span>";
			$error_msg_key = "";
		}
	} else {
		$error_msg_key = "<span class=\"error\">Le chiavi non corrispondono</span>";
		$error_msg_pass = "";
	}
}
if ($end == false){
	if ($GLOBALS["user_level"] > 0){
		$check_user_key = $pdo->query("select encryption_key from airs_users where `username` = '" . addslashes($decrypted_user) . "' and `encryption_key` != ''");
		if ($check_user_key->rowCount() > 0){
			$modifying_msg = "<b>ATTENZIONE: risulta che tu abbia già una chiave.</b><br />Compilando questo modulo resetterai e ne genererai una nuova<br /><br />";
		} else {
			$modifying_msg = "";
		}
		$content_body = <<<New_key
		Attualmente il sistema genera unicamente una chiave criptata in modo da poter abilitare l'accesso.<br />
		Per successivi sviluppi consultare la pagina sulle <a href="Sicurezza/Chiave_di_cifratura#Implementazioni future">future implementazioni</a><br />
		<br />
		$modifying_msg
		<b>Al termine dell'operazione sarà necessario ripetere il login con la nuova chiave</b><br />
		<br />
		<br />
		<form action="" method="post">
			<fieldset>
				<legend>Dati per l'accesso</legend>
				<table cellspacing="5" cellpadding="5" style="width: 100%;">
					<tr>
						<th><img src="common/media/img/document_sans_security_64_ccc.png" /></th>
						<td>
							<input type="password" size="36" class="key" name="newkey" id="newkey" value="" placeholder="Nuova chiave di cifratura" required="required" autocomplete="off" /> $error_msg_key<br /><br />
							<input type="password" size="36" name="key" id="key" value="" placeholder="Ripeti chiave di cifratura" required="required" autocomplete="off" />
						</td>
					</tr>
					<tr><td>&nbsp;</td></tr>
					<tr>
						<th><label for="password">Password</label></th>
						<td>
							<input type="password" name="password" id="password" value="" required="required" autocomplete="off" /> $error_msg_pass
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="create_new_key_btn" value="Crea" />
						</td>
					</tr>
				</table>
			</fieldset>
		</form>
New_key;
		require_once("common/include/conf/replacing_object_data.php");
	} else {
		require_once("common/tpl/__401.tpl");
	}
}
?>