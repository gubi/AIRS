<?php
/**
* Generates news page
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

require_once("common/include/funcs/_converti_data.php");
$pdo = db_connect("editorss");

$page_title = str_replace("_", " ", $GLOBALS["page_q"]);
if (is_numeric($GLOBALS["page_q"])){
	$news_page = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . addslashes($page_title) . "' and `user` = '" . addslashes($decrypted_user) . "'");
} else {
	$news_page = $pdo->query("select * from `editorss_feeds_news` where `id` like '" . addslashes($page_title) . "%' and `user` = '" . addslashes($decrypted_user) . "'");
}
if ($news_page->rowCount() > 0){
	while ($dato_news_page = $news_page->fetch()){
		$id = $dato_news_page["id"];
		$date = converti_data(date("D, d M Y", strtotime($dato_news_page["date"])), "it", "month_first");
		$title = $dato_news_page["title"];
		$page_title = $title;
		$description = $dato_news_page["description"];
		$link = $dato_news_page["link"];
		
			$parent_feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . $dato_news_page["parent_id"] . "'");
			if ($parent_feed->rowCount() > 0){
				while($dato_parent_feed = $parent_feed->fetch()){
					$feed_id = $dato_parent_feed["id"];
					$feed_title = $dato_parent_feed["title"];
				}
			}
		$last_insert_date = converti_data(date("D, d M Y", strtotime($dato_news_page["last_insert_date"])), "it", "month_first");
		
		$tag = $dato_news_page["tags"];
		
		$automation_status = "in " . $dato_news_page["automation_status"];
		if ($dato_news_page["is_active"] == "1"){
			$is_active = "<span style=\"color: #3d8f23;\"><b>Attiva</b></span>";
		} else {
			$is_active = "<span style=\"color: #cc5e5e;\"><b>NON attiva</b></span>";
		}
	}
	$content_body = <<<Table
	<fieldset>
		<legend class="edit">News <acronym oldtitle="Really Simple Syndication">RSS</acronym></legend>
		<table class="card" cellspacing="2" cellpadding="2">
			<tr>
				<th>Indirizzo:</th>
				<td><a href="$link" target="_blank" title="Vai alla pagina della news">$link</a></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th valign="top">Data:</th>
				<td>$date</td>
			</tr>
			<tr>
				<th>Titolo:</th>
				<td>$title</td>
			</tr>
			<tr>
				<th valign="top">Descrizione:</th>
				<td>$description</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th>Feed di afferenza:</th>
				<td><a href="EditoRSS/Feeds/$feed_id" title="Visualizza il feed di riferimento">$feed_title</a></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th valign="top">Data di inserimento:</th>
				<td>$last_insert_date</td>
			</tr>
		</table>
		<br />
		<fieldset>
			<legend class="label">Tag</legend>
			<table cellspacing="2" cellpadding="2" class="card">
				<tr>
					<td>
						$tags
					</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<br />
	</fieldset>
Table;
	$content_title = "<a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "\">News</a>: " . $page_title;
	$content_subtitle = "Visualizzazione dei dati del feed";
} else {
	require_once("common/tpl/AIR/_no_results.tpl");
}
?>