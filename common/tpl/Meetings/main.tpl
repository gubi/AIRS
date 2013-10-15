<?php
/**
* List all runnign Meeting
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

require_once("common/include/lib/bbb-api-php/includes/bbb-api.php");
function is_meeting_running($id){
	$bbb = new BigBlueButton();
	try {
		$is_running = $bbb->isMeetingRunningWithXmlResponseArray($id);
	} catch (exception $e) {
		print $e->getMessage();
	}
	$res = json_decode(json_encode($is_running), 1);
	if($is_running["running"][0] == "true") {
		return true;
	} else {
		return false;
	}
}
$bbb = new BigBlueButton();

$pdm = db_connect("meetings");
$query_meetings = $pdm->query("select * from `current_meetings` order by `creation_date` desc, `end_date` desc ");
if($query_meetings->rowCount() > 0){
	$content_body .= <<<Meetings_page
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function delete_result(){
		if($("#selected_row").val() == "" || $(".delete").hasClass("disabled")){
			apprise("&Egrave; necessario selezionare almeno un elemento da rimuovere", {"textOk": "Ok"});
		} else {
			var ids = $("#selected_row").val();
			ids.split(",");
			if(ids.length == 1){
				apprise("Sicuri di voler rimuovere il risultato selezionato?", {"confirm": "true"}, function(r){
					if (r){
						$.get("common/include/funcs/_ajax/AIR/delete_research_results.php", {id: ids[0]}, function(data){
							if (data == "removed"){
								apprise("Risultato della ricerca rimosso con successo", {"animate": "true"});
								$("#row" + ids[0]).fadeOut(300, function() { $(this).remove(); });
								$("#meetings_list").flexReload();
							}
						});
					}
				});
			} else {
				apprise("Sicuri di voler rimuovere i risultati selezionati?", {"confirm": "true"}, function(r){
					if (r){
						$.get("common/include/funcs/_ajax/AIR/delete_research_results.php", {id: ids}, function(data){
							if (data == "removed"){
								apprise("Risultato della ricerca rimosso con successo", {"animate": "true"});
								for (i = 0; i < ids.length; i++){
									$("#row" + ids[i]).fadeOut(300, function() { $(this).remove(); });
								}
								$("#meetings_list").flexReload();
							}
						});
					}
				});
			}
		}
	}
	$javascript_functions
	$(document).ready(function() {
		$("fieldset.legend").click(function(){
			if($(this).hasClass("collapsed")){
				$(this).switchClass("collapsed", "", 300).find("p.indication").slideUp(250);
				$(this).find("div#legend_content").slideDown(300);
			} else {
				$(this).switchClass("", "collapsed", 300).find("p.indication").slideDown(250);
				$(this).find("div#legend_content").slideUp(300);
			}
		});
		$("#selected_row").val("");
		$("#meetings_list").flexigrid({
			colModel : [
				{display: 'STATO', name: 'status', width: 42, sortable: true, align: 'left'},
				{display: 'ID', name: 'id', width: 18, sortable: true, align: 'right', searchable: false},
				{display: 'NOME', name: 'name', width: 90, sortable: true, align: 'left'},
				{display: 'DESCRIZIONE', name: 'description', width: 245, sortable: true, align: 'left'},
				{display: 'DATA CREAZIONE', name: 'creation_date', width: 130, sortable: true, align: 'left'},
				{display: 'DATA SCADENZA', name: 'end_date', width: 130, sortable: true, align: 'left'},
				{display: 'CREATO DA', name: 'user', width: 130, sortable: true, align: 'left'},
				{display: 'NUMERO VOIP', name: 'voip_no', width: 150, sortable: true, align: 'left'},
				{display: 'PARTECIPANTI', name: 'users_no', width: 120, sortable: false, align: 'left'},
				{display: 'DURATA', name: 'length', width: 110, sortable: false, align: 'left'}
			],
			searchitems : [
				{display: 'Nome del Meeting', name: 'name', isdefault: true},
				{display: 'Descrizione', name: 'description', isdefault: false}
			],
			onSuccess: function(data){ /*console.log(data); return true;*/ },
			hidable: false,
			showTableToggleBtn: true,
			url: "common/include/funcs/_ajax/Meetings/current_meetings.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			select: false,
			rp: 10
		});
		var refreshTable = 0;
		function stop_refresh_table(){
			clearTimeout(refreshTable);
		}
		function start_refresh_Table(){
			refreshTable = setTimeout(function() {
				refresh_table();
			}, 10000);
		}
		function refresh_table() {
			$("#meetings_list").flexReload();
		}
	});
	</script>
	<fieldset class="legend collapsed">
		<legend class="info">Informazioni utili</legend>
		<p class="indication">Fare click per visualizzare il contenuto condensato</p>
		<div id="legend_content">
			<p class="time">La tabella si autoaggiorna ogni 10 secondi</p>
			<p class="filter">&Egrave; possibile filtrare i risultati nel menu in fondo alla tabella</p>
		</div>
	</fieldset>
	<table id="meetings_list" class="flexigrid_autorefresh"></table>
Meetings_page;
} else {
	$meeting_content = $i18n["no_meeting"];
	$meeting_img = "chat_stop_128_ccc.png";
	$content_body .= <<<Meetings_page
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px" valign="top">
				<img src="common/media/img/$meeting_img" />
			</td>
			<td style="font-size: 1.1em;">
				<h4>$meeting_content</h4>
			</td>
		</tr>
	</table>
Meetings_page;
}
require_once("common/include/conf/replacing_object_data.php");
?>