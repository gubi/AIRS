<?php
/**
* List all users
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
* @package	AIRS_Manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$content_body = <<<Table_list
	<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	$(document).ready(function() {
		$("#user_list").flexigrid({
			colModel : [
				{display: 'ID', name: 'id', width: 32, sortable: false, align: 'center'},
				{display: 'USERNAME', name: 'username', width: 75, sortable: true, align: 'left'},
				{display: 'NOME', name: 'name', width: 150, sortable: true, align: 'left'},
				{display: 'ATTIVO', name: 'is_active', width: 65, sortable: false, align: 'center'},
				{display: 'CONNESSO', name: 'is_connected', width: 65, sortable: true, align: 'center'},
				{display: 'ULTIMO ACCESSO', name: 'last_access', width: 100, sortable: true, align: 'right'},
				{display: 'INVITATO DA', name: 'created_by', width: 75, sortable: true, align: 'left'}
			],
			url: "common/include/funcs/_ajax/manage_users/users_list.php",
			dataType: "json",
			sortorder: "asc",
			usepager: true,
			useRp: true,
			singleSelect: true,
			rp: 5
		});
		setInterval(function() {
			$("#user_list").flexReload();
		}, 10000);
	});
	</script>
	L'elenco si autoaggiorna ogni 10 secondi
	<table id="user_list"></table>
Table_list;

require_once("common/include/conf/replacing_object_data.php");
?>