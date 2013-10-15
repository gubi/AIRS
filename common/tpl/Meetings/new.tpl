<?php
/**
* Generates form for create meeting
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
* @package	AIRS_Meetings
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

if (isset($_POST["save_meeting_btn"])){
	$content_body = "<ul>";
	foreach($_POST as $k => $v){
		$content_body .= "<li><b>" . $k . "</b>: " . $v . "</li>";
	}
	$content_body .= "</ul>";
	
	require_once("common/include/lib/bbb-api-php/includes/bbb-api.php");
	$bbb = new BigBlueButton();
	
	$pdom = db_connect("meetings");
	$last_meeting = $pdom->query("select max(`id`) as 'maxid' from `current_meetings`");
	if($last_meeting->rowCount() > 0){
		while($dato_meeting = $last_meeting->fetch()){
			$last_usable_id = $dato_meeting["maxid"] + 1;
		}
	}
	if(strlen($_POST["user_password"]) == 0){
		$user_password = "BBBuserPwd";
	} else {
		$user_password = $_POST["user_password"];
	}
	if($_POST["maxParticipants"] == "0"){
		$maxParticipants = "-1";
	} else {
		$maxParticipants = $_POST["maxParticipants"];
	}
	$logout_url = $absolute_path . "Meeting/" . get_link($_POST["meeting_name"]);
	$creationParams = array(
		"meetingId" => $last_usable_id,
		"meetingName" => $_POST["meeting_name"],
		"attendeePw" => $user_password,
		"moderatorPw" => $_POST["admin_password"],
		//"welcomeMsg" => urlencode($_POST["meeting_welcome_msg"]),
		"dialNumber" => $_POST["dialNumber"],
		"voiceBridge" => $_POST["voiceBridge"],
		"webVoice" => $_POST["webVoice"], // NULL at this time
		"logoutUrl" => $logout_url,
		"maxParticipants" => (int)$maxParticipants,
		"record" => "false",
		"duration" => (int)$_POST["duration"]
	);
	$itsAllGood = true;
	try {
		$result = $bbb->createMeetingWithXmlResponseArray($creationParams);
	} catch (exception $e) {
		$content_title = $i18n["no_meeting"];
		$meeting_body = str_replace("{PROBLEM}", $e->getMessage(), $i18n["meeting_problem"]);
		$meeting_img = "chat_cancel_128_ccc.png";
		$itsAllGood = false;
	}
	if ($itsAllGood == true) {
		if ($result == null) {
			$content_title = $i18n["no_meeting"];
			$meeting_body = $i18n["no_bbb_connection"];
			$meeting_img = "chat_cancel_128_ccc.png";
		} else {
			if ($result['returncode'] == "SUCCESS") {
				if($creationParams["duration"] == "0") {
					$end_time = "";
				} else {
					$end_time = date("Y-m-d H:i:s", strtotime("+" . $creationParams["duration"] . " minute"));
				}
				$check_exists_meeting = $pdom->query("select * from `current_meetings` where `name` like '" . addslashes($_POST["meeting_name"]) . "%'");
				if($check_exists_meeting->rowCount() > 0){
					$meeting_name = str_replace(array("{NAME}", "{1}"), array($creationParams["meetingName"], ($check_exists_meeting->rowCount() + 1)), $i18n["meeting_session_txt"]);
					$logout_url = $absolute_path . "Meeting/" . get_link(str_replace(array("{NAME}", "{1}"), array($creationParams["meetingName"], ($check_exists_meeting->rowCount() + 1)), $i18n["meeting_session_txt"]));
				} else {
					$meeting_name = $creationParams["meetingName"];
				}
				$add_current_meeting = $pdom->prepare("insert into `current_meetings` (`id`, `name`, `description`, `attendeePW`, `moderatorPW`, `dialNumber`, `voiceBridge`, `webVoice`, `logoutURL`, `maxParticipants`, `duration`, `end_date`, `user`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
					$add_current_meeting->bindParam(1, $last_usable_id);
					$add_current_meeting->bindParam(2, addslashes($meeting_name));
					$add_current_meeting->bindParam(3, addslashes($_POST["meeting_desc"]));
					$add_current_meeting->bindParam(4, addslashes($user_password));
					$add_current_meeting->bindParam(5, addslashes($creationParams["moderatorPw"]));
					$add_current_meeting->bindParam(6, addslashes($creationParams["dialNumber"]));
					$add_current_meeting->bindParam(7, addslashes($creationParams["voiceBridge"]));
					$add_current_meeting->bindParam(8, addslashes($creationParams["webVoice"]));
					$add_current_meeting->bindParam(9, addslashes($logout_url));
					$add_current_meeting->bindParam(10, addslashes($creationParams["maxParticipants"]));
					$add_current_meeting->bindParam(11, addslashes($creationParams["duration"]));
					$add_current_meeting->bindParam(12, $end_time);
					$add_current_meeting->bindParam(13, addslashes($decrypted_user));
				if ($add_current_meeting->execute()){
					/*if($creationParams["record"] == "true"){
						$add_recorded_meeting = $pdom->prepare("insert into `recorded_meetings` (`parent_id`) values(?)");
						$add_recorded_meeting->bindParam(1, $last_usable_id);
						$add_recorded_meeting->execute();
					}*/
					$content_title = $i18n["meeting_creation_success_title"];
							$parameters["fullName"] = $decrypted_user;
							$parameters["meetingID"] = $last_usable_id;
							$parameters["password"] = $creationParams["moderatorPw"];
							$construct_url = http_build_query($parameters);
							$checksum = sha1("join" . $construct_url . "06a7ff02211a31d2e01fe1f646733677");
						$url = "{ABSOLUTE_PATH90}bigbluebutton/api/join?". $construct_url . "&checksum=" . $checksum;
					$meeting_body = str_replace(array("{NAME}", "{_NAME_}", "{URL}"), array($meeting_name, get_link($meeting_name), $url), $i18n["meeting_creation_success_content"]);
					$meeting_img = "chat_accept_128_ccc.png";
				}
			} else {
				$meeting_body = $i18n["meeting_creation_failed"];
				$meeting_img = "chat_cancel_128_ccc.png";
			}
		}
	}
	$content_body = <<<Meeting_data
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px" valign="top">
				<img src="common/media/img/$meeting_img" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				$meeting_body<br />
			</td>
		</tr>
	</table>
