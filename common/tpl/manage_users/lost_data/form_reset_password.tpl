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

$token = $_POST["token"];
$content_body .= <<<Reset_password
<script type="text/javascript">
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
</script>
Una volta confermate password e chiave di cifratura verrà spedita un'e-mail al tuo indirizzo con lo username.<br />
<br />
<form action="" method="post">
	<fieldset>
		<legend>Dati per l&apos;accesso</legend>
		<table cellspacing="5" cellpadding="5" class="frm">
			<tr>
				<th>Password</th>
				<td>
					<input type="hidden" name="token" value="$token" />
					<input type="password" autocomplete="off" id="user_password" name="user_password" oninput="check_password(this)" placeholder="Password" required="required" value="" tabindex="1" />
				</td>
			</tr>
			<tr>
				<th>Ripeti password</th>
				<td>
					<input type="password" autocomplete="off" id="user_password2" name="user_password2" oninput="check_password(this)" placeholder="Ripeti password" required="required" value="" tabindex="2" />
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th><img src="common/media/img/document_sans_security_64_ccc.png" /></th>
				<td>
					<input type="password" size="36" name="key" id="key" value="" placeholder="Chiave di cifratura" required="required" tabindex="3" autocomplete="off" /><br/>
					<br />
					<a style="margin: 10px 0;" href="Sicurezza/Chiave_di_cifratura" target="_blank">Perch&eacute; la chiave di cifratura</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">				
					<input type="submit" name="recover_username_btn" value="Prosegui" />
				</td>
			</tr>
		</table>
	</fieldset>
</form>
Reset_password;
?>