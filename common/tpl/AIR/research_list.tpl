<?php
/**
* List all research
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
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$pdo = db_connect("air");
$check_res = $pdo->query("select * from `air_research`");
if ($check_res->rowCount() > 0){
	require_once("common/include/funcs/_converti_data.php");
	require_once("common/include/funcs/_create_link.php");
	require_once("common/include/funcs/_taglia_stringa.php");
	
	while ($dato_check_res = $check_res->fetch()) {
		$tag_ = "";
		$search_engines = array();
		$filter_count = 0;
		$the_id = $dato_check_res["id"];
		$res_title = $dato_check_res["title"];
		$res_description = stripslashes(nl2br(htmlentities(utf8_decode($dato_check_res["description"]))));
			$res_se_ids = explode(",", $dato_check_res["search_engines"]);
			foreach($res_se_ids as $se_id){
				$se = $pdo->query("select `name` from `air_search_engines` where id = '" . $se_id . "'");
				if ($se->rowCount() > 0){
					while($dato_se = $se->fetch()){
						$search_engines[] = "<a href=\"" . $dato_se["search_page"] . "\" target=\"_blank\" title=\"Vai alla Pagina principale di questo motore di ricerca\">" . $dato_se["name"] . "</a>";
					}
				}
			}
			$unique_search_engines = array_unique($search_engines);
			$res_search_engines = join(", ", $unique_search_engines);
		$res_query = $dato_check_res["query"];
		if (strlen($dato_check_res["languages"]) > 0){
			$filter_count += 1;
		}
		if (strlen($dato_check_res["filter_domain"]) > 0){
			$filter_count += 1;
		}
		if (strlen($dato_check_res["filter_filetype"]) > 0){
			$filter_count += 1;
		}
		if (strlen($dato_check_res["filter_region"]) > 0){
			$filter_count += 1;
		}
		if (strlen($dato_check_res["filter_date"]) > 0){
			$filter_count += 1;
		}
		$res_insert_date = $dato_check_res["last_insert_date"];
		if (strlen($dato_check_res["tags"]) > 0){
			$res_tags = explode(",", $dato_check_res["tags"]);
			$t = 0;
			foreach($res_tags as $tag){
				$t++;
				if($t < 6){
					$tag_ .= "<span class=\"tag\">" . trim(stripslashes($tag)) . "</span>  ";
				}
			}
			if(count($res_tags) > 5){
				$tag_ .= "<span class=\"tag\">...</span>  ";
			}
		} else {
			$tag_ = "<center>-</center>";
		}
		$res_table_tr .= "<tr id=\"tr_" . $the_id . "\"><td>" . $the_id . "</td><td id=\"res_title_" . $the_id . "\" class=\"ellipsis\"><a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "/" . $the_id . "\">" . stripslashes($res_title) . "</a></td><td width=\"180\" title=\"" . $res_description . "\" class=\"ellipsis\">" . taglia_stringa($res_description, 150) . "</td><td width=\"100\">" . $tag_ . "</td><td width=\"140\">" . $res_search_engines . "</td><td width=\"180\">" . $res_query . "</td><td width=\"35\">" . $filter_count . "</td><td width=\"75\">" . $res_insert_date . "</td><td width=\"55\" align=\"center\"><a class=\"edit\" title=\"Modifica ricerca\" href=\"AIR/Ricerche/Modifica:" . $the_id .  "\"></a>&emsp;<a class=\"cancel\" href=\"javascript:void(0);\" onclick=\"delete_research('" . $the_id . "');\" title=\"Elimina ricerca\"></a>&emsp;<a class=\"start\" href=\"javascript:void(0);\" onclick=\"rescan_research('" . $the_id . "');\" title=\"Riavvia la ricerca\"></a></td></tr>";
	}
	$res_table = <<<Table
	<table class="fexigrid">
		<thead>
			<tr>
				<th width="24">ID</th>
				<th width="180">TITOLO</th>
				<th width="180">DESCRIZIONE</th>
				<th width="100">TAGS</th>
				<th width="140">MOTORI DI RICERCA</th>
				<th width="100">QUERY DI RICERCA</th>
				<th width="35">FILTRI</th>
				<th width="75">DATA</th>
				<th width="72"></th>
			</tr>
		</thead>
		<tbody>$res_table_tr</tbody>
	</table>
Table;
	
	$content_body = <<<Research_list
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function rescan_research(the_id){
		loader("Avvio della ricerca automatizzata", "show");
		$.get("common/include/funcs/_cron/automation.php", {force_run: "true", id: the_id}, function (data){
			if (data){
				var response = data.split(" :: ");
				if (response[0] == "STARTED"){
					location.replace("https://airs.inran.it/AIR/Risultati_delle_ricerche");
				}
			}
		});
	}
	function delete_research(i){
		apprise("Sicuri di voler rimuovere la ricerca &quot;" + $("#res_title_" + i).text() + "&quot;", {"confirm": "true"}, function(r){
			if (r){
				$.get("common/include/funcs/_ajax/AIR/delete_research.php", {id: i}, function(data){
					if (data == "removed"){
						$("#tr_" + i).fadeOut(300, function() { $(this).remove(); });
					}
				});
			}
		});
	}
	$(document).ready(function() {
		$(".fexigrid").flexigrid({
			singleSelect: true
		});
	});
	</script>
		$res_table
Research_list;
} else {
	$content_body = <<<No_se
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/document_se_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				Non sono ci sono ancora ricerche automatizzate.
			</td>
		</tr>
	</table>
No_se;
}

require_once("common/include/conf/replacing_object_data.php");
?>