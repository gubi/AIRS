<?php
/**
* Generates research results page
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
// Estrae i dati della pagina
if (is_numeric($card_title)){
	$page_card = $pdo->query("select * from `air_research_results` where `id` = '" . addslashes($card_title) . "'");
} else {
	$page_card = $pdo->query("select * from `air_research_results` where `result_link_text` like '" . addslashes($card_title) . "%'");
}
if ($page_card->rowCount() > 0){
	while($dato_page_card = $page_card->fetch()){
		$research = $pdo->query("select * from `air_research` where id = '" . addslashes($dato_page_card["research_id"]) . "'");
		if ($research->rowCount() > 0){
			while($dato_research = $research->fetch()){
				$card_title = $dato_research["title"];
				$research_date = converti_data(date("D, d M Y \a\l\l\e H.i:s", strtotime($dato_page_card["date"])), "it", "month_first");
				$research_title = "<a href=\"/AIR/Ricerche/" . $dato_research["id"] . "\" title=\"Vai alla scheda della ricerca\">" . $dato_research["title"] . "</a>";
				$research_description = stripslashes($dato_research["description"]);
				$research_tag = $dato_research["tags"];
				$research_page = $dato_page_card["result_page"];
				$research_no = $dato_page_card["result_id"];
					$research_link_description = stripslashes($dato_page_card["result_description"]);
					$research_link = "<a href=\"" . $dato_page_card["search_uri"] . "\" target=\"_blank\" title=\"Vai alla pagina del risultato del Motore di Ricerca\">" . $dato_page_card["search_uri"] . "</a>";
					$research_result_link = "<a href=\"" . $dato_page_card["result_uri"] . "\" target=\"_blank\" title=\"Vai alla pagina del risultato\">" . stripslashes($dato_page_card["result_link_text"]) . "</a>";
					
					$research_content = mb_convert_encoding(nl2br(stripslashes(trim($dato_page_card["result_content"]))), "HTML-ENTITIES", mb_detect_encoding($dato_page_card["result_content"]));
					$research_entire_html_content_src = $dato_page_card["id"];
			}
		}
	}
	$tags_arr = explode(",", $research_tag);
	foreach($tags_arr as $tag){
		$tags[] = "<a class=\"tag\" href=\"/Tags/" . trim($tag) . "\" title=\"Vai alla pagina di questo tag\">" . trim($tag) . "</a>";
	}
	$research_tags = utf8_decode(implode("&nbsp;", $tags));
	$content_body = <<<Table
	<script type="text/javascript">
	function SelectText(element) {
		var doc = document;
		var text = doc.getElementById(element);    
		if (doc.body.createTextRange) {
			var range = doc.body.createTextRange();
			range.moveToElementText(text);
			range.select();
		} else if (window.getSelection) {
			var selection = window.getSelection();
			var range = doc.createRange();
			range.selectNodeContents(text);
			selection.removeAllRanges();
			selection.addRange(range);
		}
	}
	</script>
	<fieldset>
		<legend class="info">Dati della ricerca</legend>
		<table class="card" cellspacing="5" cellpadding="2">
			<tr>
				<th valign="top">Data di scansione:</th>
				<td>$research_date</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th valign="top">Descrizione della ricerca:</th>
				<td>$research_description</td>
			</tr>
			<tr>
				<th valign="top">Ricerca di afferenza:</th>
				<td>$research_title</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th>Coordinate</th><td>$research_no risultato di pagina $research_page</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th valign="top">Collegamento alla pagina dei risultati:</th>
				<td>$research_link</td>
			</tr>
		</table>
		<br />
		<fieldset>
			<legend class="label">Tag</legend>
			<table cellspacing="2" cellpadding="2" class="card">
				<tr>
					<td>
						$research_tags
					</td>
				</tr>
			</table>
		</fieldset>
	</fieldset>
	<br />
	<fieldset>
		<legend class="search">Risultato della ricerca</legend>
		<table class="card" cellspacing="2" cellpadding="2">
			<tr>
				<th valign="top">Collegamento al risultato:</th>
				<td>$research_result_link<br /><div style="color: #999; width: 75%;">$research_link_description</div></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<br />
					<fieldset>
						<legend class="form">Contenuto scansionato</legend>
						<p style="font-style: italic;">
							Questo contenuto &egrave; stato estrapolato grazie al browser interno <a href="http://it.wikipedia.org/wiki/Lynx_%28software%29" target="_blank">Lynx</a>.<br />
							Idealmente, questo &egrave; ci&ograve; che "vedono" i motori di ricerca quando scansionano una pagina web.
						</p>
						<div id="scanned_content" onDoubleClick="SelectText('scanned_content');" style="padding: 10px; font-family: monospace; width: 96%; height: 300px; border: #bbb 1px solid; box-shadow: 0 0 6px #666 inset; overflow-y: auto;">
							$research_content
						</div>
						<br />
					</fieldset>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<br />
					<fieldset>
						<legend class="source">Pagina del contenuto acquisita</legend>
						<br />
						<i>
							Questa &egrave; la copia della pagina archiviata dal Sistema.<br />
							<b>DON'T PANIC</b>: per lasciare spazio nella memoria, le immagini non sono state salvate <i>volutamente</i>. Tuttavia ne &egrave; stato impostato il puntamento al loro dominio di riferimento.<br />
							&Egrave; comunque possibile che non vengano caricate se sono state rimosse dal Server di afferenza.
						</i>
						<br />
						<br />
						<iframe src="common/include/funcs/_ajax/AIR/show_html_content.php?id=$research_entire_html_content_src" style="width: 100%; height: 450px; border: #bbb 1px solid; box-shadow: 0 0 6px #666 inset;"></iframe>
					</fieldset>
				</td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<br />
					<fieldset>
						<legend class="search">Cache del browser</legend>
						<table class="card" cellspacing="2" cellpadding="2">
							<tr>
								<th valign="top">Collegamento alla copia cache:</th>
								<td>$research_result_link</td>
							</tr>
							<tr>
								<td colspan="2">
									<br />
									<i>
										Questa &egrave; la copia della copia cache archiviata dal Sistema.<br />
										<b>DON'T PANIC</b>: per lasciare spazio nella memoria, le immagini non sono state salvate <i>volutamente</i>. Tuttavia ne &egrave; stato impostato il puntamento al loro dominio di riferimento.<br />
										&Egrave; comunque possibile che non vengano caricate se sono state rimosse dal Server di afferenza.<br />
										<br />
										<b>Questo contenuto potrebbe risultare in errore.</b><br />
										In tal caso seguire il collegamento su indicato.
									</i>
									<br />
									<br />
									<iframe src="common/include/funcs/_ajax/AIR/show_html_content.php?cache=true&id=$research_entire_html_content_src" style="width: 100%; height: 450px; border: #bbb 1px solid; box-shadow: 0 0 6px #666 inset;"></iframe></td>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</fieldset>
Table;
	$content_title = "<a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "\">Risultato della Ricerca</a>: " . $card_title;
	$content_subtitle = "Visualizzazione dei dati al dettaglio per la ricerca automatizzata";
} else {
	require_once("common/tpl/AIR/_no_results.tpl");
}
?>
