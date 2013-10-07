<?php
/**
* Generates form require an invitation
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
	if (!isset($_POST["require_invitation_btn"])){
		$content_body .= <<<Add_user_form
		<form method="post" action="" onsubmit="">
			<table cellspacing="10" cellpadding="10" style="width: 100%;">
				<tr>
					<td style="width: 128px;">
						<img src="common/media/img/user_half_information_128_ccc.png" />
					</td>
					<td>
						<table class="card" cellspacing="5" cellpadding="2">
							<tr>
								<td>
									<input type="text" name="user_name" placeholder="Nome" required="required" value="" />
								</td>
							</tr>
							<tr>
								<td>
									<input type="text" name="user_lastname" placeholder="Cognome" required="required" value="" />
								</td>
							</tr>
							<tr>
								<td colspan="2" style="border-top: 0px none;">
									<input type="email" name="user_email" placeholder="Indirizzo e-mail" required="required" value="" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<textarea name="comment" placeholder="Commento" style="width: 50%; height: 75px;"></textarea>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<input type="submit" name="require_invitation_btn" value="Richiedi" />
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</form>
Add_user_form;
	} else {
		require_once("common/include/funcs/mail.send.php");
		
		$content_title = "Richiesta effettuata con successo!";
		$content_subtitle = "";
		$content_body = $_POST["user_name"] . ",<br />grazie per aver richiesto un invito alla registrazione al portale, che &egrave; stata effettuata con successo.<br />Il sistema ha provveduto ad inviare un'e-mail a tutti gli amministratori, che se lo riterranno opportuno ti attiveranno un invito.<br />Una volta invitato, riceverai un'e-mail di conferma con il codice di verifica, grazie al quale potrai proseguire con la registrazione e usufruire dei servizi forniti dal sistema AIRS.<br /><br />A presto!";
		
		$find_admin = $pdo->query("select * from `airs_users` where level >= '2'");
		while($dato_admin = $find_admin->fetch()){
			$to = ucwords(strtolower($dato_admin["name"] . " " . $dato_admin["lastname"])) . "<" . $dato_admin["email"] . ">";
			$subject = "Richiesta di invito ad AIRS da parte di un utente esterno";
			
			$text = ucfirst(strtolower($dato_admin["name"])) . ",\nun utente ha richiesto di essere invitato a registrarsi al portale.\n\nDi seguito sono riportati i suoi dati:\n\n";
			$text .= "> Nome: " . ucwords(strtolower($_POST["user_name"])) . "\n";
			$text .= "> Cognome: " . ucwords(strtolower($_POST["user_lastname"])) . "\n";
			$text .= "> Indirizzo e-mail: " . strtolower($_POST["user_email"]) . "\n";
			$text .= "> Commento:\n>> " . str_replace("\n", "\n>> ", $_POST["comment"]) . "\n";
			$text .= str_repeat("-", 100) . "\n\n";
			$text .= "Per accettare la richiesta di invito basta creare un nuovo utente inserendo i suoi dati, da questo indirizzo: http://airs.inran.it/Sistema/Gestione_utenti/Aggiungi_utente\n";
			
			ob_start();
			$send_mail_status = send_mail($to, $subject, $text);
			$output = ob_get_clean();
		}
	}
} else {
	require_once("common/tpl/__no_login.tpl");
}
?>