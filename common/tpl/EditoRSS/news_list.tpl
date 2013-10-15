<?php
/**
* List all news
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

require_once("Pager.php");
$pagerOptions = array("mode" => "Sliding", "delta"   => 2, "perPage" => 20);

$pdo = db_connect("editorss");
$is_conditioned = 0;
$check_news = $pdo->query("select * from `editorss_feeds_news` where `user` = '" . addslashes($decrypted_user) . "'");
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
if ($check_news->rowCount() > 0){
	require_once("common/include/funcs/_converti_data.php");
	require_once("common/include/funcs/_create_link.php");
	require_once("common/include/funcs/_taglia_stringa.php");
	
	require_once("common/tpl/_component_toolbar.tpl");
	$content_title = "Elenco news scansionate";
	$content_body = toolbar("export_to_file.php", "editorss", "editorss_feeds_news", str_replace(" ", "_", strtolower($content_title)));
	$content_body .= <<<News_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function remove_news(the_id){
		$.get("common/include/funcs/_ajax/EditoRSS/remove.php", {type: "news", id: the_id}, function(data){
			if (data == "ok"){
				apprise("News rimossa con successo", {"animate": "true"}, function(r){
					if (r){
						$("#row" + the_id).fadeOut(600, function(){
							$(this).remove();
						});
					}
				});
			}
		})
	}
	function count_checked() {
		var n = $(".sel > input:checked").length;
		if (n > 0){
			$("#exp_btn").fadeIn(300);
		} else {
			$("#exp_btn").fadeOut(300);
		}
	}
	Array.prototype.remove = function(v) { this.splice(this.indexOf(v) == -1 ? this.length : this.indexOf(v), 1); };
	var ids = new Array();
	function export_news(){
		$("body").append('<form id="exportform" action="common/include/funcs/_ajax/EditoRSS/export_news_to_csv.php" method="get"><input type="hidden" id="ids" name="ids" value="' + $("#selected_ids").val() + '" /></form>');
		$("ids").val($("#selected_ids").val());
		$("#exportform").submit().remove();
		return true;
	}
	$(document).ready(function() {
		count_checked();
		$(".sel > input:checkbox").click(count_checked);
		
		$("#table_news_list").flexigrid({
			colModel : [
				{display: 'ID', name : 'id', width : 18, sortable : true, align: 'right'},
				{display: 'FEED', name : 'feed', width : 120, sortable : false, align: 'left'},
				{display: 'TITOLO', name : 'titolo', width : 130, sortable : true, align: 'left'},
				{display: 'DESCRIZIONE', name : 'descrizione', width : 250, sortable : true, align: 'left'},
				{display: 'URI', name : 'uri', width : 250, sortable : false, align: 'left'},
				{display: 'DATA', name : 'data', width : 100, sortable : true, align: 'left'},
				{display: '', name : 'actions', width : 45, sortable : false, align: 'left'}
			],
			searchitems : [
				{display: 'Titolo della news', name : 'title', isdefault: true},
				{display: 'Descrizione della news', name : 'description'}
			],
			url: "common/include/funcs/_ajax/EditoRSS/feed_listing.php?type=news",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 5
		});
		$(".fht-tfoot input").remove();
		$("#table_news_list tr").click(function(){
			select_row_news($(this).attr("id"));
			count_checked();
		});
	});
	</script>
	<div id="tableBlock">
		<table id="table_news_list"></table>
	</div>
	<div id="exp_btn">
		<input type="hidden" id="selected_ids" value="" />
		<a href="javascript: void(0);" class="download" onclick="export_news();" title="ESPORTA IN CSV">
			<img src="common/media/img/download_32.png" />
		</a>
	</div>
News_list;
} else {
	$content_body = <<<No_news
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/document_feed_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				Non sono ancora presenti delle news.
			</td>
		</tr>
	</table>
No_news;
}

require_once("common/include/conf/replacing_object_data.php");
?>