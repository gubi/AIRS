<?php
/**
* Generates list of user to disconnect
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

if (!isset($_POST["disconnect_user_btn"])){
	$content_body .= <<<Table_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function eject(id){
		$.get("common/include/funcs/_ajax/manage_users/eject_page.php", {page_id: id}, function(data){
			response = data.split("::");
			if(response[0] !== "error"){
				apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Pagina sganciata</h1><br />La pagina &quot;" + response[1] + "&quot; è stata sganciata con successo</td></tr></table>", {"noButton": false});
				$("#page_editing_list").flexReload();
			}
		});
	}
	function disconnect(username){
		$.get("common/include/funcs/_ajax/manage_users/disconnect_user_and_eject_pages.php", {user: username}, function(data){
			response = data.split("::");
			if(response[0] !== "error"){
				apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>" + username + " è stato disconnesso</h1><br />" + response[1] + " dal sistema.<br />Qualora sia attualmente " + response[2] + ", il suo pc visualizzerà una schermata per la riconnessione</td></tr></table>", {"noButton": false});
				$("#page_editing_list").flexReload();
			}
		});
	}
	$(document).ready(function() {
		$("#page_editing_list").flexigrid({
			colModel : [
				{display: 'UTENTE', name: 'user', width: 45, sortable: true, align: 'left'},
				{display: 'PAGINA', name: 'page', width: 250, sortable: true, align: 'left'},
				{display: 'ULTIMA MODIFICA', name: 'days_diff', width: 100, sortable: true, align: 'left'},
				{display: 'DATA ULTIMA MODIFICA', name: 'last_edit', width: 130, sortable: true, align: 'left'},
				{display: '', name: 'instruments', width: 100, sortable: false, align: 'center'}
			],
			url: "common/include/funcs/_ajax/manage_users/live_page_editing.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			rp: 5
		});
		setInterval(function() {
			$("#page_editing_list").flexReload();
		}, 10000);
	});
	</script>
	La tabella si autoaggiorna ogni 10 secondi<br /><br />
	<table id="page_editing_list"></table>
Table_list;

	require_once("common/include/conf/replacing_object_data.php");
	/*
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
			$users .= "<option value=\"" . $dato_user["username"] . "\">" . ucwords(strtolower($dato_user["name"] . " " . $dato_user["lastname"])) . "" . $username . " ~ " . $levels[$dato_user["level"]] . "</option>";
		}
		$users .= "</select>";
	}
	$content_body .= <<<edit_user_form
	<form method="post" action="" onsubmit="">
		<table cellspacing="10" cellpadding="10" style="width: 100%;">
			<tr>
				<td style="width: 128px;">
					<img src="common/media/img/user_half_edit_128_ccc.png" />
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
								<input type="submit" name="disconnect_user_btn" value="Modifica" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</form>
edit_user_form;
	*/
} else {
}
?>