<?php
/**
* List all live meetings
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
* @package	AIRS_Meetings
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");
require_once("../../_taglia_stringa.php");
require_once("../../../lib/bbb-api-php/includes/bbb-api.php");
require_once("Text/Wiki.php");
require_once("../../../conf/Wiki/rendering.php");

function is_meeting_running($id){
	$bbb = new BigBlueButton();
	try {
		$is_running = $bbb->isMeetingRunningWithXmlResponseArray($id);
	} catch (exception $e) {
		print $e->getMessage();
	}
	$res = json_decode(json_encode($is_running), 1);
	if($is_running["running"][0] == "true") {
		return true;
	} else {
		return false;
	}
}
function is_meeting_time($end_date){
	if ((($end_date == "0000-00-00 00:00:00") ? strtotime($end_date) : strtotime(date("Y-m-d H:i:s"))) >= strtotime(date("Y-m-d H:i:s"))) { return true; } else { return false; }
}
$bbb = new BigBlueButton();

$pdm = db_connect("meetings");

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
$the_query = "select * from `current_meetings` " . $where . " order by `creation_date` desc, `end_date` desc " . $limit;
$count_query = "select * from `current_meetings` " . $where ;
//print $the_query;
$query_meetings = $pdm->query($the_query);
$check_total = $pdm->query($count_query);
if($query_meetings->rowCount() > 0){
	$r = -1;
	while($dato_meetings = $query_meetings->fetch()){
		if(is_meeting_time($dato_meetings["end_date"])){
			$status = "<img src=\"common/media/img/cancel_32_333.png\" />";
		} else {
			if(is_meeting_running($dato_meetings["id"])) {
				$status = "<img src=\"common/media/img/arrow_sans_right_32_333.png\" />";
			} else {
				$status = "<img src=\"common/media/img/controls_pause_32_333.png\" />";
			}
		}
		$output = $wiki->transform(stripslashes(utf8_decode($dato_meetings["description"])), "Xhtml");
		$output = trim(taglia_stringa(utf8_decode(mb_convert_encoding($output, "UTF-8", "HTML-ENTITIES")), 250));
		
		$r++;
		$res["page"] = $page;
		$res["total"] = $check_total->rowCount();
		$res["total_ids"][] = $r;
		$res["rows"][$r]["cell"]["id"] = $dato_meetings["id"];
		$res["rows"][$r]["cell"]["name"] = '<a href="./Meeting/' . $dato_meetings["name"] . '">' . $dato_meetings["name"] . '</a>';
		$res["rows"][$r]["cell"]["description"] = stripslashes($output);
		$res["rows"][$r]["cell"]["creation_date"] = converti_data(date("D, d M Y H:i:s", strtotime($dato_meetings["creation_date"])), "it", "month_first", "short");
		$res["rows"][$r]["cell"]["end_date"] = ($dato_meetings["end_date"] !== "0000-00-00 00:00:00") ? converti_data(date("D, d M Y H:i:s", strtotime($dato_meetings["end_date"])), "it", "month_first", "short") : "<center>-</center>";
		$res["rows"][$r]["cell"]["user"] = '<a href="./Utente/' . ucwords($dato_meetings["user"]) . '">' . ucwords($dato_meetings["user"]) . '</a>';
		$res["rows"][$r]["cell"]["status"] = $status;
		$res["rows"][$r]["cell"]["voip_no"] = $dato_meetings["dialNumber"];
		$res["rows"][$r]["cell"]["users_no"] = $dato_meetings["maxParticipants"];
		$res["rows"][$r]["cell"]["length"] = "";
	}
} else {
	$res["page"] = 1;
	$res["total"] = 0;
	$res["total_ids"][] = 0;
}
if($_GET["debug"] == "true"){
	print_r($res);
}
print json_encode($res);
?>