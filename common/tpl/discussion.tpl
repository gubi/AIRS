<?php
/**
* Generates discussion page
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
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

require_once("common/include/funcs/_converti_data.php");
if (isset($_POST["save_page_btn"])){
	$add_message = $pdo->prepare("insert into airs_discussions (`name`, `subname`, `sub_subname`, `title`, `body`, `calls`, `user`) values(?, ?, ?, ?, ?, ?, ?)");
		$add_message->bindParam(1, addslashes($GLOBALS["page_m"]));
		$add_message->bindParam(2, addslashes($GLOBALS["page_id"]));
		$add_message->bindParam(3, addslashes($GLOBALS["page_q"]));
		$add_message->bindParam(4, addslashes($_POST["discussion_object"]));
		$add_message->bindParam(5, addslashes($_POST["discussion_content"]));
		$add_message->bindParam(6, addslashes(ucwords($_POST["called"])));
		$add_message->bindParam(7, addslashes(ucfirst($decrypted_user)));
	if ($add_message->execute()){
		$called_users = explode(",", $_POST["called"]);
		foreach($called_users as $username){
			// Notifica agli invitati
			$user = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($username) . "'");
			if ($user->rowCount() > 0){
				while($dato_user = $user->fetch()){
					
					$to = $dato_user["name"] . " " . $dato_user["lastname"] . " <" . $dato_user["email"] . ">";
					$header = "From: INRAN AIRS <airs@inran.it>\r\nContent-Type: text/plain; charset=\"utf-8\"\nContent-Transfer-Encoding: quoted-printable";
					$subject = "Invito alla discussione inerente alla pagina " . $GLOBALS["page"];
					$message = "Ciao " . $dato_user["name"] . ",\nl'utente " . ucfirst($decrypted_user) . " ti ha invitato a seguire la discussione sulla pagina " . $GLOBALS["page"] . ".\nA seguire il messaggio:\n\n";
					$message .= ">*". utf8_decode($_POST["discussion_object"]) . "*\n";
					$message .= ">". utf8_decode($_POST["discussion_content"]) . "\n\n";
					$message .= "Per visualizzare e rispondere al messaggio segui questo collegamento:\n" . $page_uri;
					$message .= "\n\n-- =\n\nIl Sistema AIRS\nINRAN\nIstituto Nazionale di Ricerca per l'alimentazione e la Nutrizione\nhttp://airs.inran.it/\nhttp://www.inran.it/";
					
					if (mail($to, $subject, utf8_decode($message), $header)){
						$confirm = "true";
					} else {
						$confirm = "false";
					}
				}
				// Notifica all'autore
				$user = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($decrypted_user) . "'");
				if ($user->rowCount() > 0){
					while($dato_user = $user->fetch()){
						if ($confirm == "true"){
							if ($user->rowCount() > 1){
								$to_user_txt = "agli utenti";
							} else {
								$to_user_txt = "all'utente";
							}
							$subject = "Conferma dell'invio della notifica per la discussione inerente alla pagina " . $GLOBALS["page"];
							$message = "Ciao " . $dato_user["name"] . ",\nl'invito " . $to_user_txt . " " . ucwords($_POST["called"]) . " per la pagina " . $GLOBALS["page"] . " è stata inviata con successo.";
						} else {
							$subject = "Errore dell'invio della notifica per la discussione inerente alla pagina " . $GLOBALS["page"];
							$message = "Ciao " . $dato_user["name"] . ",\nsi sono verificati degli errori nell'invio della notifica agli utenti invitati e pertanto potranno sapere dell'invito al primo accesso al sistema.";
						}
						$to = $dato_user["name"] . " " . $dato_user["lastname"] . " <" . $dato_user["email"] . ">";
						$header = "From: INRAN AIRS <airs@inran.it>\r\nContent-Type: text/plain; charset=\"utf-8\"\nContent-Transfer-Encoding: quoted-printable";
						$message .= "\n\n-- =\n\nIl Sistema AIRS\nINRAN\nIstituto Nazionale di Ricerca per l'alimentazione e la Nutrizione\nhttp://airs.inran.it/\nhttp://www.inran.it/";
						
						mail($to, $subject, $message, $header);
					}
				}
			}
		}
		redirect($absolute_path . $GLOBALS["current_pos_functioned"]);
	} else {
		print join(", ", $add_message->errorInfo());
	}
} else {
	$content_subtitle = "Discussioni sulla pagina";
	$check_page = $pdo->query("select * from `airs_discussions` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
	if ($check_page->rowCount() > 0){
		require_once("Text/Wiki.php");
		require_once("common/include/conf/Wiki/rendering.php");
		
		$content_body .= "<div id=\"discussion\">";
		$mcc = 0;
		while ($dato_check_page = $check_page->fetch()){
			$mcc++;
			$array_responses = explode("re: ", strtolower(stripslashes($dato_check_page["title"])));
			foreach($array_responses as $kr => $vr){
				$msg_content_style = "margin-left: " . ($kr*2) . "%;";
			}
			if ($mcc == $check_page->rowCount()){
				$msg_container_style .= " display: block;";
			} else {
				$msg_container_style .= "";
			}
			$content_body .= "<h1 id=\"msg_h1_" . $dato_check_page["id"] . "\" style=\"" . $msg_content_style . "\"><span onclick=\"slide('" . $dato_check_page["id"] . "')\">" . stripslashes($dato_check_page["title"]) . "</span></h1>";
				$content_body .= "<input type=\"hidden\" id=\"item_id_" . $dato_check_page["id"] . "\"class=\"item_id\" value=\"" . $dato_check_page["id"] . "\" />";
				$content_body .= "<input type=\"hidden\" id=\"item_style_" . $dato_check_page["id"] . "\" value=\"" . $msg_content_style . "\" />";
				$content_body .= "<input type=\"hidden\" id=\"obj_" . $dato_check_page["id"] . "\" value=\"" . stripslashes($dato_check_page["title"]) . "\" />";
				
					$write_by = "Scritto da ((" . ucfirst($dato_check_page["user"]) . "|" . ucfirst($dato_check_page["user"]) . ")) il " . converti_data(date("d M Y \a\l\l\e H:i", strtotime($dato_check_page["date"])));
					$content_wilki_write_by = $write_by;
					$output_content_wilki_write_by = $wiki->transform(stripslashes(utf8_decode($content_wilki_write_by)), "Xhtml");
					$content_wilki = $dato_check_page["body"];
					$output = $wiki->transform(stripslashes(utf8_decode($content_wilki)), "Xhtml");
				
				$content_body .= "<textarea id=\"edit_message_" . $dato_check_page["id"] . "\" style=\"display: none;\">" . str_replace("\n", "&#13;", stripslashes(utf8_encode(utf8_decode($content_wilki)))) . "</textarea>";
				$content_body .= "<textarea id=\"message_" . $dato_check_page["id"] . "\" style=\"display: none;\">Il " . converti_data(date("d M Y \a\l\l\e H:i", strtotime($dato_check_page["date"]))) . " ((" . ucfirst($dato_check_page["user"]) . "|" . ucfirst($dato_check_page["user"]) . ")) ha scritto:&#13;> " . str_replace("> >", ">>", str_replace("\n", "&#13;> ", stripslashes(utf8_encode(utf8_decode($content_wilki))))) . "</textarea>";
			$content_body .= "<div class=\"msg_container \" id=\"msg_" . $dato_check_page["id"] . "\" style=\"" . $msg_container_style . "\">";
				// Pulsanti di modifica, rimozione e quoting
				$content_body .= "<ul style=\"" . $msg_content_style . "\">";
					$content_body .= "<li><a class=\"edit\" href=\"javascript: void(0);\" onclick=\"edit_msg('" . $dato_check_page["id"] . "');\" title=\"Modifica il messaggio\"></a></li>";
					$content_body .= "<li><a class=\"cancel\" href=\"javascript: void(0);\" onclick=\"delete_msg('" . $dato_check_page["id"] . "', 'false');\" title=\"Cancella il messaggio\"></a></li>";
					$content_body .= "<li>&emsp;</li>";
					$content_body .= "<li><a class=\"quote\" href=\"javascript: void(0);\" onclick=\"quote_msg('" . $dato_check_page["id"] . "');\" title=\"Cita l'intero messaggio\"></a></li>";
				$content_body .= "</ul>";
				$content_body .= "<div id=\"msg_content_" . $dato_check_page["id"] . "\" class=\"msg_content\" style=\"" . $msg_content_style . "\">";
					// Elenco invitati
					$content_body .= "<div class=\"discussion_called\" id=\"discussion_called_" . $dato_check_page["id"] . "\">";
						$called = explode(",", $dato_check_page["calls"]);
						if (strlen($dato_check_page["calls"]) > 0){
							$content_body .= "<h1>Invitati:</h1>";
							$content_body .= "<div>";
								$content_body .= "<ul>";
									foreach($called as $li_call){
										$content_body .= "<li><a href=\"/Utente:" . ucwords(trim($li_call)) . "\">" . ucwords(trim($li_call)) . "</a></li>";
									}
									$calls = str_replace(ucfirst($decrypted_user), ucfirst($dato_check_page["user"]), ucwords($dato_check_page["calls"]));
									if (strpos(ucwords($dato_check_page["calls"]), ucfirst($dato_check_page["user"])) === false && ucfirst($decrypted_user) !== ucfirst($dato_check_page["user"])){
										$calls .= "," . ucfirst($dato_check_page["user"]);
									}
								$content_body .= "</ul>";
							$content_body .= "</div>";
							$content_body .= "<input type=\"hidden\" id=\"invited_" . $dato_check_page["id"] . "\" value=\"" . $calls . "\" />";
						} else {
							$content_body .= "<input type=\"hidden\" id=\"invited_" . $dato_check_page["id"] . "\" value=\"\" />";
						}
					$content_body .= "</div>";
				
					$content_body .= $output_content_wilki_write_by . "<div style=\"padding-left: 10px;\">";
						$content_body .= stripslashes($output);
					$content_body .= "</div>";
				$content_body .= "</div>";
			$content_body .= "</div>";
		}
		$content_body .= "</div>";
	} else {
		$content_body = "<i>Ancora nessuna discussione per questa pagina</i>";
	}
	$content_body .= "<hr />";
	// Se l'utente ha i privilegi
	if ($GLOBALS["user_level"] > 0){
		$content_body .= <<<Discussion
			<div id="new_message_editor">
				<h2>Nuovo messaggio</h2>
				<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
				<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
				<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
				<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
				<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/wiki/style.css" />
				<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
				<script type="text/javascript">
				function slide(id){
					if($("#msg_" + id).css("display") == "none"){
						$(".item_id").each(function(){
							if (id != $(this).val()){
								$("#msg_h1_" + $(this).val() + " span").text($("#obj_" + $(this).val()).val());
								$("#discussion_called_" + $(this).val()).fadeOut(300).animate({"margin-top": "-51px"}, 150);
								$("#msg_content_" + $(this).val()).css({"display": "block"});
								$("#msg_content_editor_" + $(this).val()).css({"display": "none"});
							}
						});
						$(".msg_container").slideUp(600);
						$("#discussion_called_" + id).fadeIn(300).animate({"margin-top": "-51px"}, 150);
						$("#msg_" + id).slideDown(600);
						$("#new_message_editor").css({"display": "block"});
					} else {
						$("#msg_" + id + " a.view").switchClass("view","edit").attr("onclick", "edit_msg('" + id + "')");
						$("#msg_" + id + " a.quote").fadeIn(300);
						$("#new_message_editor").css({"display": "block"});
						$("#msg_h1_" + id + " span").text($("#obj_" + id).val());
						$("#msg_content_" + id).css({"display": "block"});
						$("#msg_content_editor_" + id).css({"display": "none"});
					}
				}
				function save_edit_data(the_form, confirm){
					var the_id = the_form.id.replace("form_", "");
					var title = $("discussion_object_" + the_id).val();
					if (confirm == "true"){
						var the_confirm = "&confirm=true";
					} else {
						var the_confirm = "";
					}
					$.get("common/include/funcs/_ajax/save_discussion_edits.php?id=" + the_id + the_confirm + "&" + $("#" + the_form.id).serialize(), function(data){
						var response = data.split("~~~");
						if (response[0] == "error"){
							alert(response[1]);
						} else {
							switch(response[0]) {
								case "edited":
									$("#msg_content_" + response[1] +" > div").html(response[3]);
									slide(response[1]);
									break;
								case "need_confirm":
									apprise("<b>Si è sicuri di voler rimuovere il commento \"" + $("#obj_" + response[1]).val() + "\"?</b>", {'confirm': 'true'}, function(r){
										if (r){
											save_edit_data(the_form, "true");
										}
									});
									break;
								case "removed":
									apprise("Commento rimosso con successo");
									$("#msg_h1_" + response[1]).fadeOut(600, function(){
										$(this).remove();
										$("#item_id_" + response[1]).remove();
										$("#item_style_" + response[1]).remove();
										$("#obj_" + response[1]).remove();
										$("#edit_message_" + response[1]).remove();
										$("#message_" + response[1]).remove();
										$("#msg_" + response[1]).fadeOut(600, function(){
											$(this).remove();
										});
										slide(response[2]);
									});
									break;
							}
						}
					});
					return false;
				}
				function delete_msg(id, confirm){
					$.get("common/include/funcs/_ajax/save_discussion_edits.php?id=" + id + "&confirm=" + confirm + "&discussion_content=", function(data){
						var response = data.split("~~~");
						if (response[0] == "error"){
							alert(response[1]);
						} else {
							switch(response[0]) {
								case "need_confirm":
									apprise("<b>Si è sicuri di voler rimuovere il commento \"" + $("#obj_" + response[1]).val() + "\"?</b>", {'confirm': 'true'}, function(r){
										if (r){
											delete_msg(id, "true");
										}
									});
									break;
								case "removed":
									apprise("Commento rimosso con successo");
									$("#msg_h1_" + response[1]).fadeOut(600, function(){
										$(this).remove();
										$("#item_id_" + response[1]).remove();
										$("#item_style_" + response[1]).remove();
										$("#obj_" + response[1]).remove();
										$("#edit_message_" + response[1]).remove();
										$("#message_" + response[1]).remove();
										$("#msg_" + response[1]).fadeOut(600, function(){
											$(this).remove();
										});
										slide(response[2]);
									});
									break;
							}
						}
					});
				}
				function edit_msg(id){
					var msg = $("#edit_message_" + id).text();
					var msg_obj = $("#obj_" + id).val();
					var msg_calls = $("#invited_" + id).val();
					
					/* Modifica l'icona per avviare l'editor con quella per la visualizzazione */
					$("#msg_" + id + " a.edit").switchClass("edit","view").attr("onclick", "slide('" + id + "')");
					/* E nasconde quella per la citazione */
					$("#msg_" + id + " a.quote").fadeOut(300);
					/* Nasconde l'editor per i nuovi messaggi */
					$("#new_message_editor").css({"display": "none"});
					/* Inserisce il testo dei nuovi messaggi */
					$("#msg_h1_" + id + " span").text("Modifica di: " + msg_obj);
					
					$("#msg_content_" + id).css({"display": "none"});
					if($("#msg_content_editor_" + id).length == 0){
						$("#msg_" + id).append("<div id=\"msg_content_editor_" + id + "\" style=\"" + $("#item_style_" + id).val() + "\"><form id=\"form_" + id + "\" action=\"\" method=\"post\" onsubmit=\"return save_edit_data(this, 'false'); return false;\"><table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%;\" id=\"content_editor_" + id + "\"><tr><td><input type=\"text\" id=\"discussion_object_" + id + "\" name=\"discussion_object\" placeholder=\"Oggetto della discussione\" style=\"width: 99%;\" value=\"" + msg_obj + "\" /></td></tr><tr><td><textarea id=\"discussion_content_" + id + "\" name=\"discussion_content\">" + msg + "</textarea>\nPer maggiori informazioni riguardo alla formattazione consultare la <a href=\"Guide/Sintassi_del_wiki\" title=\"Guida per la sintassi del Wiki\" target=\"_blank\">guida per la sintassi del Wiki</a></td></tr><tr><td>&nbsp;</td></tr><tr><td><fieldset>Digita il nome degli utenti o gli indirizzi e-mail di chi vuoi invitare separati da una virgola \",\"<legend class=\"label\">Invitati</legend><table cellspacing=\"5\" cellpadding=\"5\" style=\"width: 100%;\"><tbody><tr><td><input type=\"hidden\" id=\"inputCall_" + id + "\" name=\"called\" value=\"" + msg_calls + "\" /><ul id=\"calls_" + id + "\"></ul></td></tr></tbody></table></fieldset></td></tr><tr><td><input type=\"submit\" name=\"edit_btn\" id=\"edit_btn_" + id + "\" disabled=\"disabled\" title=\"Temporanemente inattivo\" value=\"Modifica\" /></td></tr></table></form></div>");
						$("#discussion_content_" + id).markItUp(myWikiSettings);
						$("#discussion_content_" + id).bind('keyup', function(){
							$("#edit_btn_" + id).removeAttr('disabled');
						});
						$("#calls_" + id).tagit({
							fieldName: "call_" + id,
							singleField: true,
							singleFieldNode: $("#inputCall_" + id),
							removeConfirmation: true,
							allowSpaces: true,
							tagSource: function(search, showChoices) {
								var that = this;
								$.ajax({
									url: "common/include/funcs/_ajax/get_discussion_called.php",
									data: search,
									dataType: "json",
									success: function(choices) {
										showChoices(that._subtractArray(choices, that.assignedTags()));
									}
								});
							},
							onTagAdded: function(evt, tag) {
								var that = this;
								$.ajax({
									url: "common/include/funcs/_ajax/get_discussion_called.php",
									data: {term: $("#calls_" + id).tagit("tagLabel", tag)},
									dataType: "json",
									success: function(choices) {
										if (choices == null || $("#calls").tagit("tagLabel", tag) != choices) {
											$("#calls_" + id).tagit("removeTag", tag);
										}
									}
								});
							}
						}).click(function(){
							$(this).css({
								"border": "#999 1px solid",
								"box-shadow": "0 0 9px #ccc"
							});
						}).find("input").blur(function(){
							$("#calls_" + id).css({
								"border": "#ccc 1px solid",
								"box-shadow": "none"
							});
						}).focus(function(){
							$("#calls_" + id).css({
								"border": "#999 1px solid",
								"box-shadow": "0 0 9px #ccc"
							});
						});
					} else {
						$("#msg_content_editor_" + id).css({"display": "block"});
					}
					$("#discussion_object_" + id).focus();
				}
				function quote_msg(id){
					var quote = $('#message_' + id).text();
					var discussion_obj = $('#obj_' + id).val();
					var discussion_calls = $('#invited_' + id).val();
					var calls = discussion_calls.split(",");
					$("#calls").tagit("removeAll");
					for (var c = 0; c < calls.length; c++){
						$("#calls").tagit("createTag", calls[c]);
					}
					var discussion_content = $('.discussion_content').val();
					if (discussion_content.length == 0){
						$('#discussion_object').val('Re: ' + discussion_obj);
					}
					if (discussion_content.length > 0){
						discussion_content = discussion_content + '\\n\\n';
					}
					$('.discussion_content').val(discussion_content + quote + '\\n');
					$('.discussion_content').focus();
				}
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
					$('.discussion_content').markItUp(myWikiSettings);
					$('.discussion_content').bind('keyup', function(){
						$('#save_page_btn').removeAttr('disabled');
					});
				});
				$(document).ready(function() {
					$("#calls").tagit({
						fieldName: "call",
						singleField: true,
						singleFieldNode: $('#inputCall'),
						removeConfirmation: true,
						allowSpaces: true,
						tagSource: function(search, showChoices) {
							var that = this;
							$.ajax({
								url: "common/include/funcs/_ajax/get_discussion_called.php",
								data: search,
								dataType: "json",
								success: function(choices) {
									showChoices(that._subtractArray(choices, that.assignedTags()));
								}
							});
						},
						onTagAdded: function(evt, tag) {
							var that = this;
							$.ajax({
								url: "common/include/funcs/_ajax/get_discussion_called.php",
								data: {term: $("#calls").tagit("tagLabel", tag)},
								dataType: "json",
								success: function(choices) {
									if (choices == null || $("#calls").tagit("tagLabel", tag) != choices) {
										$("#calls").tagit("removeTag", tag);
									}
								}
							});
						}
					}).click(function(){
						$(this).css({
							"border": "#999 1px solid",
							"box-shadow": "0 0 9px #ccc"
						});
					}).find("input").blur(function(){
						$("#calls").css({
							"border": "#ccc 1px solid",
							"box-shadow": "none"
						});
					}).focus(function(){
						$("#calls").css({
							"border": "#999 1px solid",
							"box-shadow": "0 0 9px #ccc"
						});
					});
					$("#inputCall").val("");
					$("#discussion_object").val("");
					$(".discussion_content").val("");
					$("#calls").tagit("removeAll");
				});
				</script>
				<form action="" method="post">
					<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
						<tr>
							<td>
								<input type="text" id="discussion_object" name="discussion_object" placeholder="Oggetto della discussione" style="width: 99%;" value="" />
							</td>
						</tr>
						<tr>
							<td>
								<textarea class="discussion_content" name="discussion_content"></textarea>
								Per maggiori informazioni riguardo alla formattazione consultare la <a href="Guide/Sintassi_del_wiki" title="Guida per la sintassi del Wiki" target="_blank">guida per la sintassi del Wiki</a>
							</td>
						</tr>
						<tr><td>&nbsp;</td></tr>
						<tr>
							<td>
								<fieldset>
									Digita il nome degli utenti o gli indirizzi e-mail di chi vuoi invitare separati da una virgola ","
									<legend class="label">Invitati</legend>
									<table cellspacing="5" cellpadding="5" style="width: 100%;">
										<tbody><tr>
											<td>
												<input type="hidden" id="inputCall" name="called" value="" />
												<ul id="calls"></ul>
											</td>
										</tr>
									</tbody></table>
								</fieldset>
							</td>
						</tr>
						<tr>
							<td>
								<input type="submit" name="save_page_btn" id="save_page_btn" disabled="disabled" title="Temporanemente inattivo" value="Salva" />
							</td>
						</tr>
					</table>
				</form>
			</div>
Discussion;
	} else {
		// Maschera per il login
		require_once("common/tpl/fieldset_login.tpl");
	}
	require_once("common/include/conf/replacing_object_data.php");
}
?>