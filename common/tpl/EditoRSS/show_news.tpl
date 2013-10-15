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

if (isset($_POST["edit_news"])){
	require_once("common/tpl/EditoRSS/news_form.tpl");
} else {
	if (!isset($_GET["q"])){
		$content_subtitle = "Elenco delle news relative ai feeds indicizzati";
		require_once("common/tpl/EditoRSS/news_list.tpl");
	} else {
		require_once("common/include/funcs/_create_link.php");
		require_once("common/include/funcs/_converti_data.php");
		echo("select * from `editorss_feeds_news` where `id` = '" . ($GLOBALS["page_q"] - 1) . "'");
		$prev_news = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . ($GLOBALS["page_q"] - 1) . "'");
		if ($prev_news->rowCount() > 0){
			while ($dato_prev_news = $prev_news->fetch()){
				$prev = "<a class=\"prev\" title=\"PRECEDENTE\" href=\"EditoRSS/News/" . $dato_prev_news["id"] . "\"></a>";
			}
		} else {
			$prev = "";
		}
		$next_news = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . ($GLOBALS["page_q"] + 1) . "'");
		if ($next_news->rowCount() > 0){
			while ($dato_next_news = $next_news->fetch()){
				$next = "<a class=\"next\" title=\"SUCCESSIVA\" href=\"EditoRSS/News/" . $dato_next_news["id"] . "\"></a>";
			}
		} else {
			$next = "";
		}
		$nav_btns = "<div id=\"nav_btn\">" . $prev . $next . "</div><br />";
		$news = $pdo->query("select * from `editorss_feeds_news` where `id` = '" . addslashes($GLOBALS["page_q"]) . "'");
		if ($news->rowCount() > 0){
			while ($dato_news = $news->fetch()){
				$news_id = $dato_news["id"];
				$content_title = stripslashes($dato_news["title"]);
				$content_last_edit = "<b>" . converti_data($dato_news["date"]) . "</b>";
				$the_body = "<div id=\"news_body\">" . nl2br(create_link(html_entity_decode(trim($dato_news["description"]), ENT_NOQUOTES, "UTF-8"))) . "</div>";
				$the_body = create_link(stripslashes($the_body));
				$content_body = $nav_btns . "Pagina dell'articolo: " . create_link($dato_news["link"]) . "<br /><br />" . $the_body;
			}
		}
		$content_body .= "<form method=\"post\" action=\"\"><br /><hr /><input type=\"submit\" name=\"edit_news\" value=\"Modifica news\" /></form>";
	}
}
?>
