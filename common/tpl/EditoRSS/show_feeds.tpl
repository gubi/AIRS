<?php
/**
* Generates feed page
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

//echo("edit_feed: " . $_POST["edit_feed"]);
//echo("q:" . $_GET["q"]);
if (isset($_POST["edit_feed"])){
	require_once("common/tpl/EditoRSS/news_form.tpl");
} else {
	if (!isset($_GET["q"])){
		$content_subtitle = "Elenco dei feeds disponibili per la modifica";
		
		$check_type = "feed";
		require_once("common/tpl/EditoRSS/list.tpl");
	} else {
		require_once("common/include/funcs/_create_link.php");
		require_once("common/include/funcs/_converti_data.php");
		
		$prev_feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . ($GLOBALS["page_q"] - 1) . "'");
		if ($prev_feed->rowCount() > 0){
			while ($dato_prev_feed = $prev_feed->fetch()){
				$prev = "<a class=\"prev\" title=\"PRECEDENTE\" href=\"./EditoRSS/Feeds/" . $dato_prev_feed["id"] . "\"></a>";
			}
		} else {
			$prev = "";
		}
		$next_feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . ($GLOBALS["page_q"] + 1) . "'");
		if ($next_feed->rowCount() > 0){
			while ($dato_next_feed = $next_feed->fetch()){
				$next = "<a class=\"next\" style=\"padding-right: 26px;\" title=\"SUCCESSIVO\" href=\"./EditoRSS/Feeds/" . $dato_next_feed["id"] . "\"></a>";
			}
		} else {
			$next = "";
		}
		$nav_btns = "<div id=\"nav_btn\">" . $prev . $next . "</div><br />";
		$news = $pdo->query("select * from `editorss_feeds` where `id` = '" . addslashes($GLOBALS["page_q"]) . "'");
		if ($news->rowCount() > 0){
			while ($dato_feed = $news->fetch()){
				$news_id = $dato_feed["id"];
				$content_title = stripslashes($dato_feed["title"]);
				$date = explode(" ", $dato_feed["last_insert_date"]);
				$content_last_edit = "<b>" . converti_data(date("<b>D, d M Y</b>", strtotime($dato_feed["last_insert_date"]))) . "</b>";
				$the_body = "<div id=\"news_body\">" . nl2br(create_link(html_entity_decode(trim($dato_feed["description"]), ENT_NOQUOTES, "UTF-8"))) . "</div>";
				$the_body = create_link(stripslashes($the_body));
				$content_body = $nav_btns . "URI del feed: " . create_link($dato_feed["uri"]) . "<br /><br />" . $the_body;
			}
		}
		$content_body .= <<<Show_feed
		<form method="post" action="">
			<br />
			<hr />
			<input type="submit" name="edit_feed" value="Modifica news" />
		</form>
Show_feed;
	}
}
?>
