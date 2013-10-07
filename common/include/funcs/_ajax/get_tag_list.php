<?php
/**
* Returns a list of used tags
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
* @SLM_status	testing
*/
header("Content-type: application/json; charset=utf8;");
require_once("../../.mysql_connect.inc.php");
require_once("../_converti_data.php");
require_once("../_taglia_stringa.php");
require_once("../_make_url_friendly.php");

foreach($_GET as $_getk => $_getv){
	$_POST[$_getk] = $_getv;
}
$page = isset($_POST["page"]) ? $_POST["page"] : 1;
$query = isset($_POST["query"]) ? $_POST["query"] : false;
$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;

$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
$start = (($page-1) * $rp);
$limit = "limit $start, $rp";
	if ($query) {
		$where = " where $qtype like '%" . addslashes($query) . "%' ";
	} else {
		$where = " where `tags` != ''";
	}
	
	$db["pdo"] = db_connect("");
	$db["pdoa"] = db_connect("air");
	$db["pdoe"] = db_connect("editorss");
		$params = array(
			"editorss_feeds" => "pdoe", 
			"editorss_feeds_news" => "pdoe", 
			"air_research" => "pdoa"
		);
	foreach ($params as $table => $database){
		$the_query = "select `tags` from `" . $table . "` " . $where . $limit;
		
		$check_res = $db[$database]->query($the_query);
		$check_total = $check_res->rowCount();
		if ($check_res->rowCount() > 0){
			$r = -1;
			$res["total"] = $check_total;
			while ($dato_check_res = $check_res->fetch()) {
				$r++;
				$res["page"] = $page;
				$total_tags = explode(",", $dato_check_res["tags"]);
				foreach($total_tags as $ttags){
					$tag[] = ucfirst(trim($ttags));
				}
				/*
				$res["rows"][$r]["cell"]["descrizione"] = "<span title=\"" . $dato_check_res["description"] . "\">" . taglia_stringa($dato_check_res["description"], 300) . "</span>";
				$res["rows"][$r]["cell"]["uri"] = "<a href=\"" . $dato_check_res["link"] . "\" title=\"Visualizza la pagina della news\" target=\"_blank\">" . $dato_check_res["link"] . "</a>";
				$res["rows"][$r]["cell"]["data"] = $date;
				$res["rows"][$r]["cell"]["actions"] = "<a class=\"edit\" title=\"Modifica news\" href=\"./EditoRSS/News/Modifica:" . $dato_check_res["id"] .  "\"></a>&emsp;<a class=\"cancel\" title=\"Elimina news\" href=\"javascript:void(0);\" onclick=\"remove_news('" . $dato_check_res["id"] .  "');\"></a>";
				*/
			}
		}
	}
	$total_presence = array_count_values($tag);
	$unique = array_unique($tag);
	$r = 0;
	foreach($unique as $tg){
		$r++;
		$res["rows"][$r]["cell"]["tags"] = "<a href=\"./Tags/" . ucfirst($tg) . "\" title=\"Visualizza la scheda del tag\">" . $tg . "</a>";
		$res["rows"][$r]["cell"]["total_presence"] = $total_presence[$tg];
	}
//print_r($res);
print json_encode($res);
?>