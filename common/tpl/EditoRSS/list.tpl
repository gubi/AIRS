<?php
/**
* List all feed
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

$pdo = db_connect("editorss");
$check_feed = $pdo->query("select * from `editorss_feeds` where `user` = '" . addslashes($decrypted_user) . "'");
if ($check_feed->rowCount() > 0){
	require_once("common/tpl/_component_toolbar.tpl");
	$content_title = "Elenco dei feeds";
	$content_body = toolbar("export_to_file.php", "editorss", "editorss_feeds", str_replace(" ", "_", strtolower($content_title)));
	$content_body .= <<<Feed_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function disableLink(e) {
		e.preventDefault();
		return false;
	}
	function goTo(link, blank){
		if (blank == "true"){
			window.open(link);
		} else {
			location.href = link
		}
		return false;
	}
	function retake_feed(the_id, title){
		$.get("common/include/funcs/_ajax/EditoRSS/able_unable_feed.php", {id: the_id, action: '1', type: '$check_type'}, function(data){
			if (data != "error"){
				$("#_" + the_id + " tr.force_delete").fadeOut(600, function(){
					$("#_" + the_id).find("td.restore").attr("title", "Marca per la rimozione").attr("onclick", "deleteItem('" + the_id + "', '" + title + "')").switchClass("restore", "cancel", 900, function(){
						$("#_" + the_id + " tr.actions").fadeIn(300);
						$("#_" + the_id + " td a.button").attr("disabled", "");
						$("#_" + the_id).switchClass("inactive", "", 900).find("td.cancel").switchClass("disable", "", 900);
						var current_status = $("#_" + the_id + " td:first-child").attr("class");
						$("#_" + the_id + " .actions td").each(function(){
							var this_ = $(this).attr("class");
							this_ = this_.replace("disable", "");
							this_ = $.trim(this_);
							if (this_ != current_status){
								$("#_" + the_id + " .actions td." + this_).switchClass("disable", "", 300);
							}
						});
					});
				});
			}
		});
	}
	function alterate_scan(the_id, act){
		$.get("common/include/funcs/_ajax/EditoRSS/play_stop_pause_scan.php", {action: act, id: the_id, type: '$check_type'}, function(data){
			if (data != "error" && data != "no action"){
				var current_status = $("#_" + the_id + " td:first-child").attr("class");
				if (current_status != data){
					$("#_" + the_id + " .actions td").each(function(){
						var this_ = $(this).attr("class");
						this_ = this_.replace("disable", "");
						this_ = $.trim(this_);
						if (this_ != data){
							$("#_" + the_id + " .actions td." + this_).switchClass("disable", "", 300);
						}
						$("#_" + the_id + " > td." + current_status).switchClass(current_status, data, 300);
						$("#_" + the_id + " .actions td." + data).switchClass("", "disable", 300);
					});
				}
			}
		});
	}
	function deleteItem(the_id, title){
		apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/questionmark_128_ccc.png\" /></td><td valign=\"top\"><h1>Si &grave; sicuri di voler marcare $type_txt come &quot;rimosso&quot;?</h1><br />$type_txt_ucfirst <b>&quot;" + title + "&quot;</b> non verrà definitivamente cancellato, ma marcato come inattivo.<br >Le automazioni afferenti non saranno più attive.</td></tr></table>", {"verify": true}, function(r){
			if (r){
				$.get("common/include/funcs/_ajax/EditoRSS/able_unable_feed.php", {id: the_id, action: '0', type: '$check_type'}, function(data){
					if (data != "error"){
						$("#_" + the_id).find("td.cancel").attr("title", "Ripristina $type_txt cancellato").attr("onclick", "retake_feed('" + the_id + "', '" + title + "')").switchClass("cancel", "restore", 900, function(){
							$("#_" + the_id + " tr.actions").fadeOut(300);
							$("#_" + the_id + " tr.force_delete").fadeIn(300);
							$("#_" + the_id + " td a.button").bind("click", disableLink).attr("disabled", "disabled");
							$("#_" + the_id).switchClass("", "inactive", 900).find("td.stop").switchClass("", "disable", 900).parent("tr").find("td.play").switchClass("", "disable", 900).parent("tr").find("td.pause").switchClass("", "disable", 900);
						});
					}
				});
			}
		});
	}
	function force_remove(the_id, title){
		apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/cancel_128_ccc.png\" /></td><td valign=\"top\"><h1>Si &grave; sicuri di voler rimuovere definitivamente $type_txt?</h1><br />$type_txt_ucfirst <b>&quot;" + title + "&quot;</b> verrà definitivamente cancellato insieme alle news e le automazioni afferenti.</td></tr></table>", {"verify": true}, function(r){
			if (r){
				$.get("common/include/funcs/_ajax/EditoRSS/remove.php", {id: the_id, type: '$check_type'}, function(data){
					if (data != "error"){
						$("#_" + the_id).fadeOut(900, function(){
							$(this).remove();
						});
					}
				});
			}
		});
	}
	$(document).ready(function() {
		$("#table_feed_list").flexigrid({
			colModel : [
				{display: 'ID', name: 'id', width: 32, sortable: false, align: 'center'},
				{display: 'TITOLO', name: 'titolo', width: 130, sortable: true, align: 'left'},
				{display: 'DESCRIZIONE', name: 'descrizione', width: 250, sortable: true, align: 'left'},
				{display: 'TAGS', name: 'tags', width: 120, sortable: false, align: 'left'},
				{display: 'GRUPPI', name: 'group', width: 120, sortable: true, align: 'left'},
				{display: 'DATA', name: 'data', width: 100, sortable: true, align: 'left'},
				{display: '', name: 'actions', width: 100, sortable: true, align: 'center'}
			],
			searchitems : [
				{display: 'Titolo della news', name : 'result_link_text'},
				{display: 'Descrizione della news', name : 'result_description', isdefault: true}
			],
			url: "common/include/funcs/_ajax/EditoRSS/feed_listing.php?type=feed",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 5
		});
	});
	</script>
	<table id="table_feed_list"></table>
Feed_list;
} else {
	$content_body = <<<No_feed
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/document_feed_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				Non &egrave; stato trovato nessun risultato.<br />
				Questo &grave; possibile perch&eacute; &grave; stato selezionato un filtro che non riporta risultati oppure non &grave; stato ancora importato nessun feed.<br />
				<a href="EditoRSS/Feeds/Aggiungi_feed">Aggiungi dei feed RSS</a>.
			</td>
		</tr>
	</table>
No_feed;
}
require_once("common/include/conf/replacing_object_data.php");
?>