<?php
/**
* List all research results
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

// Pagina dell'elenco 
$pdo = db_connect("air");
$check_res = $pdo->query("select * from `air_research_results` limit 10");
if ($check_res->rowCount() > 0){
	// Toolbar
	require_once("common/tpl/_component_toolbar.tpl");
	$javascript_functions = toolbar("export_to_file.php", "air", "air_research_results", "risultati_delle_ricerche_del_sistema", "javascript_functions");
	$btns_table = toolbar("export_to_file.php", "air", "air_research_results", "risultati_delle_ricerche_del_sistema", "btns_table");
	$user_mail_txt = toolbar("export_to_file.php", "air", "air_research_results", "risultati_delle_ricerche_del_sistema", "user_mail_txt");
	$user_mail_txt_full = toolbar("export_to_file.php", "air", "air_research_results", "risultati_delle_ricerche_del_sistema", "user_mail_txt_full");
	
	$content_body .= <<<Research_list
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
								$("#research_results_list").flexReload();
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
								$("#research_results_list").flexReload();
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
		$("#research_results_list").flexigrid({
			colModel : [
				{display: 'ID', name: 'id', width: 18, sortable: true, align: 'right', searchable: false},
				{display: 'RICERCA', name: 'ricerca', width: 90, sortable: true, align: 'left'},
				{display: 'MOTORE DI RICERCA', name: 'search_engine', width: 130, sortable: true, align: 'left'},
				{display: 'QUERY', name: 'query', width: 130, sortable: true, align: 'left'},
				{display: 'RISULTATI', name: 'risultati', width: 245, sortable: true, align: 'left'},
				{display: 'COPIA CACHE', name: 'copia_cache', width: 150, sortable: true, align: 'left'},
				{display: 'TAGS', name: 'tags', width: 120, sortable: false, align: 'left'},
				{display: 'KEYWORDS', name: 'keywords', width: 110, sortable: false, align: 'left'},
				{display: 'PAROLE', name: 'words_count', width: 45, sortable: true, align: 'right'},
				{display: 'UTENTE', name: 'utente', width: 65, sortable: true, align: 'left'},
				{display: 'DATA', name: 'data', width: 65, sortable: true, align: 'left'}
			],
			searchitems : [
				{display: 'Nome della ricerca', name: 'title', isdefault: false},
				{display: 'Titolo del risultato', name: 'title', isdefault: false},
				{display: 'Titolo della collegamento al risultato', name: 'result_link_text', isdefault: false},
				{display: 'Descrizione del risultato', name: 'result_description', isdefault: true},
				{display: 'Contenuto del risultato', name: 'result_content', isdefault: false},
				{display: 'Query di ricerca', name: 'query', isdefault: false},
				{display: 'Tag', name: 'o.tags'},
				{display: 'Keyword', name: 'keywords'},
				{display: 'Utente', name: 'c.user'},
				{display: 'Data (AAA-MM-GG)', name: 'date'}
			],
			buttons : [
				{name: '', btitle: 'Seleziona/deseleziona tutto', bid: 'selectOrUnselectAll', bclass: 'pUnselectAll pSelectAll', onpress: function(){ $("#research_results_list").flexSelectAll(); }},
				{separator: true},
				{name: '', btitle: 'Rimuovi', bclass: 'function_btn delete disabled', onpress: function(){ delete_result(); }},
				{separator: true},
				{name: '', btitle: 'Notifica via e-mail', bclass: 'function_btn mail disabled right', onpress: function(){
					if($(".mail").hasClass("disabled")){
						apprise("&Egrave; necessario selezionare almeno un elemento da rimuovere", {"textOk": "Ok"});
					} else {
						apprise("<h1>Esporta via e-mail</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/mail_run_128_ccc.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\">Inserisci l'indirizzo in cui si desidera ricevere la mail<br /></td></tr></table>", {"input": "$user_mail_txt"}, function(r){
							if(r){
								if (r == "$user_mail_txt"){
									$("#send_to").val("$user_mail_txt_full");
									var to = "$user_mail_txt_full";
								} else {
									var to = r;
								}
								/* Invio con allegato ~ da finire di sviluppare */
								/*
								apprise('$btns_table', {"animate": "true", "noButton": "true"}, function(s){
									if(s){
									} else {
										$("#send_to").val("");
									}
								});
								*/
								exp("mail", to);
							} else {
								$("#send_to").val("");
							}
						});
					}
				}},
				{separator: true, bclass: 'right'},
				{name: '', btitle: 'Esporta', bclass: 'function_btn export disabled right', onpress: function(){
					if($(".export").hasClass("disabled")){
						apprise("&Egrave; necessario selezionare almeno un elemento da rimuovere", {"textOk": "Ok"});
					} else {
						apprise('$btns_table', {"animate": "true", "noButton": "true"});
					}
				}}
			],
			onSuccess: function(data){ /*console.log(data); return true;*/ },
			hidable: false,
			showTableToggleBtn: true,
			url: "common/include/funcs/_ajax/AIR/research_results_list.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
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
			$("#research_results_list").flexReload();
		}
	});
	</script>
	<fieldset class="legend collapsed">
		<legend class="info">Informazioni utili</legend>
		<p class="indication">Fare click per visualizzare il contenuto condensato</p>
		<div id="legend_content">
			<p class="time">La tabella si autoaggiorna ogni 10 secondi</p>
			<p class="filter">&Egrave; possibile filtrare i risultati nel menu in fondo alla tabella</p>
			<p class="activated">Selezionando i risultati si attiveranno le funzioni su di essi</p>
		</div>
	</fieldset>
	<table id="research_results_list" class="flexigrid_autorefresh"></table>
Research_list;
} else {
	require_once("common/tpl/AIR/__no_automation_yet.tpl");
}
require_once("common/include/conf/replacing_object_data.php");
?>