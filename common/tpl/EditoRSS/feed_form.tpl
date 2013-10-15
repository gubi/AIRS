<?php
/**
* Generates form for add or edit feed
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$content_title = "Modifica il feed";
$content_subtitle = "Modulo di modifica di un feed del sistema EditoRSS";
$content_body = <<<Feed_form
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
function show_warn(warn, subj){
	if (warn.length > 0){
		apprise(warn, {'textOk': 'Ok'}, function(r){
			if (r){
				$("#" + subj).attr(warn);
				$("#" + subj).focus();
				$("#" + subj).addClass("error");
				
				$("#" + subj).live('keydown blur', function(){
					$("#" + subj).removeClass("error");
				});
			}
		});
	}
}
function check_if_theres_rss(e, f){
	if (f == undefined || f == null){
		rss_title = $("#rss_title").val();
		rss_uri = $("#rss_uri").val();
	} else {
		rss_title = f.rss_title.value;
		rss_uri = f.rss_uri.value;
	}
	if(rss_uri.length > 7){
		if (e == "test"){
			loader("Controllo il feed", "show");
			$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {uri: rss_uri, type: e, user: "{DECRYPTED_USER}"}, function(dato) {
				if (dato.response == "invalid uri"){
					apprise("L'indirizzo inserito non è un feed valido", function(r){
						if (r){
							$("#rss_uri").focus();
						}
					});
					loader("", "hide");
				} else {
					if ($("#rss_title").val() == ""){ $("#rss_title").val(dato.title); }
					$("#rss_description").html(dato.description).attr("disabled", false);
					if(dato.category != null) {
						for(cc = 0; cc < dato.category.length; cc++){
							new_group = dato.category[cc];
							$("#group").tagit("createTag", new_group);
						}
					}
					if (dato.tags != null){
						if (dato.tags.length > 0){
							for(tt = 0; tt < dato.tags.length; tt++){
								new_tag = dato.tags[tt];
								$("#tag").tagit("createTag", new_tag);
							}
						}
					}
					$("#breadcrumb").find("li:last-child").html($("#rss_title").val());
					loader("", "hide");
				}
			}, "json");
		} else {
			loader("interpretazione del feed", "show");
			$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {uri: rss_uri, type: e, user: "{DECRYPTED_USER}"}, function(dato) {
				if (dato >= 1){
					$("#action").val("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php?uri=" + rss_uri + "&type=" + e + "&user={DECRYPTED_USER}");
					/* Salva i dati nel primo form */
					$.post("common/include/funcs/_ajax/EditoRSS/add_feed.php", $("#add_feed_group").serialize(), function(data){
						var response = data.split(":");
						if (response[0] == "error"){
							apprise(response[1]);
							loader("", "hide");
						} else {
							if (response[0] == "added" || response[0] == "edited"){
								$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {uri: rss_uri, type: "save", id: response[1], user: "{DECRYPTED_USER}"}, function(dato) {
									if (dato == "added"){
										apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><h1>Dati salvati con successo.</h1></td><td><img src=\"common/media/img/accept_128_ccc.png\" /></td></tr></table>", {'animate': true}, function(r){
											if (r){
												loader("Redirezionamento", "show");
												window.location = ("{ABSOLUTE_PATH}EditoRSS");
											}
										});
									} else {
										apprise(data);
										loader("", "hide");
									}
								});
							} else {
								apprise(data);
								loader("", "hide");
							}
						}
					});
				} else {
					var warn = "";
					switch (dato){
						case "no get":
							warn = "Si è verificato un problema di rete";
							break;
						case "invalid uri":
							warn = "L'indirizzo inserito non è un feed valido.<br /><br />È possibile <a href=\"" + rss_uri + "\" target=\"_blank\">visualizzarlo in un'altra pagina</a>";
							break;
						default:
							alert(data);
							break;
					}
					show_warn(warn, "rss_uri");
					loader("", "hide");
				}
			});
		}
	}
	return false;
}
$('.noWarn').click(function() { $('body').removeAttr('onbeforeunload'); });

