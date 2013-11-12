<?php
/**
* List feeds
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: application/json");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");
require_once("../../_taglia_stringa.php");
require_once("../../_make_url_friendly.php");

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
			$sortname = "id";
			break;
		case "titolo":
			$sortname = "title";
			break;
		case "descrizione":
			$sortname = "description";
			break;
		case "data":
			$sortname = "date";
			break;
		default:
			$sortname = $_POST["sortname"];
			break;
	}
} else {
	$sortname = "id";
}

$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
$start = (($page-1) * $rp);
$limit = "limit $start, $rp";
	if ($query) {
		$where = " where $qtype like '%" . addslashes($query) . "%' ";
	} else {
		$where = "";
	}
if (isset($_GET["type"]) && trim($_GET["type"]) !== ""){
	switch($_GET["type"]){
		case "news":
			$the_query = "select * from `editorss_feeds_news` " . $where . " order by " . $sortname . " " . $sortorder . " " . $limit;
			$stats_query = "select *, id as 'oid', tags as 'otags' from `editorss_feeds_news` " . $where . " order by " . $sortname . " " . $sortorder;
			$count_query = "select count(`id`) as `total` from `editorss_feeds_news` " . $where ;
			
			$pdo = db_connect("editorss");
			$check_res = $pdo->query($the_query);
			$check_stats = $pdo->prepare($stats_query);
			$check_stats->execute();
			$check_total = $pdo->prepare($count_query);
			$check_total->execute();
			if ($check_res->rowCount() > 0){
				$r = -1;
				while ($dato_check_res = $check_res->fetch()) {
					$r++;
					$res["page"] = $page;
					while ($dato_check_total = $check_total->fetch()) {
						$res["total"] = $dato_check_total["total"];
					}
					while ($dato_check_stats = $check_stats->fetch()) {
						$res["total_ids"][] = $dato_check_stats["oid"];
					}
					$res["rows"][$r]["id"] = $dato_check_res["id"];
					$result_uri = $dato_check_res["link"];
					$result_page = $dato_check_res["result_page"];
					
					$result_link_text = $dato_check_res["result_link_text"];
					$result_description = $dato_check_res["result_description"];
					$date = converti_data(date("D, d M Y", strtotime($dato_check_res["last_insert_date"])), "it", "month_first");
					
					$res["rows"][$r]["cell"]["id"] = $dato_check_res["id"];
						$parent_feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . $dato_check_res["parent_id"] . "'");
						if ($parent_feed->rowCount() > 0){
							while($dato_parent_feed = $parent_feed->fetch()){
								$res["rows"][$r]["cell"]["feed"] = "<a href=\"/EditoRSS/Feeds/" . $dato_parent_feed["id"] . "\" title=\"Vai al feed di riferimento\">" . $dato_parent_feed["title"] . "</a>";
							}
						} else {
							$res["rows"][$r]["cell"]["feed"] = "Feed rimosso<br />Era id " . $dato_check_res["parent_id"];
						}
					$res["rows"][$r]["cell"]["titolo"] = "<a href=\"/EditoRSS/News/" . $dato_check_res["id"] . "\" title=\"Visualizza la scheda della news\">" . stripslashes($dato_check_res["title"]) . "</a>";
					$res["rows"][$r]["cell"]["descrizione"] = "<span title=\"" . stripslashes($dato_check_res["description"]) . "\">" . taglia_stringa(stripslashes($dato_check_res["description"]), 300) . "</span>";
					$res["rows"][$r]["cell"]["uri"] = "<a href=\"" . $dato_check_res["link"] . "\" title=\"Visualizza la pagina della news\" target=\"_blank\">" . $dato_check_res["link"] . "</a>";
					$res["rows"][$r]["cell"]["data"] = $date;
					$res["rows"][$r]["cell"]["actions"] = "<a class=\"edit\" title=\"Modifica news\" href=\"./EditoRSS/News/Modifica:" . $dato_check_res["id"] .  "\"></a>&emsp;<a class=\"cancel\" title=\"Elimina news\" href=\"javascript:void(0);\" onclick=\"remove_news('" . $dato_check_res["id"] .  "');\"></a>";
				}
			} else {
				$res[] = "";
			}
			break;
		case "feed":
			$the_query = "select * from `editorss_feeds` " . $where . " order by " . $sortname . " " . $sortorder . " " . $limit;
			$stats_query = "select *, id as 'oid', tags as 'otags' from `editorss_feeds` " . $where . " order by " . $sortname . " " . $sortorder;
			$count_query = "select count(`id`) as `total` from `editorss_feeds` " . $where ;

			$pdo = db_connect("editorss");
			$check_res = $pdo->query($the_query);
			$check_stats = $pdo->prepare($stats_query);
			$check_stats->execute();
			$check_total = $pdo->prepare($count_query);
			$check_total->execute();
			if ($check_res->rowCount() > 0){
				$r = -1;
				while ($dato_check_res = $check_res->fetch()) {
					$r++;
					$res["page"] = $page;
					while ($dato_check_total = $check_total->fetch()) {
						$res["total"] = $dato_check_total["total"];
					}
					while ($dato_check_stats = $check_stats->fetch()) {
						$res["total_ids"][] = $dato_check_stats["oid"];
					}
					$res["rows"][$r]["id"] = $dato_check_res["id"];	
					if($dato_check_feed["is_active"] == 0){
						if ($c & 1){
							$is_active = "odd inactive";
						} else {
							$is_active = "inactive";
						}
						$disable_cancel = true;
						$disable_stop = "disable";
						$disable_play = "disable";
						$disable_pause = "disable";
					} else {
						if ($c & 1){
							$is_active = "odd";
						} else {
							$is_active = "";
						}
						switch ($dato_check_feed["automation_status"]){
							case "stop":	$disable_cancel = false;	$disable_stop = "disable";	$disable_play = "";			$disable_pause = "";		break;
							case "play":	$disable_cancel = false;	$disable_stop = "";			$disable_play = "disable";		$disable_pause = "";		break;
							case "pause":	$disable_cancel = false;	$disable_stop = "";			$disable_play = "";			$disable_pause = "disable";	break;
						}
					}
					
					$result_link_text = $dato_check_res["result_link_text"];
					$result_description = $dato_check_res["result_description"];
					$date = converti_data(date("D, d M Y", strtotime($dato_check_res["last_insert_date"])), "it", "month_first");
					$feed_table ="<table cellpadding=\"0\" cellspacing=\"0\" class=\"controls\"><tr class=\"odd\">";
						$feed_table .= "<td class=\"feed\" title=\"Visualizza feed\" onclick=\"goTo('" . $dato_check_res["uri"] .  "', 'true');\"></td>";
						$feed_table .= "<td class=\"edit\" title=\"Modifica feed\" onclick=\"goTo('EditoRSS/Feeds/Modifica:" . $dato_check_res["id"] .  "', 'false');\"></td>";
						if ($disable_cancel){
							$feed_table .= "<td class=\"restore\" title=\"Ripristina feed cancellato\" onclick=\"retake_feed('" . $dato_check_res["id"] .  "', '" . addslashes($dato_check_res["title"]) . "');\"></td>";
						} else {
							$feed_table .= "<td class=\"cancel\" title=\"Marca per la rimozione\" onclick=\"deleteItem('" . $dato_check_res["id"] .  "', '" . addslashes($dato_check_res["title"]) .  "');\"></td>";
						}
					$feed_table .="</tr>";
						if($dato_check_feed["is_active"] == 1){
							$action_display = "";
							$force_delete_display = "display: none;";
						} else {
							$action_display = "display: none;";
							$force_delete_display = "";
						}	
						$feed_table .= "<tr class=\"odd actions\" style=\"" . $action_display . "\">";
							$feed_table .= "<td class=\"stop " . $disable_stop . "\" title=\"Smetti l'automazione feed\" onclick=\"alterate_scan('" . $dato_check_res["id"] .  "', 'stop');\"></td>";
							$feed_table .= "<td colspan=\"2\" class=\"play " . $disable_play . "\" title=\"Riprendi l'automazione del feed\" onclick=\"alterate_scan('" . $dato_check_res["id"] .  "', 'play');\"></td>";
							$feed_table .= "<td class=\"pause " . $disable_pause . "\" title=\"Interrompi l'automazione sul feed\" onclick=\"alterate_scan('" . $dato_check_res["id"] .  "', 'pause');\"></td>";
						$feed_table .= "</tr>";
						$feed_table .= "<tr class=\"odd force_delete\" style=\"" . $force_delete_display . "\">";
							$feed_table .= "<td colspan=\"3\"class=\"remove\" title=\"Forza la rimozione del feed\" onclick=\"force_remove('" . $dato_check_res["id"] .  "', '" . addslashes($dato_check_res["title"]) . "');\"></td>";
						$feed_table .= "</tr>";
					$feed_table .= "</table>";
					
					$res["rows"][$r]["cell"]["id"] = $dato_check_res["id"];
					$res["rows"][$r]["cell"]["titolo"] = "<a href=\"/EditoRSS/Feeds/" . $dato_check_res["id"] . "\" title=\"Visualizza la scheda del feed\">" . $dato_check_res["title"] . "</a>";
					$res["rows"][$r]["cell"]["descrizione"] = "<span title=\"" . htmlentities(stripslashes(utf8_decode($dato_check_res["description"]))) . "\">" . taglia_stringa(htmlentities(stripslashes(utf8_decode($dato_check_res["description"]))), 300) . "</span>";
						if(strlen($dato_check_res["tags"]) > 0){
							$tags = "";
							$tags_arr = explode(",", $dato_check_res["tags"]);
							foreach($tags_arr as $tag){
								$tags .= "<a href=\"./Tags/" . make_url_friendly(trim($tag)) . "\" class=\"tag\" title=\"Vai alla pagina di questo tag\">" . trim($tag) . "</a> ";
							}
						} else {
							$tags = "";
						}
						if(strlen($dato_check_res["group"]) > 0){
							$groups = "";
							$groups_arr = explode(",", $dato_check_res["group"]);
							foreach($groups_arr as $group){
								$groups .= "<a href=\"./Gruppi/" . make_url_friendly(trim($group)) . "\" class=\"tag\" title=\"Vai alla pagina di questo gruppo\">" . trim($group) . "</a> ";
							}
						} else {
							$groups = "";
						}
					$res["rows"][$r]["cell"]["tags"] = $tags;
					$res["rows"][$r]["cell"]["group"] = $groups;
					$res["rows"][$r]["cell"]["uri"] = "<a href=\"" . $dato_check_res["uri"] . "\" title=\"Visualizza il feed\" target=\"_blank\">" . $dato_check_res["uri"] . "</a>";
					$res["rows"][$r]["cell"]["data"] = $date;
					$res["rows"][$r]["cell"]["actions"] = $feed_table;
				}
			} else {
				$res[] = "";
			}
			break;
	}
}
if($_GET["debug"] == "true"){
	print_r($res);
}
print json_encode($res);
?>