<?php
/**
* Generates research result page
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

require_once("common/include/funcs/_converti_data.php");

$card_title = str_replace("_", " ", $GLOBALS["page_q"]);
// Connette ai database
$pdo = db_connect("air");
$pdoa = db_connect(".airs");
// Estrae i dati della pagina
if(is_numeric($card_title)){
	$page_card = $pdo->query("select * from `air_research` where `id` = '" . addslashes($card_title) . "'");
} else {
	$page_card = $pdo->query("select * from `air_research` where `title` like '" . addslashes($card_title) . "%'");
}
if ($page_card->rowCount() > 0){
	while($dato_page_card = $page_card->fetch()){
		$id = $dato_page_card["id"];
		$title = $dato_page_card["title"];
		$card_title = $title;
		$description = $dato_page_card["description"];
		$search_terms = $dato_page_card["query"];
			// Suddivide i tag
			$tag = explode(",", $dato_page_card["tags"]);
			foreach($tag as $the_tag){
				$tags_arr[] = "<a href=\"./Tags/" . ucfirst(trim($the_tag)) . "\" class=\"tag\" title=\"Vai alla pagina di questo tag\">" . trim($the_tag) . "</a>";
			}
			$unique_tags = array_unique($tags_arr);
			$tags = join("&nbsp;", $unique_tags);
			
			// Suddivide i Motori di ricerca impostati
			$res_se_ids = explode(",", $dato_page_card["search_engines"]);
			foreach($res_se_ids as $se_id){
				$se = $pdo->query("select `id`, `name`,`search_page` from `air_search_engines` where id = '" . trim($se_id) . "'");
				if ($se->rowCount() > 0){
					while($dato_se = $se->fetch()){
						$search_engines_arr[] = "<a href=\"AIR/Motori_di_ricerca/" . $dato_se["id"] . "\" class=\"tag\" title=\"Vai alla scheda di questo motore di ricerca\">" . $dato_se["name"] . "</a>";
					}
				}
			}
			$unique_search_engines = array_unique($search_engines_arr);
			$search_engines_txt = join("&nbsp;", $unique_search_engines);
			
			// Suddivide le lingue impostate
			$languages_arr = explode(",", $dato_page_card["languages"]);
			foreach($languages_arr as $lang){
				$query_se_languages = $pdoa->query("select * from `ISO_639-3` where `Part1` = '" . $lang . "'");
				if ($query_se_languages->rowCount() > 0){
					while($dato_se_lang = $query_se_languages->fetch()){
						$lang_arr[] = "<span class=\"tag\">" . ucwords(strtolower($dato_se_lang["Ref_Name_it"])) . "</span>";
					}
				}
			}
			if (is_array($lang_arr)){
				$lang_arr_unique = array_unique($lang_arr);
				$languages = join("&nbsp;", $lang_arr_unique);
			}
			
			// Suddivide le località geografiche impostate
			if (strlen($dato_page_card["filter_region"]) > 0){
				$region_arr = explode(",", $dato_page_card["filter_region"]);
				foreach($region_arr as $region){
					$query_se_country = $pdoa->query("select * from `ISO_3166-1` where `Country_codes` = '" . trim($region) . "'");
					if ($query_se_country->rowCount() > 0){
						while($dato_se_country = $query_se_country->fetch()){
							$country_arr[] = "<span class=\"tag\">" . ucwords(strtolower($dato_se_lang["Ref_Name_it"])) . "</span>";
						}
					}
				}
				$country_arr_unique = array_unique($country_arr);
				$filter_region = join("&nbsp;", $country_arr_unique);
			}
		
		// Se il valore è vuoto allora mostra un messaggio
		if (strlen($languages) == 0){
			$languages = "<i>Nessun filtro sulla lingua</i>";
		}
		$filter_domain = $dato_page_card["filter_domain"];
		if (strlen($filter_domain) == 0){
			$filter_domain = "<i>Nessun filtro su domino</i>";
		}
		$filter_filetype = $dato_page_card["filter_filetype"];
		if (strlen($filter_filetype) == 0){
			$filter_filetype = "<i>Nessun filtro su tipo di file</i>";
		}
		if (strlen($filter_region) == 0){
			$filter_region = "<i>Nessun filtro su località geografica</i>";
		}
		$filter_date = converti_data(date("D, d M Y", strtotime($dato_page_card["filter_date"])), "it", "month_first", "short");
		if (strlen($filter_date) == 0){
			$filter_date = "<i>Nessun filtro di data</i>";
		}
		$last_insert_date = converti_data(date("\<\b\>l d F Y\<\/\b\> \a\l\l\e \<\b\>H:i:s\<\/\b\>", strtotime($dato_page_card["last_insert_date"])), "it", "month_first", "");
		
		$results_page_link = "<h2><a style=\"float: right;\" href=\"/AIR/Risultati_delle_ricerche/" . $id . "/\" title=\"Vai alla pagina con l'elenco dei risultati\">Vedi i risultati delle ricerche &rsaquo;</a></h2>";
		
		// Automazioni
		$research_uris_arr = explode(",", $dato_page_card["research_uris"]);
		foreach($research_uris_arr as $result_uri){
			$result_automation = $pdoa->query("select * from `airs_automation` where `action` = '" . addslashes($result_uri) . "' limit 1");
			if ($result_automation->rowCount() > 0){
				$is_automated = true;
				while($dato_result_automation = $result_automation->fetch()){
					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
					// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
					// 																													IMPORTANTE
					// Effettuare ricerca interna alla tabella `airs_automation_frequency` per estrapolare i valori testuali della frequenza di ricerca
					$query_frequency_txt = $pdoa->query("select * from `airs_automation_frequency` where `type` = '" . addslashes($dato_result_automation["frequency"]) . "'");
					if ($query_frequency_txt->rowCount() > 0){
						while($dato_frequency_txt = $query_frequency_txt->fetch()){
							$result_automation_frequency = $dato_frequency_txt["frequency_txt"];
						}
					}
					
					$result_automation_start_date = date("d/m/Y", strtotime($dato_result_automation["start_date"]));
					$result_automation_start_time = $dato_result_automation["start_time"];
					$result_automation_insert_date = date("d/m/Y \a\l\l\e H:i:s", strtotime($dato_result_automation["date"]));
					$result_automation_runs = $dato_result_automation["runs"];
				}
			} else {
				$is_automated = false;
			}
		}
		if ($is_automated){
			$result_automation_fieldset = <<<Result_automation_fieldset
				<br />
				<fieldset>
					<legend class="clock">Automazione</legend>
					<table cellpadding="2" cellspacing="2" class="card">
						<tr>
							<th>Frequenza di ricerca:</th>
							<td>$result_automation_frequency</td>
						</tr>
						<tr>
							<th>Data e ora di inizio:</th>
							<td>$result_automation_start_date alle $result_automation_start_time</td>
						</tr>
						<tr>
							<th>Ultima esecuzione:</th>
							<td>$result_automation_insert_date</td>
						</tr>
						<tr>
							<th>Esecuzioni totali:</th>
							<td>$result_automation_runs</td>
						</tr>
					</table>
				</fieldset>
Result_automation_fieldset;
		} else {
			$result_automation_fieldset = "";
		}
		
	}
	$content_body = <<<Page_card
	$results_page_link
	<br />
	<br />
	<fieldset>
		<legend class="info">Dati identificativi della ricerca</legend>
		<table cellpadding="2" cellspacing="2" class="card">
			<tr>
				<th>Titolo:</th>
				<td>$title</td>
			</tr>
			<tr>
				<th valign="top">Descrizione:</th>
				<td>$description</td>
			</tr>
			<tr>
				<th valign="top">Data di inserimento:</th>
				<td>$last_insert_date</td>
			</tr>
		</table>
		<br />
		<fieldset>
			<legend class="label">Tag</legend>
			<table cellpadding="2" cellspacing="2" class="card">
				<tr>
					<td>$tags</td>
				</tr>
			</table>
		</fieldset>
	</fieldset>
	<br />
	<fieldset>
		<legend class="search">Ricerca</legend>
		<table cellpadding="2" cellspacing="2" class="card">
			<tr>
				<th>Termini di ricerca:</th>
				<td>$search_terms</td>
			</tr>
			<tr>
				<th valign="top">Motori di ricerca:</th>
				<td>$search_engines_txt</td>
			</tr>
		</table>
		<br />
		<fieldset>
			<legend class="query">Ricerca selettiva</legend>
			<table cellpadding="2" cellspacing="2" class="card">
				<tr>
					<th valign="top">Lingua:</th>
					<td>$languages</td>
				</tr>
				<tr>
					<th valign="top">Filtro su un dominio:</th>
					<td>$filter_domain</td>
				</tr>
				<tr>
					<th valign="top">Filtro su un formato di documento:</th>
					<td>$filter_filetype</td>
				</tr>
				<tr>
					<th valign="top">Filtro su località geografica:</th>
					<td>$filter_region</td>
				</tr>
				<tr>
					<th valign="top">Filtro temporale:</th>
					<td>$filter_date</td>
				</tr>
			</table>
		</fieldset>
	</fieldset>
	$result_automation_fieldset
	<br />
	<br />
	$results_page_link
Page_card;
	$content_title = "<a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "\">Ricerca</a>: " . $card_title;
	$content_subtitle = "Visualizzazione dei parametri impostati per la ricerca automatizzata";
	require_once("common/include/conf/replacing_object_data.php");
} else {
	require_once("common/tpl/__404.tpl");
}
?>