$(document).ready(function() {
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
		dateFormat: 'D, dd M yy',
		firstDay: 1,
		autoSize: true,
		isRTL: false,
		minDate: 0
	};
	$.datepicker.setDefaults($.datepicker.regional['it']);
	$(".datepicker").datepicker();
	$(".timepicker").timepicker({
		timeFormat: "hh:mm",
		stepHour: 1,
		stepMinute: 1,
		hourGrid: 5,
		minuteGrid: 10
	});
	
	$("#group").tagit({
		fieldName: "tags",
		singleField: true,
		singleFieldNode: $('#inputGroup'),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/EditoRSS/get_existing_feeds_groups.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){
		$(this).css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	}).find("input").blur(function(){
		$("#tag").css({
			"border": "#ccc 1px solid",
			"box-shadow": "none"
		});
	}).focus(function(){
		$("#tag").css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	});
	$("#tag").tagit({
		fieldName: "tags",
		singleField: true,
		singleFieldNode: $('#inputTag'),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/get_existing_tags.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){
		$(this).css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	}).find("input").blur(function(){
		$("#tag").css({
			"border": "#ccc 1px solid",
			"box-shadow": "none"
		});
	}).focus(function(){
		$("#tag").css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	});
	$("#rss_uri").blur(function(){
		check_if_theres_rss("test");
	});
	if ($("#rss_title").val() !== "" && $("#rss_uri").val() !== ""){
		check_if_theres_rss("test");
	}
});
</script>
<input type="hidden" id="results_index" value="" />
<div id="add_feed_form">
	<form action="" method="post" id="add_feed_group" onsubmit="return check_if_theres_rss('check', this); return false;">
		<input type="hidden" name="decrypted_user" value="{DECRYPTED_USER}" />
		<input type="hidden" id="action" name="action" value="" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="edit">Feed <acronym title="Really Simple Syndication">RSS</acronym></legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="url" id="rss_uri" name="rss_uri" placeholder="Indirizzo del feed" style="width: 99%;" value="$rss_uri" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<input type="text" id="rss_title" name="rss_title" placeholder="Titolo del feed" style="width: 99%;" value="$rss_title" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea style="width: 99%;" name="rss_description" id="rss_description" placeholder="Descrizione del feed" disabled="disabled">$rss_description</textarea>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="groups">Gruppi di feed</legend>
						Raggruppa il feed in base a delle cerchie di afferenza, saranno così catalogati e ordinati.
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="hidden" id="inputGroup" name="rss_group" value="$rss_group" />
									<ul id="group">$rss_li_group</ul>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						Aggiungi delle parole chiave per poter rintracciare questo feed più facilmente.
						<legend class="label">Tag</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="hidden" id="inputTag" name="rss_tag" value="$rss_tag" />
									<ul id="tag">$rss_li_tag</ul>
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						<legend class="clock">Automazione</legend>
						<label>
							<input type="checkbox" id="automation" name="automation" checked="$checked" onclick="if($('#automation').is(':checked')){ $('#automation_cadence').attr('disabled', false); $('#automation_date').attr('disabled', false); $('#automation_time').attr('disabled', false); } else { $('#automation_cadence').attr('disabled', true); $('#automation_date').attr('disabled', true); $('#automation_time').attr('disabled', true); }" />
							&nbsp;Scansiona il feed automaticamente
						</label>
						<br />
						<br />
						<table cellspacing="10" cellpadding="5" style="width: 100%; border: #ddd 1px solid; background-color: #f6f6f6;">
							<tr>
								<th style="width: auto;">Frequenza di scansione:</th>
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
								<th style="width: auto;">Inizio della scansione:</th>
								<td>
									<input type="date" class="datepicker" id="automation_date" name="automation_date" value="{FORM_DATE}" />
									&nbsp;&nbsp;&nbsp;<input type="time " class="timepicker" id="automation_time" name="automation_time" value="{HOUR}" />
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<input type="submit" name="save_feed_btn" id="save_feed_btn" value="Salva" />
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="contenuto"></div>
Feed_form;

require_once("common/include/conf/replacing_object_data.php");
?>