Meeting_data;
	require_once("common/include/conf/replacing_object_data.php");
} else {
	$pdo = db_connect("");
	$users_levels_query = $pdo->query("select * from `airs_users_level`");
	
	$user_levels = '<select name="login_level" style="width: 200px;">';
	while($dato_level = $users_levels_query->fetch()){
		if($dato_level["level"] == "0"){
			$checked = ' checked="checked"';
		} else {
			$checked = "";
		}
		$user_levels .= '<option value="' . $dato_level["level"] . '"' . $checked . '>' . $dato_level["level_txt"] . '</option>';
	}
	$user_levels .= "</select><br /><br />";
	$content_body = <<<New_page_form
	<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
	<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/wiki/style.css" />
	<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
	<script type="text/javascript">
	$(function(){
		myWikiSettings = {
			nameSpace: "wiki",
			previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
			previewAutoRefresh: true,
			markupSet:  [
				{name:'Grassetto', key:'B', openWith:"'''", closeWith:"'''", className:'bold'}, 
				{name:'Corsivo', key:'I', openWith:"''", closeWith:"''", className:'italic'}, 
				{name:'Sottolineato', key:'U', openWith:'__', closeWith:'__', className:'underline'}, 
				{name:'Barrato', key:'S', openWith:'@@--- ', closeWith:' @@', className:'strokethrough'}, 
				{separator:'---------------' },
				{name:'Titolo 1', key:'1', openWith:'= ', closeWith:' =', className:'h1'},
				{name:'Titolo 2', key:'2', openWith:'== ', closeWith:' ==', className:'h2'},
				{name:'Titolo 3', key:'3', openWith:'=== ', closeWith:' ===', className:'h3'},
				{name:'Titolo 4', key:'4', openWith:'==== ', closeWith:' ====', className:'h4'},
				{name:'Titolo 5', key:'5', openWith:'===== ', closeWith:' =====', className:'h5'},
				{separator:'---------------' }, 
				{name:'Elenco puntato', openWith:'(!(* |!|*)!)', className:'ul'}, 
				{name:'Elenco numerato', openWith:'(!(# |!|#)!)', className:'ol'}, 
				{separator:'---------------' },
				{name:'Crea tabella', className:'tablegenerator', placeholder:"Inserisci del testo qui",
					replaceWith:function(h) {
						var cols = prompt("Inserire il numero di colonne"),
							rows = prompt("Inserire il numero di righe"),
							html = "||";
						if (h.altKey) {
							for (var c = 0; c < cols; c++) {
								html += "! [![TH"+(c+1)+" text:]!]";
							}	
						}
						for (var r = 0; r < rows; r++) {
							if (r > 0){
								html += "||\\n||";
							}
							for (var c = 0; c < cols; c++) {
								if (c > 0){
									html += "||";
								}
								html += (h.placeholder||"");
							}
						}
						html += "||";
						return html;
					},
				className:'tablegenerator'},
				{name:'Immagine', key:'P', replaceWith:'[![Url:!:http://]!] [![name]!]', className:'image'},
				{name:'Collegamento libero', openWith:'(([![Collegamento:!:]!]|', closeWith:'))', placeHolder:'Testo del collegamento', className:'internal_link'},
				{name:'Collegamento InterWiki', openWith:'[[![Canale wiki:!:Wikipedia]!]:[![Lingua (sigla ISO_3166-1):!:it]!]:', closeWith:']', placeHolder:'Testo del collegamento', className:'interwiki_link'},
				{name:'Collegamento esterno', openWith:'[[![URI (Uniform Resource Locator):!:http://]!] ', closeWith:']', placeHolder:'Testo del collegamento', className:'external_link'},
				{separator:'---------------' },
				{name:'Citazione', openWith:'(!(> |!|>)!)', className:'quote'},
				{name:'Codice', openWith:'(!(<code type="[![Linguaggio:!:php]!]">\\n|!|<pre>)!)', closeWith:'(!(\\n</code>|!|</pre>)!)', className:'code'}, 
				{separator:'---------------' },
				{name:'Anteprima', call:'preview', className:'preview'}
			]
		};
		$("#meeting_desc").markItUp(myWikiSettings);
		$("#meeting_name").focus();
	});
	</script>
	<form method="post">
		<table cellpadding="10" cellspacing="10" class="frm">
			<tr>
				<td style="width: 128px" style="width: 100%;" valign="top">
					<img src="common/media/img/chat_plus_128_ccc.png" />
				</td>
				<td>
					<h1>Modulo di creazione di un nuovo meeting</h1>
					<br />
					<br />
					<fieldset>
						<legend class="edit">Dati relativi all'evento</legend>
						<table class="frm" cellspacing="5" cellpadding="5">
							<tr>
								<td>
									<label for="meeting_name">Nome del Meeting*</label>
								</td>
								<td>
									<input type="text" name="meeting_name" id="meeting_name" value="" required="required" placeholder="Nome del Meeting" style="width: 75%;" />
								</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="meeting_desc">Descrizione dell'evento</label>
								</td>
								<td>
									<textarea name="meeting_desc" id="meeting_desc" style="height: 100px;" placeholder="Questa descrizione sar&agrave; utilizzata per creare il contenuto della relativa pagina"></textarea>
								</td>
							</tr>
							<!--<tr><td>&nbsp;</td></tr>
							<tr>
								<td valign="top">
									<label for="meeting_welcome_msg">Messaggio di benvenuto</label>
								</td>
								<td>
									<textarea name="meeting_welcome_msg" id="meeting_welcome_msg" placeholder="Un messaggio di benvenuto che viene mostrato nella finestra della chat quando l'utente entra nella stanza." style="width: 99%;"></textarea><br />
									&Egrave; possibile includere parole chiave quali <tt>%%CONFNAME%%</tt>, <tt>%%DIALNUM%%</tt> e <tt>%%CONFNUM%%</tt>.<br /><br />
								</td>
							</tr>-->
							<tr><td colspan="2">&nbsp;</td></tr>
							<tr>
								<td valign="top">
									<label for="duration">Durata</label>
								</td>
								<td>
									<input type="number" min="0" max="100" size="4" name="duration" id="duration" value="0" /><br />
									Durata dell'evento in minuti.<br />
									Se impostato su <tt>0</tt> non si avr&agrave; limite di tempo.<br /><br />
								</td>
							</tr>
							<tr>
								<td>
									<label for="maxParticipants">Numero massimo di partecipanti</label>
								</td>
								<td>
									<input type="number" min="0" max="100" size="4" name="maxParticipants" id="maxParticipants" value="20" placeholder="Numero di partecipanti" />
								</td>
							</tr>
							<!--<tr>
								<td>
									Registrazione evento
								</td>
								<td>
									<label>
										<input type="radio" name="record" value="true" /> Attiva
									</label><br />
									<label>
										<input type="radio" name="record" value="false" checked="checked" /> Non attiva
									</label>
								</td>
							</tr>-->
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="label">Integrazione <acronym title="Voice Over IP">VoIP</acronym></legend>
						<table class="frm" cellspacing="5" cellpadding="5">
							<tr>
								<td valign="top">
									<label for="dialNumber">Numero di telefono</label>
								</td>
								<td>
									<input type="text" name="dialNumber" id="dialNumber" value="" placeholder="Numero di telefono" /><br />
									Un numero di telefono da associare all'evento, chiamando il quale sar&agrave; possibile partecipare in chiamata VoIP.<br /><br />
								</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="voiceBridge">PIN di accesso</label>
								</td>
								<td>
									<input type="number" name="voiceBridge" id="voiceBridge" maxlength="5" size="5" name="voiceBridge" value="" placeholder="12345" /><br />
									Un PIN per abilitare l'accesso in chiamata VoIP.
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="key">Impostazioni di sicurezza</legend>
						<table class="frm" cellspacing="5" cellpadding="5">
							<tr>
								<td>
									<label for="login_level">Livello di accesso*</label>
								</td>
								<td>
									$user_levels
								</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="admin_password">Password moderatore*</label>
								</td>
								<td>
									<input type="password" name="admin_password" id="admin_password" value="" required="required" placeholder="Password amministratore" style="width: 25%;" /><br />
									Password per accedere alla conferenza come moderatore.<br /><br />
								</td>
							</tr>
							<tr>
								<td valign="top">
									<label for="user_password">Password utente</label>
								</td>
								<td>
									<input type="text" name="user_password" id="user_password" value="" placeholder="Password utente" /><br />
									(Opzionale) La password richiesta ai partecipanti per accedere alla conferenza.<br />
									Se non impostata, la conferenza sar&agrave; di accesso pubblico.
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
		<hr />
		<table cellpadding="0" cellspacing="0" style="width: 100%;">
			<tr>
				<td>
					<input type="submit" name="save_meeting_btn" id="save_meeting_btn" value="Crea" />
				</td>
			</tr>
		</table>
	</form>
New_page_form;
	require_once("common/include/conf/replacing_object_data.php");
}
?>