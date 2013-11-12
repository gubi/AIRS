<?php
/**
* List all log databse tables
* 
* PHP versions 5
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
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");

$pdsl = db_connect("system_logs");
$log_list = $pdsl->query("show table status");
if($log_list->rowCount() > 0){
	$res["page"] = 1;
	$res["total"] = $log_list->rowCount();
	$tb_count = 0;
	while($dato_log_tables = $log_list->fetch()){
		$tb_count++;
		$res["total_ids"][] = $dato_log_tables["id"];
		$res["rows"][$tb_count]["id"] = $tb_count;
		$res["rows"][$tb_count]["cell"]["id"] = $tb_count;
		if ($dato_log_tables["Rows"] == 0){
			$res["rows"][$tb_count]["cell"]["type"] = $dato_log_tables["Comment"];
		} else {
			$res["rows"][$tb_count]["cell"]["type"] = "<a href=\"./Sistema/Log/" . $tb_count . "\" title=\"Visualizza questo registro\">" . $dato_log_tables["Comment"] . "</a>";
		}
		$res["rows"][$tb_count]["cell"]["values"] = $dato_log_tables["Rows"];
		$res["rows"][$tb_count]["cell"]["last_date"] = converti_data(date("D, d M Y \a\l\l\e H:i:s", strtotime($dato_log_tables["Update_time"])), "it", "month_first", "short");
		$res["rows"][$tb_count]["cell"]["size"] = round(($dato_log_tables["Data_length"]/1024), 2) . " Kb";
		//print_r($dato_log_tables);
	}
} else {
	$res["page"] = $page;
	$res["total"] = 0;
	$res["total_ids"] = array();
}
if($_GET["debug"] == "true"){
	print_r($res);
}
print json_encode($res);
?>