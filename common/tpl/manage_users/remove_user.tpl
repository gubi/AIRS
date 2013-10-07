<?php
/**
* Generates form for remove user
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

if (!isset($_POST["remove_user_btn"])){
	$level = $pdo->query("select * from `airs_users_level` where level > '0'");
	if($level->rowCount() > 0){
		$user_level = "<select name=\"user_level\" id=\"user_level\" style=\"width: 200px;\">";
		while ($dato_level = $level->fetch()){
			if ($dato_level["level"] == 1){
				$selected = "selected";
			} else {
				$selected = "";
			}
			$user_level .= "<option value=\"" . $dato_level["level"] . "\" " . $selected . ">" . ucfirst($dato_level["level_txt"]) . "</option>";
			$levels[$dato_level["level"]] = $dato_level["level_txt"];
		}
		$user_level .= "</select>";
	}
	$user = $pdo->query("select * from `airs_users` where `username` != '" . addslashes($GLOBALS["decrypted_user"]) . "' order by `lastname` asc");
	if($user->rowCount() > 0){
		$users = "<select name=\"user_id\" id=\"user_id\" style=\"width: 90%;\">";
		while ($dato_user = $user->fetch()){
			if(strlen($dato_user["username"]) > 0){
				$username = " (" . ucfirst($dato_user["username"]) . ")";
			} else {
				$username = "";
			}
			$users .= "<option value=\"" . $dato_user["id"] . "\">" . ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"])) . "" . $username . " ~ " . $levels[$dato_user["level"]] . "</option>";
		}
		$users .= "</select>";
	}
	$content_body .= <<<remove_user_form
	<b><u>Attenzione:</u></b> Una volta premuto il pulsante "Rimuovi l'utente sar&agrave; rimosso definitivamente
	<br />
	<br />
	<form method="post" action="" onsubmit="">
		<table cellspacing="10" cellpadding="10" style="width: 100%;">
			<tr>
				<td style="width: 128px;">
					<img src="common/media/img/user_half_cancel_128_ccc.png" />
				</td>
				<td>
					<table class="card" cellspacing="5" cellpadding="2">
						<tr>
							<th>Utente</th>
							<td>
								$users
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" name="remove_user_btn" value="Rimuovi" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
remove_user_form;
} else {
	$remove_user = $pdo->prepare("delete from `airs_users` where `id` = '" . addslashes($_POST["user_id"]) . "'");
	$remove_user->bindParam(1, addslashes($_POST["user_level"]));
	if (!$remove_user->execute()) {
		$content_body = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		$user_data = $pdo->query("select * from `airs_users` where `id` = '" . addslashes($_POST["user_id"]) . "'");
		if($user_data->rowCount() > 0){
			while ($dato_user = $user_data->fetch()){
				$name = "<b>" . ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"])) . "</b>";
				
				$level_data = $pdo->query("select * from `airs_users_level` where `level` = '" . addslashes($dato_user["level"]) . "'");
				if($level_data->rowCount() > 0){
					while ($dato_level = $level_data->fetch()){
						$level_name = "<b>" . strtolower($dato_level["level_txt"]) . "</b>";
					}
				}
			}
		}
		$content_title = "Utente rimosso!";
		$content_subtitle = "L'utente è stato rimosso dalla memoria del sistema";
		$result_body = "L'utente $name con i permessi di $level_name è stato rimosso dal sistema.<br /><br />";
		$result_body .= "<ul><li><a href=\"./Sistema/Gestione_utenti/Rimuovi_utente\">Rimuovi un altro utente</a></li>";
		$result_body .= "<li><a href=\"./Sistema/Gestione_utenti\">Torna alla gestione degli utenti</a></li></ul>";
		$content_body = utf8_decode($result_body);
		
		// Rimuove i valori POST
		foreach($_POST as $k => $v){
			unset($_POST[$k]);
		}
	}
}

?>