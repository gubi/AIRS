<?php
/**
* Generates form for confirm account
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

if(!isset($_COOKIE["iac"])){
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
								<td>
									<input type="text" name="token" autocomplete="off" required="required" placeholder="Codice di verifica" value="" style="width: 75%;" />
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="confirm_subscription_btn" value="Conferma" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
Form_ceck_registration;
	} else {
		require_once("common/include/funcs/_blowfish.php");
		
		$key = $config["system"]["key"];
		$token = PMA_blowfish_decrypt($_POST["token"], $key);
		
		$parsed_token = explode("~", $token);
		$token_date = explode(" ", $parsed_token[1]);
		
		$datetime1 = new DateTime($token_date[0]);
		$datetime2 = new DateTime(date("d-m-Y"));
		$interval = $datetime1->diff($datetime2);
		if ($interval->format("%a") <= 15){
			$check_pre_registration = $pdo->query("select * from `airs_users` where `is_active` = '0' and `email` = '" . addslashes($parsed_token[0]) . "'");
			if($check_pre_registration->rowCount() > 0){
				// Next step
				$expiretime = time()+60*60 * 24 * 15;
				setcookie("ias", PMA_blowfish_encrypt($parsed_token[0] . "~" . $token_date[0], $key), $expiretime, "/");
				redirect("./Sistema/Iscrizione");
			}
		} else {
			require_once("common/tpl/_expired_invitation.tpl");
		}
	}
} else {
	require_once("common/tpl/__no_login.tpl");
}
?>