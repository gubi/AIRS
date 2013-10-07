<?php
/**
* List all users
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
* @package	AIRS_manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");

$pdo = db_connect("");

$page = isset($_POST["page"]) ? $_POST["page"] : 1;
$query = isset($_POST["query"]) ? $_POST["query"] : false;
$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;

$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
$start = (($page-1) * $rp);
$limit = "limit $start, $rp";

$users_list = $pdo->query("select * from `airs_users` limit $start, $rp");
$users_list_count = $pdo->query("select * from `airs_users`");
if($users_list->rowCount() > 0){
	$res["page"] = $page;
	$res["total"] = $users_list_count->rowCount();
	$tb_count = 0;
	while($dato_users = $users_list->fetch()){
		$users_level = $pdo->query("select * from `airs_users_level` where `level` = '" . addslashes($dato_users["level"]) . "'");
		while ($dato_level = $users_level->fetch()){
			$level = $dato_level["level_txt"];
		}
		$tb_count++;
		$res["total_ids"][] = $dato_users["id"];
		$res["rows"][$tb_count]["id"] = $tb_count;
		$res["rows"][$tb_count]["cell"]["id"] = $dato_users["id"];
		$res["rows"][$tb_count]["cell"]["name"] = ucwords(strtolower($dato_users["name"] . " " . $dato_users["lastname"]));
		$res["rows"][$tb_count]["cell"]["username"] = "<a href=\"./Utente/" . ucfirst(strtolower($dato_users["username"])) . "\" title=\"Visualizza la scheda di questo utente\">" . ucfirst(strtolower($dato_users["username"])) . "</a>";
		if ($dato_users["is_active"] == 1){
			$is_active_txt = "attivo";
		} else {
			$is_active_txt = "in attesa di attivazione";
		}
		$res["rows"][$tb_count]["cell"]["is_active"] = $is_active_txt;
		if ($dato_users["is_connected"] == 1){
			$is_connected_txt = "connesso";
		} else {
			$is_connected_txt = "disconnesso";
		}
		$res["rows"][$tb_count]["cell"]["is_connected"] = $is_connected_txt;
		$res["rows"][$tb_count]["cell"]["last_access"] = converti_data(date("D, d M Y \a\l\l\e H:i:s", strtotime($dato_users["date"])), "it", "month_first", "short");
		if(strlen($dato_users["created_by"]) > 0){
			$created_by = "<a href=\"./Utente/" . ucfirst(strtolower($dato_users["created_by"])) . "\" title=\"Visualizza la scheda di questo utente\">" . ucfirst(strtolower($dato_users["created_by"])) . "</a>";
		} else {
			$created_by = " - ";
		}
		$res["rows"][$tb_count]["cell"]["created_by"] = $created_by;
	}
}
//print_r($res);
print json_encode($res);
?>