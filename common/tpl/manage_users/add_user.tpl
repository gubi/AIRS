<?php
/**
* Generates form for add user
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

if (!isset($_POST["save_user_btn"])){
	$level = $pdo->query("select * from `airs_users_level` where level > '0'");
	if($level->rowCount() > 0){
		$user_level = "<select name=\"user_level\" style=\"width: 200px;\">";
		while ($dato_level = $level->fetch()){
			if ($dato_level["level"] == 1){
				$selected = "selected";
			} else {
				$selected = "";
			}
			$user_level .= "<option value=\"" . $dato_level["level"] . "\" " . $selected . ">" . $dato_level["level_txt"] . "</option>";
		}
		$user_level .= "</select>";
	}
	$content_body .= <<<Add_user_form
	<script type="text/javascript">
	function check_email(input){
		if (input.value.length > 3){
			$.get("common/include/funcs/_ajax/check_existing_email.php", {email: input.value}, function(data){
				if(data == "false"){
					input.setCustomValidity("Questo indirizzo e-mail esiste gia`");
					$.webshims.validityAlert.showFor(input, "Questo indirizzo e-mail esiste gia`'");
				} else {
					input.setCustomValidity("");
				}
			});
		}
	}
	</script>
	<form method="post" action="" onsubmit="">
		<table cellspacing="10" cellpadding="10" style="width: 100%;">
			<tr>
				<td style="width: 128px;">
					<img src="common/media/img/user_half_plus_128_ccc.png" />
				</td>
				<td>
					<table class="card" cellspacing="5" cellpadding="2">
						<tr>
							<td>
								<input type="text" name="user_name" placeholder="Nome" required="required" value="" />
							</td>
							<td></td>
						</tr>
						<tr>
							<td>
								<input type="text" name="user_lastname" placeholder="Cognome" required="required" value="" />
							</td>
							<td></td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td colspan="2" style="border-top: 0px none;">
								<input type="email" name="user_email" placeholder="Indirizzo e-mail" oninput="check_email(this)" required="required" value="" />
							</td>
						</tr>
						<tr>
							<th>Livello di accesso</th>
							<td>
								$user_level
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="save_user_btn" value="Crea" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
Add_user_form;
} else {
	$check_inserted = $pdo->query("select * from `airs_users` where `email` = '" . addslashes($_POST["user_email"]) . "'");
	if($check_inserted->rowCount() == 0){
		$name = ucfirst(strtolower(trim($_POST["user_name"])));
		$lastname = ucfirst(strtolower(trim($_POST["user_lastname"])));
		
		$add_user = $pdo->prepare("insert into `airs_users` (`name`, `lastname`, `email`, `level`, `created_by`) values(?, ?, ?, ?, ?)");
		$add_user->bindParam(1, addslashes($name));
		$add_user->bindParam(2, addslashes($lastname));
		$add_user->bindParam(3, addslashes($_POST["user_email"]));
		$add_user->bindParam(4, addslashes($_POST["user_level"]));
		$add_user->bindParam(5, addslashes($GLOBALS["decrypted_user"]));
		if (!$add_user->execute()) {
			$content_body = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
		} else {
			require_once("common/include/funcs/mail.send.php");
			require_once("common/include/funcs/_blowfish.php");
			
			$invitor_data = $pdo->query("select `name` from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
			while ($dato_invitor = $invitor_data->fetch()){
				$invitor_name = ucwords($dato_invitor["name"]);
			}
			$to = ucwords($_POST["user_name"] . " " . $_POST["user_lastname"]) . " <" . $_POST["user_email"] . ">";
			$key = $rsa_encrypted;
			$token = PMA_blowfish_encrypt($_POST["user_email"] . "~" . date("d-m-Y H:i:s"), $key);
			
			if (substr($name, -1) == "a"){
				$was_txt = "stata";
				$invited_txt = "invitata";
				$interested_txt = "interessata";
			} else {
				$was_txt = "stato";
				$invited_txt = "invitato";
				$interested_txt = "interessato";
			}
				$subject = "Sei " . $was_txt . " " . $invited_txt . " ad iscriverti al sistema AIRS";
			$text = "Ciao " . $name . ",\n";
			$text .= "ottime notize, " . $invitor_name . " ti ha " . $invited_txt . " ad iscriverti al Sistema!\n";
			$text .= "Iscrivendoti potrai usufruire di molte funzionalità sicuramente utili al tuo lavoro, tra cui istruire un crawler che automatizza e indicizza le tue ricerche, scansionare e acquisire ciclicamente feed RSS, indicizzare Newsletter, gestire conferenze e altro ancora...\n";
			$text .= "A seguire i passi per procedere:\n\n";
			$text .= "1. Segui il link in basso e incolla nell'apposito campo il _codice di verifica_;\n\n";
			$text .= "2. Completa i campi richiesti, e se necessario correggi quelli preriempiti (nome e cognome);\n";
			$text .= "   I campi aggiuntivi richiesti sono:\n";
			$text .= "   * Dati relativi al nuovo account (username e password);\n";
			$text .= "   * La nuova chiave di cifratura (chiave e commento).\n\n";
			$text .= "   Maggiori informazioni riguardo alla chiave di cifratura potrai trovarle qui: https://airs.inran.it/Sicurezza/Chiave_di_cifratura\n\n";
			$text .= "3. Con un po' di pazienza, attendi che il Sistema raccolga la necessaria entropia per generare la chiave,\n";
			$text .= "   quindi procedi pure con il login.\n\n";
			$text .= "Per accettare l'invito, infine, segui il collegamento in basso e incolla il codice di verifica a seguire:\n\n";
			$text .= "> " . str_repeat("-", 100) . "\n";
			$text .= "> *CODICE DI VERIFICA*: " . $token . " \n\n";
			$text .= "> " . str_repeat("-", 100) . "\n";
			$text .= "> *LINK PER LA CONFERMA*: https://airs.inran.it/Sistema/Conferma_iscrizione\n";
			$text .= "> " . str_repeat("-", 100) . "\n\n\n";
			$text .= "Se invece non sei " . $interested_txt . ", basta che trascuri questa e-mail: dopodomani (fra 48 ore) questo invito non sarà più valido, ed eventualmente ne sarà necessario uno nuovo.\n";
			$text .= "Beh, a presto allora!";
				$html = "Ciao " . $name . ",<br />";
				$html .= "ottime notize, " . $invitor_name . " ti ha " . $invited_txt . " ad iscriverti al Sistema!<br />";
				$html .= "Iscrivendoti potrai usufruire di molte funzionalit&agrave; sicuramente utili al tuo lavoro, tra cui istruire un crawler che automatizza e indicizza le tue ricerche, scansionare e acquisire ciclicamente feed RSS, indicizzare Newsletter, gestire conferenze e altro ancora...<br />";
				$html .= "A seguire i passi per procedere:<br />";
				$html .= "<ul>";
				$html .="	<ol>";
				$html .= "<li>Segui il link in basso e incolla nell'apposito campo il <u>codice di verifica</u>;</li>";
				$html .= "<li>Completa i campi richiesti, e se necessario correggi quelli preriempiti (nome e cognome);<br />";
				$html .= "I campi aggiuntivi richiesti sono:<br />";
				$html .= "<ul>";
				$html .= "<li>Dati relativi al nuovo account (username e password);</li>";
				$html .= "<li>La nuova chiave di cifratura (chiave e commento).</li>";
				$html .= "</ul><br />";
				$html .= 'Maggiori informazioni riguardo alla chiave di cifratura potrai trovarle qui: <a href="https://airs.inran.it/Sicurezza/Chiave_di_cifratura">https://airs.inran.it/Sicurezza/Chiave_di_cifratura</a></li>';
				$html .= "<li>Con un po' di pazienza, attendi che il Sistema raccolga la necessaria entropia per generare la chiave, quindi procedi pure con il login.</li>";
				$html .= "</ol><br />";
				$html .= "<center>* * *</center><br />";
				$html .= "Per accettare l'invito, infine, segui il collegamento in basso e incolla il codice di verifica a seguire:<br /><br />";
				$html .= '<table cellpadding="5" cellspacing="5" align="center" style="border: #ccc 1px solid; border-right: #666 15px solid; border-left: #666 15px solid;">';
				$html .= '<tr><td align="right"><b>CODICE DI VERIFICA</b>:</td><td><code>' . $token . '</code></td></tr>';
				$html .= '<tr><td align="right"><b>LINK PER LA CONFERMA</b>:</td><td><a href="https://airs.inran.it/Sistema/Conferma_iscrizione">https://airs.inran.it/Sistema/Conferma_iscrizione</a></td></tr>';
				$html .= "</table>";
				$html .= "<br /><br />";
				$html .= "Se invece non sei " . $interested_txt . ", basta che trascuri questa e-mail: dopodomani (fra 48 ore) questo invito non sar&agrave; pi&ugrave; valido, ed eventualmente ne sar&agrave; necessario uno nuovo.<br />";
				$html .= "Beh, a presto allora!";
			
			ob_start();
			@send_mail($to, $subject, $text, $html);
			$send_mail_status = ob_get_clean();
			
			if(trim($send_mail_status) == "OK"){
				$content_title = "Invito effettuato!";
				$content_subtitle = $name . " è stato invitato ad iscriversi al sistema";
				$result_body = "È stata spedita un'e-mail con l'invito per la registrazione, e il sistema è in attesa che $name accetti, proseguendo con l'iscrizione.<br />";
				$result_body .= "I dati inseriti nel modulo precedente sono stati salvati, ma bisogna tener conto che l'invito è valido 15 giorni, termine dopo il quale saranno automaticamente rimossi.<br /><br />";
				$result_body .= "<ul><li><a href=\"./Sistema/Gestione_utenti/Aggiungi_utente\">Aggiungi un altro utente</a></li>";
				$result_body .= "<li><a href=\"./Sistema/Gestione_utenti\">Torna alla gestione degli utenti</a></li></ul>";
				$content_body = utf8_decode($result_body);
			} else {
				print $send_mail_status;
			}
			
			// Rimuove i valori POST
			foreach($_POST as $k => $v){
				unset($_POST[$k]);
			}
		}
	} else {
		redirect("./Sistema/Gestione_utenti");
	}
}

?>