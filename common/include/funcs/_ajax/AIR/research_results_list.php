<?php
/**
* List all research results
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
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: application/json; charset=utf8;");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");
require_once("../../_create_link.php");

foreach($_GET as $_getk => $_getv){
	$_POST[$_getk] = $_getv;
}
$page = isset($_POST["page"]) ? $_POST["page"] : 1;
$query = isset($_POST["query"]) ? $_POST["query"] : false;
$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;
$sortorder = isset($_POST["sortorder"]) ? $_POST["sortorder"] : "desc";
if(isset($_POST["sortname"]) && trim($_POST["sortname"]) !== "undefined") {
	switch($_POST["sortname"]){
		case "id":
			$sortname = "o.id";
			break;
		case "ricerca":
			$sortname = "c.id";
			break;
		case "search_engine":
			$sortname = "search_engine_id";
			break;
		case "risultati":
			$sortname = "result_link_text";
			break;
		case "copia_cache":
			$sortname = "result_cache_html_content";
			break;
		case "utente":
			$sortname = "c.user";
			break;
		case "data":
			$sortname = "date";
			break;
		default:
			$sortname = $_POST["sortname"];
			break;
	}
} else {
	$sortname = "o.id";
}

$rp = isset($_POST['rp']) ? $_POST['rp'] : 10;
$start = (($page-1) * $rp);
$limit = "limit $start, $rp";
	if ($query) {
		$where = "where " . $qtype . " like '%" . addslashes($query) . "%' ";
	} else {
		$where = "";
	}
//$the_query = "select * as 'air_research_results_(*)' from `air_research`, `air_research_results` " . $where . $limit;
$the_query = "select *, o.id as 'oid', o.tags as 'otags' from `air_research_results` as o join `air_research` as c on o.research_id = c.id " . $where . " order by " . $sortname . " " . $sortorder . " " . $limit;
$stats_query = "select *, o.id as 'oid', o.tags as 'otags' from `air_research_results` as o join `air_research` as c on o.research_id = c.id " . $where . " order by " . $sortname . " " . $sortorder;
$count_query = "select count(o.id) as `total` from `air_research_results` as o join `air_research` as c on o.research_id = c.id " . $where ;
//print $count_query . "\n\n";
$pdo = db_connect("air");
$check_res = $pdo->prepare($the_query);
if(!$check_res->execute()){
	$check_res = $pdo->prepare($the_query2);
}
$check_stats = $pdo->prepare($stats_query);
$check_stats->execute();
$check_total = $pdo->prepare($count_query);
$check_total->execute();
if ($check_res->rowCount() > 0){
	$r = -1;
	while ($dato_check_res = $check_res->fetch()) {
		$tag_ = "";
		$keyword_ = "";
		$r++;
		$res["page"] = $page;
		while ($dato_check_total = $check_total->fetch()) {
			$res["total"] = $dato_check_total["total"];
		}
		while ($dato_check_stats = $check_stats->fetch()) {
			$res["total_ids"][] = $dato_check_stats["oid"];
		}
		$res["rows"][$r]["id"] = $dato_check_res["oid"];
		// Controlla la tabella delle ricerche impostate
		$research = $pdo->query("select * from `air_research` where `id` = '" . addslashes($dato_check_res["research_id"]) . "'");
		if ($research->rowCount() > 0){
			while ($dato_research = $research->fetch()){
				// Acquisisce l'id del Motore di ricerca di riferimento
				$search_engines_arr = explode(",", $dato_research["search_engines"]);
				$res["rows"][$r]["cell"]["ricerca"] = "<a href=\"/AIR/Ricerche/" . $dato_check_res["research_id"] . "\" title=\"Vai alla scheda della ricerca\">" . $dato_research["title"] . "</a>";
			}
		}
		$search_engine = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($dato_check_res["search_engine_id"]) . "'");
		if ($search_engine->rowCount() > 0){
			while ($dato_se = $search_engine->fetch()){
				$res["rows"][$r]["cell"]["search_engine"] =  "<a href=\"" . $dato_se["search_page"] . "\" target=\"_blank\" title=\"Visualizza la ricerca con questo motore\">" . $dato_se["name"] . "</a>";
				$res["rows"][$r]["cell"]["query"] = "<a href=\"" . $dato_check_res["search_uri"] . "\" target=\"_blank\">" . $dato_check_res["query"] . "</a>";
			}
		}
		$result_uri = $dato_check_res["result_uri"];
		$result_page = $dato_check_res["result_page"];
		
		$result_link_text = $dato_check_res["result_link_text"];
		$result_description = $dato_check_res["result_description"];
		$date_arr = explode(" ", $dato_check_res["date"]);
		$date_a = explode("-", $date_arr[0]);
		$d = $date_a[2];
		$m = $date_a[1];
		$Y = $date_a[0];
		
		$res["rows"][$r]["cell"]["id"] = $dato_check_res["oid"];
		$res["rows"][$r]["cell"]["risultati"] = "<a href=\"/AIR/Risultati_delle_ricerche/" . $dato_check_res["id"] . "\" title=\"Visualizza il risultato della ricerca\">" . stripslashes($result_link_text) . "</a><br /><span style=\"color: #666;\">" . $dato_check_res["result_description"] . "</span><br /><cite><a style=\"color: #009933 !important;\" href=\"" . $result_uri . "\" target=\"_blank\" title=\"Collegamento al risultato\">" . $result_uri . "</a></cite>";
		if(trim($dato_check_res["result_cache_uri"]) !== ""){
			$res["rows"][$r]["cell"]["copia_cache"] = "<a href=\"" . $dato_check_res["result_cache_uri"] . "\" target=\"_blank\">" . stripslashes($dato_check_res["result_link_text"]) . " [cache]</a>";
		} else {
			$res["rows"][$r]["cell"]["copia_cache"] = "<center>-</center>";
		}
			if (strlen($dato_check_res["otags"]) > 0){
				if($dato_check_res["otags"] !== "Forbidden,Don\'t,Have,Permission,Access,This,Server"){
					$tags = explode(",", $dato_check_res["otags"]);
					$t = 0;
					foreach($tags as $tag){
						$t++;
						if($t < 6){
							$tag_ .= "<span class=\"tag\">" . trim(stripslashes($tag)) . "</span>  ";
						}
					}
					if(count($tags) > 5){
						$tag_ .= "<span class=\"tag\">...</span>  ";
					}
					$res["rows"][$r]["cell"]["tags"] = $tag_;
				} else {
					$res["rows"][$r]["cell"]["tags"] = "<center>-</center>";
				}
			} else {
				$res["rows"][$r]["cell"]["tags"] = "<center>-</center>";
			}
		if (strlen($dato_check_res["keywords"]) > 0){
			if($dato_check_res["keywords"] !== "Forbidden,Don't,Have,Permission,Access,This,Server"){
				$keywords = explode(",", $dato_check_res["keywords"]);
				$k = 0;
				foreach($keywords as $keyword){
					$k++;
					if($k < 6) {
						$keyword_ .= "<span class=\"tag\">" . trim(stripslashes($keyword)) . "</span>  ";
					}
				}
				if(count($keywords) > 5){
					$keyword_ .= "<span class=\"tag\">...</span>  ";
				}
				$res["rows"][$r]["cell"]["keywords"] = $keyword_;
			}
		} else {
			$res["rows"][$r]["cell"]["keywords"] = "<center>-</center>";
		}
		$res["rows"][$r]["cell"]["words_count"] = ucfirst($dato_check_res["words_count"]);
		$res["rows"][$r]["cell"]["utente"] = ucfirst($dato_check_res["user"]);
		$res["rows"][$r]["cell"]["data"] = $d . "/" . $m . "/" . $Y . " " . $date_arr[1];
	}
} else {
	$res["page"] = $page;
	$res["total"] = 0;
	$res["total_ids"] = array();
}
$ress = @array_unique($res);
if($res == null){
	$res["page"] = 1;
	$res["total"] = 0;
	$res["total_ids"] = 0;
}
if($_GET["debug"] == "true"){
	print_r($res);
}
print json_encode($res);
?>