<?php
/**
* List files uploaded files
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
* @author		Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/


$content_body = <<<Table_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("#files_list").flexigrid({
			colModel : [
				{display: 'ID', name: 'id', width: 32, sortable: false, align: 'center'},
				{display: 'FILE', name: 'file', width: 250, sortable: true, align: 'left'},
				{display: 'TAGS', name: 'tags', width: 150, sortable: true, align: 'left'},
				{display: 'CARICATO DA', name: 'uploaded_by', width: 125, sortable: true, align: 'left'},
				{display: 'DATA', name: 'date', width: 100, sortable: true, align: 'right'}
			],
			url: "common/include/funcs/_ajax/files_list.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			filesp: true,
			singleSelect: true,
			rp: 15
		});
		setInterval(function() {
			$("#files_list").flexReload();
		}, 10000);
	});
	</script>
	<p>L'elenco si autoaggiorna ogni 10 secondi</p>
	<table id="files_list"></table>
Table_list;

require_once("common/include/conf/replacing_object_data.php");
?>