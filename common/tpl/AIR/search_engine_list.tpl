<?php
/**
* List all search engines
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
$check_se = $pdo->query("select * from `air_search_engines`");
if ($check_se->rowCount() > 0){
	require_once("common/include/funcs/_converti_data.php");
	require_once("common/include/funcs/_create_link.php");
	require_once("common/include/funcs/_taglia_stringa.php");
	
	while ($dato_check_se = $check_se->fetch()) {
		$the_id = $dato_check_se["id"];
		$se_title = $dato_check_se["name"];
		$se_uri = create_link($dato_check_se["search_page"]);
		$se_description_title = stripslashes(htmlentities(utf8_decode($dato_check_se["description"])));
		$se_description = stripslashes(nl2br(htmlentities(utf8_decode($dato_check_se["description"]))));
		
		// Aggiunge alla tabella i motori di ricerca globali
		if ($dato_check_se["user"] == ""){
			$se_table_tr .= "<tr id=\"tr_" . $the_id . "\"><td>" . $the_id . "</td><td id=\"se_title_" . $the_id . "\"><a href=\"/AIR/Motori_di_ricerca/" . $the_id . "\" title=\"Vedi la scheda di questo Motore di ricerca\">" . stripslashes($se_title) . "</a></td><td class=\"ellipsis\" width=\"350\" title=\"" . $se_description_title . "\">" . taglia_stringa($se_description, 150) . "</td><td width=\"250\" class=\"ellipsis\">" . $se_uri . "</td><td width=\"55\" align=\"center\"><a class=\"edit\" title=\"Modifica Motore di ricerca\" href=\"AIR/Motori_di_ricerca/Modifica:" . $the_id .  "\"></a>&emsp;<a class=\"cancel\" href=\"javascript:void(0);\" onclick=\"delete_se('" . $the_id . "');\" title=\"Elimina Motore di ricerca\"></a></td></tr>";
		} else {
			// Aggiunge alla tabella quelli personali
			if ($dato_check_se["user"] == $decrypted_user){
				$personal_se_tr .= "<tr id=\"tr_" . $the_id . "\"><td>" . $the_id . "</td><td id=\"se_title_" . $the_id . "\">" . stripslashes($se_title) . "</td><td width=\"350\" class=\"ellipsis\" title=\"" . $se_description_title . "\">" . taglia_stringa($se_description, 150) . "</td><td width=\"250\" class=\"ellipsis\">" . $se_uri . "</td><td width=\"55\" align=\"center\"><a class=\"edit\" title=\"Modifica Motore di ricerca\" href=\"AIR/Motori_di_ricerca/" . $the_id .  "\"></a>&emsp;<a class=\"cancel\" href=\"javascript:void(0);\" onclick=\"delete_se('" . $the_id . "');\" title=\"Elimina Motore di ricerca\"></a></td></tr>";
			}
		}
	}
	
	$global_se_table = "<table class=\"flexigrid\"><thead><tr><th width=\"24\">ID</th><th width=\"100\">NOME</th><th width=\"350\">DESCRIZIONE</th><th width=\"250\">URI</th><th width=\"55\">AZIONI</th></tr></thead><tbody>" . $se_table_tr . "</tbody></table>";
	// Se ci sono motori di ricerca personali ne crea la struttura
	if(strlen($personal_se_tr) > 0){
		$global_se_table = "<h1>Motori di ricerca globali</h1><br />" . $global_se_table;
		$personal_se_table = "<br /><br /><h1>Motori di ricerca personali</h1><br /><table class=\"flexigrid\"><thead><tr><th width=\"24\">ID</th><th width=\"100\">NOME</th><th width=\"350\">DESCRIZIONE</th><th width=\"250\">URI</th><th width=\"55\">AZIONI</th></tr></thead><tbody>" . $personal_se_tr . "</tbody></table>";
	}
	$content_body = <<<Feed_list
	<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
	<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>
	<script language="javascript" type="text/javascript">
	function delete_se(i){
		apprise("Sicuri di voler rimuovere il Motore di ricerca &quot;" + $("#se_title_" + i).text() + "&quot;", {"confirm": "true"}, function(r){
			if (r){
				$.get("common/include/funcs/_ajax/AIR/delete_search_engine.php", {id: i}, function(data){
					if (data == "removed"){
						apprise("Motore di ricerca rimosso con successo", {"animate": "true"});
						$("#tr_" + i).fadeOut(300, function() { $(this).remove(); });
					}
				});
			}
		});
	}
	$(document).ready(function() {
		$(".flexigrid").flexigrid({
			singleSelect: true
		});
	});
	</script>
		$global_se_table
		$personal_se_table
Feed_list;
} else {
	$content_body = <<<No_se
	<table cellspacing="10" cellpadding="10" style="width: 100%;">
		<tr>
			<td style="width: 128px">
				<img src="common/media/img/document_se_cancel_128_ccc.png" />
			</td>
			<td valign="top" style="font-size: 1.1em;">
				Non sono ancora presenti dei motori di ricerca.
			</td>
		</tr>
	</table>
No_se;
}

require_once("common/include/conf/replacing_object_data.php");
?>