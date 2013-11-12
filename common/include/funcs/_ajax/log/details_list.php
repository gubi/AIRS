<?php
/**
* List all log from databse
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
require_once("../../_create_link.php");
require_once("../../_taglia_stringa.php");

if (isset($_GET["table"]) && trim($_GET["table"]) !== ""){
	$pdsl = db_connect("system_logs");
	
	foreach($_GET as $_getk => $_getv){
		$_POST[$_getk] = $_getv;
	}
	$page = isset($_POST["page"]) ? $_POST["page"] : 1;
	$query = isset($_POST["query"]) ? $_POST["query"] : false;
	$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;

	$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
	$start = (($page-1) * $rp);
	$limit = "limit $start, $rp";
		if ($query) {
			$where = " where $qtype like '%" . addslashes($query) . "%' ";
		} else {
			$where = "";
		}
	$the_query = "select * from `" . addslashes($_GET["table"]) . "` " . $where . " order by `data` desc, `ora` desc " . $limit;
	$count_query = "select * from `" . addslashes($_GET["table"]) . "` " . $where ;
	
	$query_log = $pdsl->query($the_query);
	$check_total = $pdsl->query($count_query);
	if($query_log->rowCount() > 0){
		$r = -1;
		while($dato_log_tables = $query_log->fetch()){
			$r++;
			$res["page"] = $page;
			$res["total"] = $check_total->rowCount();
			$res["total_ids"][] = $r;
			$res["rows"][$r]["id"] = $r;
			$table_data = $pdsl->query("show full columns from `" . addslashes($_GET["table"]) . "`");
			if ($table_data->rowCount() > 0){
				while($dato_table = $table_data->fetch()){
					switch($dato_table["Field"]){
						case "data":
							$field = converti_data(date("D, d M Y", strtotime($dato_log_tables[$dato_table["Field"]])), "it", "month_first", "short");
							break;
						case "user":
							if(strlen($dato_log_tables[$dato_table["Field"]]) > 0){
								$field = "<a href=\"./Utente/" . ucfirst($dato_log_tables[$dato_table["Field"]]) . "\" title=\"Vai alla pagina dell'utente\">" . ucfirst($dato_log_tables[$dato_table["Field"]]) . "</a>";
							} else {
								$field = "<center>-</center>";
							}
							break;
						case "to":
							$field = str_replace("<<", " <<", create_link($dato_log_tables[$dato_table["Field"]]));
							break;
						case "referer":
							$field = "<a href=\"./" . $dato_log_tables[$dato_table["Field"]] . "\" title=\"Vai a questa pagina\">" . urldecode($dato_log_tables[$dato_table["Field"]]) . "</a>";
							break;
						case "body":
							$field = taglia_stringa(utf8_decode(nl2br(create_link(mb_convert_encoding(quoted_printable_decode($dato_log_tables[$dato_table["Field"]]), "UTF-8", mb_detect_encoding($dato_log_tables[$dato_table["Field"]]))))), 600);
							break;
						default:
							$field = nl2br(create_link(mb_convert_encoding(quoted_printable_decode($dato_log_tables[$dato_table["Field"]]), "UTF-8", mb_detect_encoding($dato_log_tables[$dato_table["Field"]]))));
							break;
					}
					$res["rows"][$r]["cell"][$dato_table["Field"]] = stripslashes($field);
				}
			}
		}
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