<?php
/**
* Generates feed details page
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
require_once("common/include/funcs/_make_url_friendly.php");
require_once("common/include/funcs/_taglia_stringa.php");
$pdo = db_connect("editorss");

$page_title = str_replace("_", " ", $GLOBALS["page_q"]);
if (is_numeric($GLOBALS["page_q"])){
	$feed_page = $pdo->query("select * from `editorss_feeds` where `id` = '" . addslashes($page_title) . "' and `user` = '" . addslashes($decrypted_user) . "'");
} else {
	$feed_page = $pdo->query("select * from `editorss_feeds` where `id` like '" . addslashes($page_title) . "%' and `user` = '" . addslashes($decrypted_user) . "'");
}
if ($feed_page->rowCount() > 0){
	while ($dato_feed_page = $feed_page->fetch()){
		$id = $dato_feed_page["id"];
		$title = $dato_feed_page["title"];
		$page_title = $title;
		$description = stripslashes(utf8_decode($dato_feed_page["description"]));
		$uri = $dato_feed_page["uri"];
			$group = $dato_feed_page["group"];
			$tag = $dato_feed_page["tags"];
		$valid_resources = $dato_feed_page["valid_resources"];
		$origin = $dato_feed_page["origin"];
		if(strlen($dato_feed_page["tags"]) > 0){
			$tags_arr = explode(",", $dato_feed_page["tags"]);
			foreach($tags_arr as $tag){
				$tags .= "<a href=\"./Tags/" . make_url_friendly(trim($tag)) . "\" class=\"tag\" title=\"Vai alla pagina di questo tag\">" . trim($tag) . "</a> ";
			}
		} else {
			$tags = "";
		}
		if(strlen($dato_feed_page["group"]) > 0){
			$groups_arr = explode(",", $dato_feed_page["group"]);
			foreach($groups_arr as $group){
				$groups .= "<a href=\"./Gruppi/" . make_url_friendly(trim($group)) . "\" class=\"tag\" title=\"Vai alla pagina di questo gruppo\">" . trim($group) . "</a> ";
			}
		} else {
			$groups = "";
		}
		
		$last_insert_date = converti_data(date("D, d M Y", strtotime($dato_feed_page["last_insert_date"])), "it", "month_first");
		$automation_status = "in " . $dato_feed_page["automation_status"];
		if ($dato_feed_page["is_active"] == "1"){
			$is_active = "<span style=\"color: #3d8f23;\"><b>Attiva</b></span>";
		} else {
			$is_active = "<span style=\"color: #cc5e5e;\"><b>NON attiva</b></span>";
		}
		
		$news = $pdo->query("select * from `editorss_feeds_news` where `parent_id` = '" . $id . "'");
		if ($news->rowCount() > 0){
			$news_list = "<ul>";
			while($dato_content_news = $news->fetch()){
				if(strlen($dato_content_news["title"])){
					$news_list .= "<li><a href=\"./EditoRSS/News/" . $dato_content_news["id"] . "\" title=\"Vai alla scheda della news: <i>" . htmlentities(stripslashes(utf8_decode($dato_content_news["title"]))) . "</i>\">" . taglia_stringa(stripslashes(utf8_decode($dato_content_news["title"])), 96) . "</a></li>";
				}
			}
			$news_list .= "</ul>";
		}
	}
	$goto_news_list = '<h3 style="padding-left: 20px;"><a href="./EditoRSS/News">&lsaquo; Vai all\'elenco dei feeds</a></h3>';
	$content_body = <<<Table
	<fieldset>
		<legend class="edit">Feed <acronym oldtitle="Really Simple Syndication">RSS</acronym></legend>
		<table class="card" cellspacing="2" cellpadding="2">
			<tr>
				<th>Titolo del feed:</th>
				<td>$title</td>
			</tr>
			<tr>
				<th valign="top">Descrizione:</th>
				<td>$description</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th>Indirizzo:</th>
				<td><a href="$uri" target="_blank" title="Visualizza il feed">$uri</a></td>
			</tr>
			<tr><td>&nbsp;</td></tr>
			<tr>
				<th valign="top">Risorse valide:</th>
				<td>$valid_resources</td>
			</tr>
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
		<fieldset>
			<legend class="groups">Gruppi di feed</legend>
			<table cellspacing="2" cellpadding="2" class="card">
				<tr>
					<td>
						$groups
					</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<fieldset>
			<legend class="clock">Automazione</legend>
			<table cellspacing="2" cellpadding="2">
				<tr>
					<th>Stato:</th>
					<td>$is_active &rsaquo; $automation_status</td>
				</tr>
			</table>
		</fieldset>
		<br />
		<br />
		<fieldset>
			<legend class="origin">News correlate</legend>
			<table cellspacing="2" cellpadding="2">
				<tr>
					<td>
						$goto_news_list
						$news_list
					</td>
				</tr>
			</table>
		<br />
	</fieldset>
	<br />
Table;
	$content_title = "<a href=\"" . $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"] . "\">Feed</a>: " . $page_title;
	$content_subtitle = "Visualizzazione dei dati del feed";
} else {
	require_once("common/tpl/AIR/_no_results.tpl");
}
?>