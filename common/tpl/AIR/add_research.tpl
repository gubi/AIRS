<?php
/**
* Generates form for add research
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

require_once("common/include/funcs/_taglia_stringa.php");
require_once("common/include/funcs/translate.php");
$pdo = db_connect("air");

/*
$query_user_se = $pdo->query("select `se_id` from `air_users_search_engines` where `user` = '" . addslashes($decrypted_user) . "'");
if ($query_user_se->rowCount() > 0){
	$search_engines = "<select name=\"se_search_engines[]\" id=\"se_search_engines\" multiple style=\"width: 100%;\" data-placeholder=\"Motori di ricerca...\">";
	while ($dato_user_se = $query_user_se->fetch()){
		$query_se = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($dato_user_se["se_id"]) . "'");
		*/
		$query_se = $pdo->query("select * from `air_search_engines`");
		if ($query_se->rowCount() > 0){
			$search_engines = "<select name=\"se_search_engines[]\" id=\"se_search_engines\" multiple style=\"width: 100%;\" data-placeholder=\"Motori di ricerca...\">";
			while ($dato_se = $query_se->fetch()){
				$search_engines .= "<option value=\"" . ucfirst($dato_se["id"]) . "\" title=\"" . $dato_se["name"] . "\" />" . $dato_se["name"] . "</option>";
			}
			$search_engines .= "</select>";
		}
		/*
	}
	$search_engines .= "</select>";
}
*/
$pdo = db_connect(".airs");
$query_se_languages = $pdo->query("select * from `ISO_639-3` where `Part1` != '' order by `Ref_Name_it` asc");
if ($query_se_languages->rowCount() > 0){
	$se_lang = "<select name=\"se_lang[]\" id=\"se_lang\" multiple style=\"width: 100%;\" data-placeholder=\"Lingua...\">";
	while($dato_se_lang = $query_se_languages->fetch()){
		$se_lang .= "<option value=\"" . strtolower($dato_se_lang["Part1"]) . "\" title=\"" . ucwords(strtolower($dato_se_lang["Ref_Name_it"])) . "\" />" . ucwords(strtolower($dato_se_lang["Ref_Name_it"])) . "</option>";
	}
	$se_lang .= "</select>";
}
$query_se_country = $pdo->query("select * from `ISO_3166-1` order by `Country_it` asc");
if ($query_se_country->rowCount() > 0){
	$se_country = "<select name=\"se_country[]\" id=\"se_country\" multiple style=\"width: 100%;\" data-placeholder=\"Paese...\">";
	while($dato_se_country = $query_se_country->fetch()){
		$se_country .= "<option value=\"" . strtolower($dato_se_country["Country_codes"]) . "\" title=\"" . $dato_se_country["Country_it"] . "\" />" . $dato_se_country["Country_it"] . "</option>";
	}
	$se_country .= "</select>";
}
$pdo = db_connect("air");
$content_body = <<<Add_search_form
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
<script src="{ABSOLUTE_PATH}common/js/include/AIR/add_research_sets.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
function save_se(){
	loader("Inserimento dei dati<br />Attendere...", "show");
	$.get("common/include/funcs/_ajax/AIR/add_search.php", $("#add_search_engine").serialize(), function(data){
		var response = data.split(":");
		switch (response[0]){
			case "added":
				apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Ricerca automatizzata aggiunta con successo.</h1><br />Le ricerche verranno effettuate ogni giorno alla mezzanotte.<br /><b>Si vuole avviare ora la prima ricerca?</b></td></tr></table>", {"verify": "true"}, function(r){
					if (r){
						$("#loader_message").text("Avvio della ricerca automatizzata");
						$.get("common/include/funcs/_cron/automation.php", {force_run: "true", type: "air", user: "$decrypted_user", id: response[1]},function (data){
							if (data){
								var resp = data.split(" :: ");
								if (resp[0] == "STARTED"){
									$("#loader_message").text("Ricerca avviata");
									loader("", "hide");
									apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/recent_changes_128_ccc.png\" /></td><td><h1>Ricerca in fase di scansione</h1><br />La ricerca appena inserita  in fase di scansione e al suo termine verrà inviata un'e-mail di conferma</td></tr></table>", {"noButton": false}, function(r){
										window.location = "https://airs.inran.it/AIR/Risultati_delle_ricerche";
									});
								}
							}
						});
					} else {
						loader("", "hide");
					}
				});
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
		}
	});
	return false;
}
</script>
<div id="add_se_form">
	<form action="" method="post" id="add_search_engine" onsubmit="return save_se(); return false;">
		<input type="hidden" name="decrypted_user" value="{DECRYPTED_USER}" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="info">Dati identificativi della ricerca</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="text" id="se_title" name="se_title" placeholder="Titolo della ricerca" style="width: 99%;" value="" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea style="width: 99%;" name="se_description" id="se_description" placeholder="Breve descrizione della ricerca"></textarea>
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
										<input type="hidden" id="se_tags" name="se_tags" value="" />
										<ul id="se_tags_ul"></ul>
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
									<input type="text" id="se_query" name="se_query" placeholder="Cerca:" style="width: 99%;" value="" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									$search_engines
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
										<input type="text" id="se_site" name="se_site" style="width: 98%;" value="" />
									</td>
								</tr>
								<tr>
									<th>
										<label for="se_filetype_var">Filtro su un formato di documento:</label>
									</th>
									<td>
										<input type="text" id="se_filetype_var" name="se_filetype_var" style="width: 98%;" value="" />
									</td>
								</tr>
								<tr>
									<th>
										Paese:
									</th>
									<td>
										$se_country
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<th>
										<label for="se_lastdate_val">Filtro temporale:</label>
									</th>
									<td>
										<input type="date" id="se_lastdate_val" name="se_lastdate_val" class="datepicker" value="" />
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
						<table cellspacing="10" cellpadding="5" style="width: 100%; border: #ddd 1px solid; background-color: #f6f6f6;">
							<tr>
								<th style="width: auto;">Frequenza di ricerca:</th>
								<td>
									<select name="automation_cadence" id="automation_cadence">
										<optgroup label="Opzioni personalizzate">
											<option value="only_one">Una sola volta (oltre questa)</option>
										</optgroup>
										<optgroup label="Frequenza oraria">
											<option value="half_hour">Ogni mezz'ora</option>
											<option value="1hours">Ogni ora</option>
											<option value="3hours">Ogni 3 ore</option>
											<option value="6hours">Ogni 6 ore</option>
										</optgroup>
										<optgroup label="Frequenza periodica">
											<option value="daily" selected="selected">Giornaliera</option>
											<option value="weekly">Settimanale</option>
											<option value="fortnightly">Quindicinale</option>
											<option value="monthly">Mensile</option>
										</optgroup>
									</select>
								</td>
							</tr>
							<tr>
								<th style="width: auto;">Inizio della ricerca:</th>
								<td>
									<input type="date" class="datepicker" id="automation_date" name="automation_date" value="{FORM_DATE}" disabled />
									&nbsp;&nbsp;&nbsp;<input type="time " class="timepicker" id="automation_time" name="automation_time" value="{HOUR}" disabled />
								</td>
							</tr>
						</table>
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
Add_search_form;
$content_body = $content_body;

require_once("common/include/conf/replacing_object_data.php");
?>