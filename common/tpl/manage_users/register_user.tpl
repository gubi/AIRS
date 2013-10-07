<?php
/**
* Generates form for invite user
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
	if (isset($_COOKIE["ias"])){
		require_once("common/include/funcs/_blowfish.php");
		$key = "inran_dev_2011";
		$decrypted_cookie = PMA_blowfish_decrypt($_COOKIE["ias"], $key);
		$parsed_cookie = explode("~", $decrypted_cookie);
		$email = $parsed_cookie[0];
		$date = $parsed_cookie[1];
		
		$datetime1 = new DateTime($date);
		$datetime2 = new DateTime(date("d-m-Y"));
		$interval = $datetime1->diff($datetime2);
		if ($interval->format("%a") <= 15){
			$check_subscriber = $pdo->query("select * from `airs_users` where `is_active` = '0' and `email` = '" . addslashes($email) . "'");
			if ($check_subscriber->rowCount() > 0){
				while($dato_subscriber = $check_subscriber->fetch()){
					$name = $dato_subscriber["name"];
					$lastname = $dato_subscriber["lastname"];
				}
			}
			$content_body = <<<Registration_form
			<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
			<script type="text/javascript">
			function check_username(input){
				if (input.value.length > 3){
					$.get("common/include/funcs/_ajax/check_existing_username.php", {user: input.value}, function(data){
						if(data == "false"){
							input.setCustomValidity("Questo username esiste gia`");
							$.webshims.validityAlert.showFor(input, "Questo username esiste gia`");
						} else {
							input.setCustomValidity("");
						}
					});
				}
			}
			function check_password(input) {
				if (input.value != document.getElementById("user_password").value) {
					input.setCustomValidity("Le password non sono identiche");
				} else {
					input.setCustomValidity("");
				}
				if (input.value.length < 6 && input.value == document.getElementById("user_password").value) {
					input.setCustomValidity("La password e` troppo corta e quindi poco sicura");
					$.webshims.validityAlert.showFor(input, "La password e` troppo corta e quindi poco sicura");
				}
			}
			function save_registration(){
				loader("<br />Generazione della chiave di cifratura in corso...<br />(può richiedere diverso tempo)", "show");
				$.post("common/include/funcs/_ajax/register_user.php", $("#registration_form form").serialize(), function(data){
					if(data == "OK"){
						window.location.replace("http://airs.inran.it/Speciale:Accedi");
					} else {
						apprise(data, {"noButton": false}, function(r){
							if(r){
								loader("", "hide");
							}
						});
					}
				});
				return false;
			}
			</script>
			<div id="registration_form">
				<form action="" method="post" onsubmit="return save_registration(); return false;">
					<table cellpadding="0" cellspacing="0" style="width: 100%;">
						<tr>
							<td>
								<fieldset>
									<legend class="edit">Dati personali</legend>
									<br />
									<table cellspacing="5" cellpadding="5" style="width: 100%;">
										<tr>
											<th>Nome</th>
											<td>
												<input type="text" id="user_name" name="user_name" placeholder="Nome" required="required" value="$name" />
											</td>
										</tr>
										<tr>
											<th>Cognome</th>
											<td>
												<input type="text" id="user_lastname" name="user_lastname" placeholder="Cognome" required="required" value="$lastname" />
											</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<fieldset>
									<legend class="author">Dati relativi all'account</legend>
									<br />
									<table cellspacing="5" cellpadding="5" style="width: 100%;">
										<tr>
											<th>Username</th>
											<td>
												<input type="text" autocomplete="off" id="user_username" name="user_username" oninput="check_username(this)" pattern="[A-Za-z0-9\.\-\_]{4,16}" maxlength="16" title="da 4 a 16 caratteri tra lettere, numeri e alcuni caratteri speciali (&quot;.&quot;, &quot;-&quot;, &quot;_&quot;)"  placeholder="Username" required="required" value="" />
											</td>
										</tr>
										<tr><td>&nbsp;</td></tr>
										<tr>
											<th>Password</th>
											<td>
												<input type="password" autocomplete="off" id="user_password" name="user_password" oninput="check_password(this)" placeholder="Password" required="required" value="" />
											</td>
										</tr>
										<tr>
											<th>Ripeti password</th>
											<td>
												<input type="password" autocomplete="off" id="user_password2" name="user_password2" oninput="check_password(this)" placeholder="Ripeti password" required="required" value="" />
											</td>
										</tr>
									</table>
								</fieldset>
								<br />
								<fieldset>
									<legend class="key">Chiave di cifratura</legend>
									<br />
									<a target="_blank" href="./Sicurezza/Chiave_di_cifratura">Perché la chiave di cifratura</a>
									<br />
									<br />
									<table cellspacing="5" cellpadding="5" style="width: 100%;">
										<tr>
											<td>
												<input type="text" autocomplete="off" id="key" name="user_key" size="36" placeholder="Chiave di cifratura" required="required" value="" />
											</td>
										</tr>
										<tr>
											<td>
												<textarea id="user_key_comment" name="user_key_comment" placeholder="Commento (opzionale)" style="width: 50%;"></textarea>
											</td>
										</tr>
									</table>
								</fieldset>
								<br />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="register_user_btn" id="save_feed_btn" value="Salva" />
							</td>
						</tr>
					</table>
				</form>
			</div>
Registration_form;
			$content_body = $content_body;

			require_once("common/include/conf/replacing_object_data.php");
		} else {
			require_once("common/tpl/_expired_invitation.tpl");
		}
	} else {
		redirect("./Sistema/Conferma_iscrizione");
	}
} else {
	require_once("common/tpl/__no_login.tpl");
}
?>