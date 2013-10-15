<?php
/**
* Generates form for edit search engine
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
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$pdo = db_connect("air");
$edit_se = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($GLOBALS["page_q"]) . "'");
if ($edit_se->rowCount() > 0){
	while($dato_edit_se = $edit_se->fetch()){
		$se_id = $dato_edit_se["id"];
		$se_uri = $dato_edit_se["search_page"];
		$se_title = $dato_edit_se["name"];
		$se_description = utf8_decode($dato_edit_se["description"]);
		$se_search_var = $dato_edit_se["search_var"];
		$se_lang_var = $dato_edit_se["language_var"];
		$se_site_var = $dato_edit_se["site_var"];
		$se_filetype_var = $dato_edit_se["filetype_var"];
		$se_country_var = $dato_edit_se["country_var"];
		$se_lastdate_val = $dato_edit_se["last_date_val"];
		$se_lastdate_var = $dato_edit_se["last_date_var"];
		$se_quoting_var = $dato_edit_se["quoting_var"];
		$se_not_var = $dato_edit_se["exclusion_var"];
		$se_or_var = $dato_edit_se["or_var"];
	}
}
$content_title = "Modifica del Motore di Ricerca \"" . $se_title . "\"";
$content_subtitle = "Modifica dei dati del Motore di Ricerca";
$save_se_script = <<<Jquery_script
<script type="text/javascript">
	function htmlentities(str) {
		return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
	}
	function check_input_uri(){
		var input_uri = $("#se_uri").val();
		var input_old_uri = $("#se_old_uri").val();
		if (input_old_uri.length == 0){
			return $("#se_uri").val();
		} else {
			return $("#se_old_uri").val();
		}
	}
	function check_uri(){
		var check_uri = check_input_uri();
		var title = $("#se_title").val();
		var description = $("#se_description").val();
		var search_var = $("#se_search_var").val();
		var lang_var = $("#se_lang_var").val();
		var site_var = $("#se_site_var").val();
		var filetype_var = $("#se_filetype_var").val();
		var country_var = $("#se_country_var").val();
		
		if (check_uri.length > 0){
			if ($("#check_uri").css("display") == "none") {
				$("#check_uri").fadeIn(300);
			}
			loader("Controllo il link", "show");
			$.get("common/include/funcs/_ajax/AIR/scan_new_search_engine.php", {uri: check_uri, user: "{DECRYPTED_USER}"}, function(data){
				loader("", "hide");
				if (data.error != "none"){
					switch (data.error) {
						case "user already have this one":
							apprise("Questo motore di ricerca è già salvato tra i preferiti");
							break;
						case "search engine already exists":
							apprise("Questo motore di ricerca è già stato inserito");
							break;
						case "no form":
							break;
						default:
							if (lang_var.length == 0 || site_var.length == 0 || filetype_var.length == 0 || country_var.length == 0){
								apprise("<h1>Problema per le ricerche avanzate</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/settings_cancel_128_ccc.png\" /></td><td>Non è stato possibile identificare automaticamente i parametri per le ricerche avanzate,<br />che pertanto saranno impossibili per questo motore di ricerca.<br />Per abilitarle è necessario l'inserimento manuale delle relative chiavi.<br /><br />Per maggiori informazioni sulle ricerche avanzate automatizzate consultare la <a href=\"/Guide/AIR/Variabili_di_ricerca\">relativa guida</a></td></tr></table>");
							}
							if (data.title.length > 0){
								$("#se_title").val(data.title);
							}
							if (data.description.length > 0){
								$("#se_description").val(htmlentities(data.description));
							}
							break;
					}
				} else {
					if (lang_var.length == 0 || site_var.length == 0 || filetype_var.length == 0 || country_var == 0){
						$("#se_uri").val(data.uri);
						$("#se_title").val(data.title);
						$("#se_description").val(data.description);
						$("#se_search_var").val(data.search_var);
						
						if (data.advanced_search != ""){
							apprise("<h1>Procedura di acquisizione dei dati per le ricerche avanzate</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/copy_128_ccc.png\" /></td><td>Non è stato possibile ricavare automaticamente le chiavi per le ricerche avanzate e pertanto è richiesto un intervento manuale.<br />Proseguendo con l'operazione verrà avviata la procedura guidata per l'acquisizione di tali chiavi</td></tr></table>", {confirm: "true", textOk: "Prosegui »"}, function(r){
								if (r){
									parent.zoombox.open("{ABSOLUTE_PATH}common/include/funcs/ext/air.choose_search_engine_vars.php?uri=" + encodeURIComponent(data.advanced_search));
								}
							});
						}
					} else {
						$("#se_title").val(data.title);
						$("#se_description").val(data.description);
						$("#se_search_var").val(data.search_var);
						loader("", "hide");
					}
				}
			}, "json");
		}
	}
	function save_se(){
		var uri = $("#se_uri").val();
		var title = $("#se_title").val();
		var description = $("#se_description").val();
		var search_var = $("#se_search_var").val();
		var lang_var = $("#se_lang_var").val();
		var site_var = $("#se_site_var").val();
		var filetype_var = $("#se_filetype_var").val();
		var country_var = $("#se_country_var").val();
		
		if (uri.length > 0){
			if (title.length > 0 && description.length > 0 && search_var.length > 0){
				if (lang_var.length == 0 || site_var.length == 0 || site_var.length == 0 || filetype_var.length == 0 || country_var == 0){
					apprise("<h1>Parametri per le ricerche avanzate non rilevati</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/browser_window_cancel_128_ccc.png\" /></td><td>Alcuni parametri per le ricerche avanzate non sono stati inseriti.<br />Questo renderà impossibile effettuare ricerche più selettive.<br /><b>Avviare l'acquisizione assistita ora?</b></td></tr></table>", {"confirm": true}, function(r){
						if (r){
							check_uri();
						} else {
							loader("Salvataggio dei dati...", "show");
							$.post("common/include/funcs/_ajax/AIR/edit_search_engine.php", $("#add_search_engine").serialize(),
							function(data){
								var response = data.split(":");
								if (response[0] == "error"){
									apprise(response[1]);
									loader("", "hide");
								} else {
									loader("", "hide");
									console.log("{ABSOLUTE_PATH}AIR/");
									apprise("Motore di ricerca modificato.<br />Il motore di ricerca &quot;" + $("#se_title").val() + "&quot; è stato salvato ed è disponibile per tutti gli utenti", {"animate": true}, function(r){
										if (r){
											window.location.href = "{ABSOLUTE_PATH}AIR/Motori_di_ricerca";
										}
									});
								};
							});
						}
					});
				} else {
					loader("Salvataggio dei dati...", "show");
					$.post("common/include/funcs/_ajax/AIR/edit_search_engine.php", $("#add_search_engine").serialize(),
					function(data){
						var response = data.split(":");
						if (response[0] == "error"){
							apprise(response[1]);
							loader("", "hide");
						} else {
							loader("", "hide");
							apprise("Motore di ricerca modificato.<br />Il motore di ricerca &quot;" + $("#se_title").val() + "&quot; è stato salvato ed è disponibile per tutti gli utenti", {"animate": true}, function(r){
								if (r){
									window.location.href = "{ABSOLUTE_PATH}AIR/Motori_di_ricerca";
								}
							});
						};
					});
				}
			} else {
				return false;
			}
		} else {
			return false;
		}
		return false;
	}
	$(document).ready(function(){
		check_uri();
		$("#se_uri").blur(function(){
			$("#se_old_uri").val($("#se_uri").val());
			check_uri();
		});
	});
</script>
Jquery_script;
require_once("common/tpl/AIR/form_search_engine.tpl");
require_once("common/include/conf/replacing_object_data.php");
?>