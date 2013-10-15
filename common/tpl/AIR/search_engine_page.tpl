<?php
/**
* Generates search engine page
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

$card_title = str_replace("_", " ", $GLOBALS["page_q"]);
// Connette ai database
$pdo = db_connect("air");
$pdoa = db_connect(".airs");
// Estrae i dati della pagina
if(is_numeric($card_title)){
	$page_card = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($card_title) . "'");
} else {
	$page_card = $pdo->query("select * from `air_search_engines` where `title` like '" . addslashes($card_title) . "%'");
}
if ($page_card->rowCount() > 0){
	while($dato_page_card = $page_card->fetch()){
		$se_name = $dato_page_card["name"];
		$se_description = utf8_decode($dato_page_card["description"]);
		$se_uri = $dato_page_card["search_page"] . "?" . $dato_page_card["search_var"];
		$se_lang_var = $dato_page_card["language_var"];
		$se_site_var = $dato_page_card["site_var"];
		$se_filetype_var = $dato_page_card["filetype_var"];
		$se_lastdate_var = $dato_page_card["last_date_var"];
		$se_lastdate_val = $dato_page_card["last_date_val"];
		$se_country_var = $dato_page_card["country_var"];
		$se_quoting_var = stripslashes(str_replace("{1}", "<i>testo</i>", $dato_page_card["quoting_var"]));
		$se_not_var = str_replace("{1}", "<i>testo</i>", $dato_page_card["exclusion_var"]);
		$se_or_var = str_replace("{1}", "<i>testo</i>", $dato_page_card["or_var"]);
	}
	$next_se = $pdo->query("select `id` from `air_search_engines` where `id` = '" . ((int)$GLOBALS["page_q"] + 1) . "'");
	if ($next_se->rowCount() > 0){
		$next_btn = "<h2><a style=\"float: right;\" href=\"/AIR/Motori_di_ricerca/" . ((int)$GLOBALS["page_q"] + 1) . "\" title=\"Vai al Motore successivo\">Successivo &rsaquo;</a></h2>";
	} else {
		$next_btn = "";
	}
	$prev_se = $pdo->query("select `id` from `air_search_engines` where `id` = '" . ((int)$GLOBALS["page_q"] - 1) . "'");
	if ($prev_se->rowCount() > 0){
		$prev_btn = "<h2><a style=\"float: left;\" href=\"/AIR/Motori_di_ricerca/" . ((int)$GLOBALS["page_q"] - 1) . "\" title=\"Vai al Motore precedente\">&lsaquo; Precedente</a></h2>";
	} else {
		$prev_btn = "";
	}
	$content_title = "<a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "\">Motore di Ricerca</a>: " . $se_name;
	$content_subtitle = "Visualizzazione dei dati del Motore di Ricerca";
	
	$content_body = <<<Table
	$prev_btn
	$next_btn
	<br />
	<br />
	<br />
	<br />
	<fieldset>
		<legend class="info">Dati del Motore di Ricerca</legend>
		<table class="card" cellspacing="2" cellpadding="2">
			<tr>
				<th valign="top">Nome:</th>
				<td>$se_name</td>
			</tr>
			<tr>
				<th valign="top">Descrizione:</th>
				<td>$se_description</td>
			</tr>
			<tr>
				<th valign="top">Indirizzo per le ricerche:</th>
				<td><a href="$se_uri" target="_blank" title="Prova una ricerca direttamente da questo collegamento">$se_uri</a></td>
			</tr>
		</table>
	</fieldset>
	<br />
	<table cellspacing="0" cellpadding="0" style="width: 100%;">
		<tr>
			<td style="width: 50%; padding-right: 5px;" valign="top">
				<fieldset>
					<legend class="query">Variabili nella query di ricerca (<acronym title="Uniform Resource Identifier">URI</acronym>)</legend>
					<table class="card" cellspacing="2" cellpadding="2">
						<tr>
							<th valign="top">Variabile per la lingua:</th>
							<td>$se_lang_var</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<th valign="top">Variabile per il filtro su un dominio:</th>
							<td>$se_site_var</td>
						</tr>
						<tr>
							<th>Variabile per il filtro su un formato di documento:</th>
							<td>$se_filetype_var</td>
						</tr>
						<tr>
							<th>Variabile per il filtro su una ricerca geografica:</th>
							<td>$se_country_var</td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<th>Variabile per il filtro temporale:</th>
							<td>$se_lastdate_var</th>
						</tr>
						<tr>
							<th>Valore per il filtro temporale:</th>
							<td>$se_lastdate_val</td>
						</tr>
					</table>
				</fieldset>
			</td>
			<td valign="top" style="padding-left: 5px;">
				<fieldset>
					<legend class="form">Variabili nel campo di ricerca</legend>
					<table class="card" cellspacing="2" cellpadding="2">
						<tr>
							<th>Variabile per il filtro su una citazione:</th>
							<td><tt>$se_quoting_var</tt></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<th>Variabile per l'esclusione di parole:</th>
							<td><tt>$se_not_var</tt></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<th>Variabile per una ricerca ristretta di parole:</th>
							<td><tt>"."<tt></td>
						</tr>
						<tr><td colspan="2">&nbsp;</td></tr>
						<tr>
							<th>Variabile per una <a href="http://it.wikipedia.org/wiki/Ricerca_operativa" target="_blank">ricerca operativa tra parole (OR)</a>:</th>
							<td><tt>$se_or_var</tt></td>
						</tr>
					</table>
				</fieldset>
			</td>
		</tr>
	</table>
	<br />
	<br />
	$prev_btn
	$next_btn
Table;
} else {
	require_once("common/tpl/AIR/_no_results.tpl");
}
?>