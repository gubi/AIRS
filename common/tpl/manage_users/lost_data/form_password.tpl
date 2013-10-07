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

$content_body .= <<<Password_recovery
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/jquery-cookie/jquery.cookie.js"></script>
<script type="text/javascript">
$(document).ready(function(){
	$("#captcha").parent().append("<input type=\"text\" name=\"captcha_res\" id=\"res\" style=\"vertical-align: 27px; width: 45px;\" maxlength=\"2\" value=\"\" tabindex=\"4\" />");
	$("#captcha").click(function(){
		var random_no = Math.random(99999);
		$(this).children().css({"display": "none"}).attr("src", "common/include/funcs/captcha.php?id=" + random_no).fadeIn(399);
		event.preventDefault();
	});
});
</script>
Una volta confermati username, indirizzo e-mail e chiave di cifratura verrà spedita un'e-mail al tuo indirizzo con i dati per proseguire.<br />
<br />
<form action="" method="post">
	<fieldset>
		<legend>Dati per l&apos;accesso</legend>
		<table cellspacing="5" cellpadding="5" class="frm">
			<tr>
				<th><label for="username">Username</label></th>
				<td>
					<input type="text" name="username" id="username" value="" autofocus="autofocus" required="required" tabindex="1" />
				</td>
			</tr>
			<tr>
				<th><label for="email">Indirizzo e-mail</label></th>
				<td>
					<input type="email" name="email" id="email" value="" tabindex="2" required="required" />
				</td>
				<td></td>
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
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th><img src="common/media/img/robot_64_ccc.png" /></th>
				<td>
					<a href="javascript: void(0);" id="captcha" title="Fai click per aggiornare l'immagine">
						<img style="padding: 5px;" src="common/include/funcs/captcha.php?id=$random_no" />
					</a>
				</td>
			</tr>
			<tr>
				<td colspan="2">				
					<input type="submit" name="recover_password_btn" value="Prosegui" />
				</td>
			</tr>
		</table>
	</fieldset>
</form>
Password_recovery;
?>