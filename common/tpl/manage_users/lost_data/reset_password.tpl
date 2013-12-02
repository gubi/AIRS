<?php
/**
* Generates form for reset password
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

if(!isset($_POST["recover_username_btn"])){
	if(!isset($_POST["confirm_subscription_btn"])){
		$content_body .= <<<Form_ceck_registration
		<form method="post" action="" onsubmit="">
			<table cellspacing="10" cellpadding="10" style="width: 100%;">
				<tr>
					<td style="width: 128px;">
						<img src="common/media/img/user_profile_settings_128_ccc.png" />
					</td>
					<td>
						<br />
						<table class="card" cellspacing="5" cellpadding="2">
							<tr>
								<td colspan="2" style="border-top: 0px none;">
									<input type="text" name="token" autocomplete="off" required="required" placeholder="Codice di verifica" value="" style="width: 75%;" tabindex="1" />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="confirm_subscription_btn" value="Crea" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
Form_ceck_registration;
	} else {
		require_once("common/tpl/manage_users/lost_data/form_reset_password.tpl");
		$content_body = utf8_decode($content_body);
	}
} else {
	require_once("common/include/funcs/_blowfish.php");
	
	$key = $rsa_encrypted;
	$token = PMA_blowfish_decrypt($_POST["token"], $key);
	
	$parsed_token = explode("~", $token);
	$email = $parsed_token[0];
	$encrypted_key = PMA_blowfish_encrypt($_POST["key"], $key);
	$encrypted_pass = PMA_blowfish_encrypt($_POST["user_password"], $encrypted_key);
	
	$edit_password = $pdo->prepare("update `airs_users` set `password` = '" . addslashes($encrypted_pass) . "' where `email` = '" . addslashes($email) . "' and `encryption_key` = '" . addslashes($encrypted_key) . "'");
	if (!$edit_password->execute()) {
		$content_body = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		redirect("./Speciale:Accedi");
	}
}
?>