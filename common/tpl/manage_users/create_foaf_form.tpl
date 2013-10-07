<?php
if(!isset($_POST["next"])){
	$content_body .= <<<Submit_btn
	<form method="post" action="" style="border: 0px none; box-shadow: none;">
		<input type="submit" name="next" value="Prosegui &rsaquo;" />
	</form>
Submit_btn;
} else {
	$content_body = <<<FoafOmatic
	<script type="text/javascript" src="common/js/foaf-a-matic/foaf-a-matic.js"></script>
	<script type="text/javascript" src="common/js/foaf-a-matic/foaf.js"></script>
	<script type="text/javascript" src="common/js/foaf-a-matic/sha1.js"></script>
	<script type="text/javascript">
	function add_account_form() {
		var rowCount = $("#foaf-account tr").length,
		now_rowCount = rowCount + 1;
		$("#foaf-account").append('<tr><td><label for="foaf_p_account' + now_rowCount + '">Altro account:</label></td><td><input type="text" id="foaf_p_account' + now_rowCount + '" name="account' + now_rowCount + '" placeholder="Un tuo altro account" /> <button onclick="add_account_form()">+</button></td></tr>');
	}
	$(document).ready(function(){
		$("#foaf_p_title").focus();
	});
	</script>
	
	<form name="details" onsubmit="return false;">
		<fieldset>
			<legend>Dati personali</legend>
			<table cellspacing="10" cellpadding="5">
				<tr>
					<td valign="top">
						<table cellspacing="10" cellpadding="5">
							<tr>
								<td><label for="foaf_p_title">Titolo:</label></td>
								<td><input type="text" id="foaf_p_title" name="title" placeholder="Titolo personale" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_name">Nome:</label></td>
								<td><input type="text" id="foaf_p_name" name="firstName" placeholder="Il tuo nome" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_surname">Cognome:</label></td>
								<td><input type="text" id="foaf_p_surname" name="surname" placeholder="Il tuo cognome" /></td>
							</tr>
						</table>
					</td>
					<td class="separator">&nbsp;</td>
					<td valign="top">
						<table cellspacing="10" cellpadding="5" id="foaf-account">
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<fieldset>
			<legend>Dati relativi agli account</legend>
			<table cellspacing="10" cellpadding="5">
				<tr>
					<td valign="top">
						<table cellspacing="10" cellpadding="5">
							<tr>
								<td><label for="foaf_p_nick">Nick:</label></td>
								<td><input type="text" id="foaf_p_nick" name="nick" placeholder="Un tuo pseudonimo comune" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_openid">OpenID:</label></td>
								<td><input type="text" id="foaf_p_openid" name="OpenID" placeholder="Un tuo account OpenID" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_skype">Skype:</label></td>
								<td><input type="text" id="foaf_p_skype" name="skype" placeholder="Un tuo account Skype" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_msn">Messenger:</label></td>
								<td><input type="text" id="foaf_p_msn" name="msn" placeholder="Un tuo account Messenger" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_yahoo">Yahoo!:</label></td>
								<td><input type="text" id="foaf_p_yahoo" name="yahoo" placeholder="Un tuo account Yahoo!" /></td>
							</tr>
							<tr>
								<td><label for="foaf_p_jabber">Jabber:</label></td>
								<td><input type="text" id="foaf_p_jabber" name="jabber" placeholder="Un tuo account Jabber" /></td>
							</tr>
						</table>
					</td>
					<td class="separator">&nbsp;</td>
					<td valign="top">
						<table cellspacing="10" cellpadding="5" id="foaf-account">
							<tr>
								<td><label for="foaf_p_account1">Altro account:</label></td>
								<td><input type="text" id="foaf_p_account1" name="account1" placeholder="Un tuo altro account" />&nbsp;&nbsp;&nbsp;<button onclick="add_account_form()">+</button></td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
	</form>
FoafOmatic;
}
?>