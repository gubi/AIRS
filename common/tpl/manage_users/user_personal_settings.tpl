<?php
/**
* Generates form for edit personal data
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

$select_user = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
if ($select_user->rowCount() > 0){
	while($dato_user = $select_user->fetch()){
		$user_id = $dato_user["id"];
		$user_birthdate = ($dato_user["birth"] == "0000-00-00" ? "" : $dato_user["birth"]);
		$user_username = $dato_user["username"];
		$user_email = $dato_user["email"];
		if(strpos($dato_user["mail_account"], "@")){
			$splitted_mail = explode("@", $dato_user["mail_account"]);
			$mail_account = $splitted_mail[0];
		} else {
			$mail_account = $dato_user["mail_account"];
		}
		$mail_account_passwd = PMA_blowfish_decrypt($dato_user["mail_password"], $_COOKIE["iack"]);
		$user_newsletter_frequency = $dato_user["newsletter_frequency"];
		$user_session_length = $dato_user["session_length"];
	}
}
$select_frequency = $pdo->query("select * from `airs_automation_frequency` where `is_only_one` = '0'");
if ($select_frequency->rowCount() > 0){
	$input_frequency = "<select name=\"user_update_frequency\">";
	while($dato_frequency = $select_frequency->fetch()){
		if ($user_newsletter_frequency !== $dato_frequency["type"]){
			$checked = "";
		} else {
			$checked = "selected=\"selected\"";
		}
		$input_frequency .= "<option id=\"" . $dato_frequency["type"] . "\" name=\"frequecy\" ". $checked . " value=\"" . $dato_frequency["type"] . "\" />" . $dato_frequency["frequency_txt"] . "</option>";
	}
	$input_frequency .= "</select>";
}

$content_body = <<<User_settings
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<link rel="stylesheet" type="text/css" media="screen, projection" href="{ABSOLUTE_PATH}common/js/fd-slider.mhtml.min.css" />
<script src="{ABSOLUTE_PATH}common/js/fd-slider.min.js"></script>
<script type="text/javascript">
function save_data(){
	loader("Salvataggio dei dati...", "show");
	$.post("{ABSOLUTE_PATH}common/include/funcs/_ajax/manage_users/save_user_data.php", $("#user_settings_form").serialize(),
	function(data){
		var response = data.split(":");
		if (response[0] == "error"){
			apprise(response[1]);
			loader("", "hide");
		} else {
			if (response[0] == "edited"){
				apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Dati salvati con successo.</h1></td></tr></table>", {'animate': true}, function(r){
					if (r){
						loader("", "hide");
					}
				});
			}
		}
	});
	return false;
}
$(function(){
	$("#user_session_length").change(function(){
		$("#user_session_length_label").text($(this).val());
	});
	$.datepicker.regional['it'] = {
		closeText: 'Chiudi',
		prevText: '« Prec',
		nextText: 'Succ »',
		currentText: 'Oggi',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
		dateFormat: 'yy-mm-dd',
		firstDay: 1,
		autoSize: true,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['it']);
	$(".datepicker").datepicker();
});
</script>
<form id="user_settings_form" action="" method="post" onsubmit="return save_data(); return false;">
	<table cellpadding="5" cellspacing="5" style="width: 100%;">
		<tr>
			<td>
				<fieldset>
					<legend>Dati personali</legend>
					<table cellpadding="5" cellspacing="5">
						<tr>
							<th valign="top">
								<label for="user_name">Data di nascita:</label>
							</th>
							<td>
								Questo campo &egrave; assolutamente personale e non sar&agrave; visualizzato agli altri utenti.<br />
								La sua unica utilit&agrave; &egrave; quella di attivare il conteggio dei giorni di vita (<i>complegiorno</i>) e la visualizzazione dei <a href="http://en.wikipedia.org/wiki/Biorhythm" target="_blank" title="Vai alla pagina di Wikipedia (en)">bioritmi<sup>(en)</sup></a> nella pagina principale.<br />
								<input type="hidden" id="user_id" name="user_id" value="$user_id" />
								<input type="text" id="user_birthdate" name="user_birthdate" class="datepicker" placeholder="Data di nascita" value="$user_birthdate" />
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<th valign="top">
								<label for="user_email_account">Account e-mail legato all'utente:</label>
							</th>
							<td>
								Completando questo parametro si potr&agrave; utilizzare l'editor di posta integrato nel sistema e inviare messaggi e-mail firmati e/o cifrati grazzie alla chiave di cifratura.<br />
								<input type="text" id="user_email_account" name="user_email_account" placeholder="utente" value="$mail_account" style="text-align: right;" />@inran.it
							</td>
						</tr>
						<tr>
							<th>
								<label for="user_email_password">Password relativa all'account di posta:</label>
							</th>
							<td>
								<input type="password" id="user_email_password" name="user_email_password" value="$mail_account_passwd" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>
				<fieldset>
					<legend>Dati relativi all'account</legend>
					<table cellpadding="5" cellspacing="5">
						<tr>
							<th>
								<label for="user_username">Username:</label>
							</th>
							<td>
								<input type="text" id="user_username" name="user_username" placeholder="Username" required="required" value="$user_username" />
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>
				<fieldset>
					<legend>Preferenze di sessione</legend>
					<table cellpadding="5" cellspacing="5" style="width: 100%;">
						<tr>
							<th>
								Frequenza di ricezione aggiornamenti:
							</th>
							<td>
								$input_frequency
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<th>
								<label for="user_password">Durata della sessione di login:</label>
							</th>
							<td>
								<table style="width: 50%;" cellspacing="5" cellpadding="5">
									<tr>
										<td>
											<script>
											fdSlider.createSlider({
												inp: document.getElementById("user_session_length_label"),
												step: 1800, 
												maxStep: 18000,
												min: 3600,
												max: 18000,
												animation:"tween",
												classNames: "v-s2"
											});
											</script>
										</td>
										<td>
											<input type="number" id="user_session_length_label" name="user_session_length" value="$user_session_length" min="3600" max="18000" maxlength="6 style="width: 50px !important;" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
		<tr>
			<td>
				<input type="submit" name="save_personal_settings_btn" id="save_personal_settings_btn" value="Salva" />
			</td>
		</tr>
	</table>
</form>
User_settings;

require_once("common/include/conf/replacing_object_data.php");
?>