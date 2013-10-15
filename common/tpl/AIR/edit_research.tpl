<?php
/**
* Generates form for edit research
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

require_once("common/include/funcs/_converti_data.php");
$card_title = str_replace("_", " ", $GLOBALS["page_q"]);

$pdo = db_connect("air");
if (is_numeric($card_title)){
	$edit_res = $pdo->query("select * from `air_research` where `id` = '" . $card_title . "'");
} else {
	$edit_res = $pdo->query("select * from `air_research` where `title` like '" . $card_title . "%'");
}
if ($edit_res->rowCount() > 0){
	while($dato_edit_res = $edit_res->fetch()){
		$res_id = $dato_edit_res["id"];
		$res_title = $dato_edit_res["title"];
		$card_title = $res_title;
		$res_description = $dato_edit_res["description"];
		$res_tags_arr = explode(",", $dato_edit_res["tags"]);
		$res_tags = $dato_edit_res["tags"];
			foreach($res_tags_arr as $tag){
				$res_tags_li .= "<li>" . $tag . "</li>";
			}
		$res_query = $dato_edit_res["query"];
		$res_filter_domain = $dato_edit_res["filter_domain"];
		$res_filter_filetype = $dato_edit_res["filter_filetype"];
		// Da convertire in data verbosa (es. Ven, 01 Giu 2012)
		$res_filter_date = converti_data(date("D, d M Y", strtotime($dato_edit_res["filter_date"])), "it", "month_first", "short");
		
		// Elenco dei motori di ricerca
			$se_arr = explode(",", $dato_edit_res["search_engines"]);
			foreach($se_arr as $search_engine_id){
				$query_se_id = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($search_engine_id) . "'");
				if ($query_se_id->rowCount() > 0){
					while ($dato_se_id = $query_se_id->fetch()){
						$selected_se[] = $dato_se_id["name"];
					}
				}
			}
			$s = 0;
			$query_se = $pdo->query("select * from `air_search_engines`");
			if ($query_se->rowCount() > 0){
				while ($dato_se = $query_se->fetch()){
					$s++;
					$search_eng[$s]["id"] = $dato_se["id"];
					$search_eng[$s]["text"] = $dato_se["name"];
					if (in_array($dato_se["name"], $selected_se)){
						$search_eng[$s]["selected"] = "selected";
					} else {
						$search_eng[$s]["selected"] = "";
					}
				}
			}
			//print_r($search_eng);
			$res_search_engines = "<select name=\"se_search_engines[]\" id=\"se_search_engines\" multiple style=\"width: 100%;\" data-placeholder=\"Motori di ricerca...\">";
				foreach($search_eng as $search_engine){
					$res_search_engines .= "<option value=\"" . $search_engine["id"] . "\"" . $search_engine["selected"] . ">" . $search_engine["text"] . "</option>";
				}
			$res_search_engines .= "</select>";
		// --
		
		// Elenco delle lingue
			$pdo = db_connect(".airs");
			$lang_arr = explode(",", $dato_edit_res["languages"]);
			foreach($lang_arr as $lang_arr_id){
				$query_lang_id = $pdo->query("select * from `ISO_639-3` where `Part1` != '' and `Part1` = '" . addslashes($lang_arr_id) . "' order by `Ref_Name_it` asc");
				if ($query_lang_id->rowCount() > 0){
					while ($dato_lang_id = $query_lang_id->fetch()){
						$selected_lang[] = ucwords(strtolower($dato_lang_id["Ref_Name_it"]));
					}
				}
			}
			$l = 0;
			$query_se_languages = $pdo->query("select * from `ISO_639-3` where `Part1` != '' order by `Ref_Name_it` asc");
			if ($query_se_languages->rowCount() > 0){
				while($dato_se_lang = $query_se_languages->fetch()){
					$l++;
					$langs[$l]["id"] = strtolower($dato_se_lang["Part1"]);
					$langs[$l]["text"] = ucwords(strtolower($dato_se_lang["Ref_Name_it"]));
					if(is_array($selected_lang)){
						if (in_array(ucwords(strtolower($dato_se_lang["Ref_Name_it"])), $selected_lang)){
							$langs[$l]["selected"] = "selected";
						} else {
							$langs[$l]["selected"] = "";
						}
					} else {
						$langs[$l]["selected"] = "";
					}
				}
			}
			$se_lang = "<select name=\"se_lang[]\" id=\"se_lang\" multiple style=\"width: 100%;\" data-placeholder=\"Lingua...\">";
			foreach($langs as $lang){
				$se_lang .= "<option value=\"" . $lang["id"] . "\"" . $lang["selected"] . ">" . $lang["text"] . "</option>";
			}
			$se_lang .= "</select>";
		// --
		
		// Elenco dei Paesi
			$pdo = db_connect(".airs");
			$query_regions = $pdo->query("select * from `ISO_3166-1` order by `Country_it` asc");
			if ($query_regions->rowCount() > 0){
				while($dato_regions = $query_regions->fetch()){
					$regions_arr[strtolower($dato_regions["Country_codes"])] = $dato_regions["Country_it"];
				}
			}
			$region_arr = explode(",", $dato_edit_res["filter_region"]);
			$r = 0;
			foreach($regions_arr as $region_k => $region_v){
				foreach($region_arr as $region){
					$r++;
					$regions[$r]["id"] = $region_k;
					$regions[$r]["text"] = $region_v;
					if (is_array($region_arr)){
						if ($region_k == $region){
							$regions[$r]["selected"] = "selected";
						} else {
							$regions[$r]["selected"] = "";
						}
					} else {
						$regions[$r]["selected"] = "";
					}
				}
			}
			$se_region = "<select name=\"se_country[]\" id=\"se_country\" multiple style=\"width: 100%;\" data-placeholder=\"Paese...\">";
			foreach($regions as $reg){
				$se_region .= "<option value=\"" . $reg["id"] . "\"" . $reg["selected"] . ">" . $reg["text"] . "</option>";
			}
			$se_region .= "</select>";
		// --
		
		// Automazione
		$uris_arr = explode(",", $dato_edit_res["research_uris"]);
		$pdo = db_connect(".airs");
		$sa = 0;
		$query_sected_automation = $pdo->query("select * from `airs_automation` where `action` = '" . $uris_arr[0] . "'");
		if ($query_sected_automation->rowCount() > 0){
			while($dato_sected_automation = $query_sected_automation->fetch()){
				$sa++;
				$automation_frequency = $dato_sected_automation["frequency"];
				$automation_start_date = converti_data(date("D, d M Y", strtotime($dato_sected_automation["start_date"])), "it", "month_first", "short");
				$automation_start_time = date("H:i", strtotime($dato_sected_automation["start_time"]));
			}
		}
		$automation_form = "<table cellspacing=\"10\" cellpadding=\"5\" style=\"width: 100%; border: #ddd 1px solid; background-color: #f6f6f6;\">";
			$automation_form .= "<tr><th style=\"width: auto;\">Frequenza di ricerca:</th><td><select name=\"automation_cadence\" id=\"automation_cadence\">";
				$query_automation_frequency_group = $pdo->query("select distinct(`frequency_group`) from `airs_automation_frequency` order by `id` asc");
				if ($query_automation_frequency_group->rowCount() > 0){
					while($dato_automation_frequency_group = $query_automation_frequency_group->fetch()){
						$automation_form .= "<optgroup label=\"" . $dato_automation_frequency_group["frequency_group"] . "\">";
							$query_automation_frequency = $pdo->query("select * from `airs_automation_frequency` where `frequency_group` = '" . addslashes($dato_automation_frequency_group["frequency_group"]) . "'");
							if ($query_automation_frequency->rowCount() > 0){
								while($dato_automation_frequency = $query_automation_frequency->fetch()){
									if ($automation_frequency == $dato_automation_frequency["type"]){
										$asel = "selected";
									} else {
										$asel = "";
									}
									$automation_form .= "<option value=\"" . $dato_automation_frequency["type"] . "\"" . $asel . ">" . $dato_automation_frequency["frequency_list_txt"] . "</option>";
								}
							}
						$automation_form .= "</optgroup>";
					}
				}
			$automation_form .= "</select></td></tr>";
		$automation_form .= "<tr><th style=\"width: auto;\">Inizio della ricerca:</th><td>";
			$automation_form .= "<input type=\"date\" class=\"datepicker\" id=\"automation_date\" name=\"automation_date\" value=\"" . $automation_start_date . "\" />&nbsp;&nbsp;&nbsp;<input type=\"time\" class=\"timepicker\" id=\"automation_time\" name=\"automation_time\" value=\"" . $automation_start_time . "\" />";
		$automation_form .= "</td></tr></table>";
	}
}
$content_title = "Modifica della ricerca: \"" . $card_title . "\"";
$content_subtitle = "Modifica dei parametri impostati per la ricerca automatizzata";
$content_body = <<<Edit_search_form
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
<script src="{ABSOLUTE_PATH}common/js/include/AIR/edit_research_sets.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
function show_end_message(msg){
	apprise(msg, {'verify': true}, function(r){
		if (r){
			$("#loader_message").text("Avvio della ricerca automatizzata");
			$.get("common/include/funcs/_cron/automation.php", {force_run: "true"},
			function (data){
				if (data){
					var response = data.split(" :: ");
					if (response[0] == "STARTED"){
						$("#loader_message").text("Ricerca avviata");
						loader("", "hide");
						apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/recent_changes_128_ccc.png\" /></td><td><h1>Ricerca in fase di scansione</h1><br />La ricerca appena inserita è in fase di scansione e al suo termine verrà inviata un'e-mail di conferma</td></tr></table>", function(r){
							location.reload();
						});
					}
				}
			});
		} else {
			loader("", "hide");
		}
	});
}
function save_se(){
	loader("Inserimento dei dati<br />Attendere...", "show");
	$.get("common/include/funcs/_ajax/AIR/edit_research.php", $("#edit_search_engine").serialize(), function(data){
		var response = data.split(":");
		switch (response[0]){
			case "edited":
				show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Ricerca automatica modificata con successo.</h1><br />Le ricerche verranno effettuate ogni giorno alla mezzanotte.<br /><b>Si vuole avviare ora la prima ricerca?</b></td></tr></table>");
				break;
			case "error":
				$("#loader_message").html("Si è verificato un errore");
				apprise(response[1], {"animate": "true"}, function(r){
					if (r){
						loader("", "hide");
						$("#" + response[2]).addClass("error").focus();
					}
				});
				break;
			default:
				alert(response[0]);
				break;
		}
	});
	return false;
}
</script>
<div id="add_se_form">
	<form action="" method="post" id="edit_search_engine" onsubmit="return save_se(); return false;">
		<input type="hidden" name="decrypted_user" value="{DECRYPTED_USER}" />
		<input type="hidden" name="se_id" value="$res_id" />
		<input type="hidden" name="action" value="$action" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="info">Dati identificativi della ricerca</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="text" id="se_title" name="se_title" placeholder="Titolo della ricerca" style="width: 99%;" value="$res_title" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea style="width: 99%;" name="se_description" id="se_description" placeholder="Breve descrizione della ricerca">$res_description</textarea>
								</td>
							</tr>
						</table>
						<br />
						<fieldset>
							Aggiungi delle parole chiave per poter rintracciare questa ricerca più facilmente.
							<legend class="label">Tag</legend>
							<table cellspacing="5" cellpadding="5" style="width: 100%;">
								<tr>
									<td>
										<input type="hidden" id="se_tags" name="se_tags" value="$res_tags" />
										<ul id="se_tags_ul">$res_tags_li</ul>
									</td>
								</tr>
							</table>
						</fieldset>
					</fieldset>
					<br />
					<fieldset>
						<legend class="search">Ricerca</legend>
						Cerca i seguenti termini
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="text" id="se_query" name="se_query" placeholder="Cerca:" style="width: 99%;" value="$res_query" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									$res_search_engines
								</td>
							</tr>
						</table>
						<br />
						<fieldset>
							<legend class="query">Ricerca selettiva</legend>
							<table cellspacing="5" cellpadding="5" style="width: 100%;">
								<tr>
									<th>
										Lingua:
									</th>
									<td>
										$se_lang
									</td>
								</tr>
								<tr>
									<th>
										<label for="se_site">Filtro su un dominio:</label>
									</th>
									<td>
										<input type="text" id="se_site" name="se_site" style="width: 98%;" value="$res_filter_domain" />
									</td>
								</tr>
								<tr>
									<th>
										<label for="se_filetype_var">Filtro su un formato di documento:</label>
									</th>
									<td>
										<input type="text" id="se_filetype_var" name="se_filetype_var" style="width: 98%;" value="$res_filter_filetype" />
									</td>
								</tr>
								<tr>
									<th>
										Paese:
									</th>
									<td>
										$se_region
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<th>
										<label for="se_lastdate_val">Filtro temporale:</label>
									</th>
									<td>
										<input type="date" id="se_lastdate_val" name="se_lastdate_val" class="datepicker" value="$res_filter_date" />
									</td>
								</tr>
							</table>
						</fieldset>
					</fieldset>
					<br />
					<fieldset>
						<legend class="clock">Automazione*</legend>
						<label>
							<input type="checkbox" id="automation" name="automation" onclick="if($('#automation').is(':checked')){ $('#automation_cadence').attr('disabled', false); $('#automation_date').attr('disabled', false); $('#automation_time').attr('disabled', false); } else { $('#automation_cadence').attr('disabled', true); $('#automation_date').attr('disabled', true); $('#automation_time').attr('disabled', true); }" />
							&nbsp;Ripeti questa ricerca automaticamente
						</label>
						<br />
						<br />
						$automation_form
						<br />
						<br />
						<i>* Funzionalit&agrave; da implementare su revisione del coordinatore</i>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td colspan="2">
					<input type="submit" name="save_feed_btn" id="save_feed_btn" value="Salva" />
				</td>
			</tr>
		</table>
	</form>
</div>
Edit_search_form;
$content_body = utf8_encode($content_body);

require_once("common/include/conf/replacing_object_data.php");
?>