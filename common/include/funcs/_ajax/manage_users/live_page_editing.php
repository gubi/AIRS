<?php
/**
* List all users which edits page
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

$page_status = $pdo->query("select * from `airs_content` where `modifying_by` != '' order by `date` desc limit $start, $rp");
$page_status_count = $pdo->query("select * from `airs_content` where `modifying_by` != ''");
if($page_status->rowCount() > 0){
	$res["page"] = $page;
	$res["total"] = $page_status_count->rowCount();
	$tb_count = 0;
	while($dato_page_status = $page_status->fetch()){
		$tb_count++;
		$res["total_ids"][] = $dato_page_status["id"];
		$res["rows"][$tb_count]["id"] = $tb_count;
		$res["rows"][$tb_count]["cell"]["id"] = $dato_page_status["id"];
		$res["rows"][$tb_count]["cell"]["user"] = $dato_page_status["modifying_by"];
		$page_link[$dato_page_status["title"]][] = $dato_page_status["name"];
		if (strlen($dato_page_status["subname"]) > 0){
			$page_link[$dato_page_status["title"]][] = $dato_page_status["subname"];
		}
		if (strlen($dato_page_status["sub_subname"]) > 0){
			$page_link[$dato_page_status["title"]][] = $dato_page_status["sub_subname"];
		}
		
		foreach($page_link as $title => $link){
			$res["rows"][$tb_count]["cell"]["page"] = "<a href=\"" . join("/", $link) . " title=\"" . $title . "\">" . $title . "</a>";
		}
		$res["rows"][$tb_count]["cell"]["last_edit"] = converti_data(date("D, d M Y \a\l\l\e H:i:s", strtotime($dato_page_status["date"])), "it", "month_first", "short");
		
		$datetime1 = new DateTime(date("Y-m-d", strtotime($dato_page_status["date"])));
		$datetime2 = new DateTime(date("Y-m-d"));
		$interval = $datetime1->diff($datetime2);
		
		$this_month_days = date("t",strtotime(date("m/d/Y")));
		// Se il numero di giorni è parte del numero dei giorni del mese corrente
		if($interval->format("%a") <= $this_month_days){
			$time_diff = $interval->format("%a");
			if($time_diff == 1){
				$time_diff .= " giorno fa";
			} else {
				$time_diff .= " giorni fa";
			}
		} else if($interval->format("%m") <= 12){
			// Se il numero di mesi è parte dell'anno
			$time_diff = $interval->format("%m");
			if($time_diff == 1){
				$time_diff .= " mese fa";
			} else {
				$time_diff .= " mesi fa";
			}
		} else {
			$time_diff = $interval->format("%y");
			if($time_diff == 1){
				$time_diff .= " anno fa";
			} else {
				$time_diff .= " anni fa";
			}
		}
		
		$res["rows"][$tb_count]["cell"]["days_diff"] = $time_diff;
		
		// Strumenti
		$instruments_table ="<table cellpadding=\"0\" cellspacing=\"0\" class=\"controls\"><tr class=\"odd\">";
			$instruments_table .= "<td class=\"eject\" title=\"Sgancia questa pagina\" onclick=\"eject('" . $dato_page_status["id"] .  "');\"></td>";
			$instruments_table .= "<td class=\"logout\" title=\"Disconnetti utente (e sgancia tutte le pagine a lui collegate)\" onclick=\"disconnect('" . $dato_page_status["modifying_by"] .  "');\"></td>";
		$instruments_table .="</tr>";
		$instruments_table .= "</table>";
		$res["rows"][$tb_count]["cell"]["instruments"] = $instruments_table;
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