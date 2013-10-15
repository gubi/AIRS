<?php
/**
* Generates main log page
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

$content_body .= <<<Table_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("#log_list").flexigrid({
			colModel : [
				{display: 'ID', name : 'id', width : 32, sortable: false, align: 'center'},
				{display: 'TIPO', name : 'type', width : 300, sortable: true, align: 'left'},
				{display: 'VALORI', name: 'values', width: 50, sortable: true, align: 'right'},
				{display: 'ULTIMA MODIFICA', name: 'last_date', width: 150, sortable: false, align: 'left'},
				{display: 'PESO', name: 'size', width: 50, sortable: true, align: 'right'}
			],
			url: "common/include/funcs/_ajax/log/table_list.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 5
		});
	});
	</script>
	<table id="log_list"></table>
Table_list;

require_once("common/include/conf/replacing_object_data.php");
?>