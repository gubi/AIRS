<?php
/**
* Generates GUI for all System database listing
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

$pdo = db_connect("");
if ($GLOBALS["user_level"] > 0){
	$tables_list = $pdo->query("show table status");
	$content_body = "<h3>" . $tables_list->rowCount() . " tabelle nel database di sistema</h3>";
	$content_body .= <<<Table_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("#list").flexigrid({
			colModel : [
				{display: 'TABELLA', name: 'table', width: 150, sortable: false, align: 'left'},
				{display: 'DESCRIZIONE', name: 'description', width: 300, sortable: true, align: 'left'},
				{display: 'VALORI', name: 'rows', width: 50, sortable: true, align: 'right'},
				{display: 'ULTIMA MODIFICA', name: 'last_date', width: 150, sortable: false, align: 'right'},
				{display: 'PESO', name: 'size', width: 50, sortable: true, align: 'right'}
			],
			url: "common/include/funcs/_ajax/db_table_list.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 5
		});
	});
	</script>
	<table id="list">$tables</table>
Table_list;

	require_once("common/include/conf/replacing_object_data.php");
} else {
	$show_right_panel = 0;
	$show_right_panel_toc = 0;
	require_once("common/tpl/__401.tpl");
}
?>