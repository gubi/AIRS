<?php
/**
* Generates detailed log list
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
* @package	AIRS_Logs
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

$pdsl = db_connect("system_logs");
$log_list = $pdsl->query("show table status");
if($log_list->rowCount() > 0){
	$tb_count = 0;
	while($dato_log_tables = $log_list->fetch()){
		$tb_count++;
		if ($tb_count == $GLOBALS["page_q"]){
			$table_name = $dato_log_tables["Name"];
			$content_title = $dato_log_tables["Comment"];
		}
	}
}
$table_data = $pdsl->query("show full columns from `" . addslashes($table_name) . "`");
if ($table_data->rowCount() > 0){
	while($dato_table = $table_data->fetch()){
		$title = strstr($dato_table["Comment"], "::", true);
		$len = str_replace("::", "", strstr($dato_table["Comment"], "::"));
		$check_content = $pdsl->query("select `" . $dato_table["Field"] . "` from `" . $table_name . "` limit 1");
		while($dato_check_content = $check_content->fetch()){
			if (is_numeric($dato_check_content[$dato_table["Field"]])){
				$align = "right";
			} else {
				$align = "left";
			}
		}
		$table_head[] = "{display: '" . strtoupper($title) . "', name: '" . $dato_table["Field"] . "', width: " . $len . ", sortable: false, align: '" . $align . "'}";
	}
}
$table_header = join(",", $table_head);
require_once("common/tpl/_component_toolbar.tpl");
$content_body = toolbar("export_to_file.php", "system_logs", $table_name, str_replace(" ", "_", strtolower($content_title)));
$content_body .= <<<Detail_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("#detail_list").flexigrid({
			colModel : [
				$table_header
			],
			url: "common/include/funcs/_ajax/log/details_list.php?table=$table_name",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 10
		});
		setInterval(function() {
			$("#detail_list").flexReload();
		}, 10000);
	});
	</script>
	La tabella si autoaggiorna ogni 10 secondi<br /><br />
	<table id="detail_list"></table>
Detail_list;

require_once("common/include/conf/replacing_object_data.php");
?>