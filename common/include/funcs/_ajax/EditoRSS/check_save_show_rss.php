<?php
/**
* Common EditoRSS functions
*
* This script check and save feeds in EditoRSS database
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
header("Content-type: text/plain; charset=utf-8");
require_once("../../calculate_tags.php");
require_once("../../../.mysql_connect.inc.php");
require_once("../../../browser.php");
require_once("../../get_decrypted_user.php");

if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	require_once("../../../lib/php-readability-master/Readability.php");
	if (isset($_GET["type"]) && trim($_GET["type"]) !== ""){
		$show = $_GET["type"];
	}
	
	$absolute_path = "http://" . $_SERVER["HTTP_HOST"] . "/";
	function is_all_text($text, $uri){
		$html = @file_get_contents($uri);
		
		if (function_exists('tidy_parse_string')) {
			$tidy = tidy_parse_string($html, array('indent'=>true), 'UTF8');
			$tidy->cleanRepair();
			$html = $tidy->value;
		}
		$readability = new Readability($html, $url);
		$readability->debug = false;
		$readability->convertLinksToFootnotes = false;
		$result = $readability->init();
		if ($result) {
			$content = $readability->getContent()->innerHTML;
			// if we've got Tidy, let's clean it up for output
			if (function_exists('tidy_parse_string')) {
				$tidy = tidy_parse_string($content, array('indent'=>true, 'show-body-only' => true), 'UTF8');
				$tidy->cleanRepair();
				$content = $tidy->value;
			}
		}
		if (strlen($text) < strlen(strip_tags($content))){
			return "<p style=\"margin-top: 5px;\"><u>Nota:</u> spesso nei feed RSS non &egrave; presente il testo completo di un articolo.<br /><b>&Egrave; possibile estendere tale funzionalit&agrave; abilitando un'acquisizione automatica del testo nella pagina di questo articolo.</b></p><br />";
		}
	}
	$the_url = (substr(urldecode($_GET["uri"]), 0, 4) == "www.") ? "http://" . urldecode($_GET["uri"]) : urldecode($_GET["uri"]);
	$scanned_url = browse($the_url, $GLOBALS["decrypted_user"], "mixed");
	if ($scanned_url[0]["http_code"] == 200 || $scanned_url[0]["http_code"] == 0){
		$arrDesc = array();
		$arrFeeds = array();
		$itemRSS = array();
		
		require_once "XML/RSS.php";
		$rss =& new XML_RSS($the_url);
		if ($rss->parse()) {
			$rss_data = array("channel" => $rss->getChannelInfo(), "items" => $rss->getItems());
			
			foreach ($rss_data["items"] as $item) {
				$itemRSS = array ( 
					'title' => $item["title"],
					'desc' => preg_replace("#\s+#s", " ", preg_replace("#(\t+)#s", "", trim($item["description"]))),
					'link' => $item["link"],
					'date' => $item["pubDate"]
				);
				array_push($arrFeeds, $itemRSS);
			}
			$categories = $rss_data["channel"]["category"];
			if(is_array($categories)){
				$categorie = array_unique($categories);
			} else {
				$categorie = $categories;
			}
			$description = array(
				"title" => trim(ucfirst($rss_data["channel"]["title"])),
				"description" => trim(ucfirst($rss_data["channel"]["description"])),
				"link" => trim($rss_data["channel"]["link"]),
				"language" => $rss_data["channel"]["language"],
				"lastBuildDate" => $rss_data["channel"]["lastBuildDate"],
				"generator" => trim($rss_data["channel"]["generator"]),
				"category" => $categorie,
				"valid_resources" => count($arrFeeds),
				"tags" => calculate_tags(strip_tags(utf8_decode(trim($rss_data["channel"]["description"]))))
			);
			array_push($arrDesc, $description);
		} else {
			$arrDesc = array("response" => "invalid feed");
		}
	} else {
		$arrDesc = array("response" => "invalid uri");
	}
	switch($show){
		case "save":
			$pdo = db_connect("editorss");
			require_once("../../_parse_content.php");
			require_once("../../_converti_data.php");
			
			if (is_array($arrFeeds)){
				foreach ($arrFeeds as $k => $v){
					$check_feed = $pdo->query("select * from `editorss_feeds_news` where `link` = '" . addslashes($v["link"]) . "' and `user` = '" . addslashes($_GET["user"]) . "'");
					if ($check_feed->rowCount() == 0){
						$doc = new DOMDocument();
						@$doc->loadHTMLFile(trim($v["link"]));
						$doc->preserveWhiteSpace = false;
						$xpath = new DOMXPath($doc);
						$entries = $xpath->query("//div[text()]");
						foreach ($entries as $entry) {
							$txt = str_replace(array("\t", "  "), array("", " "), trim($entry->nodeValue));
							similar_text($txt, $v["desc"], $sim);
							if ($sim > 50){
								$readable = 1;
								$text_arr[round($sim)] = array($entry->previousSibling->previousSibling->nodeName => $entry->nodeValue);
							} else {
								$readable = 0;
							}
						}
						if (is_array($text_arr)){
							sort($text_arr);
							$div = $text_arr[0];
							unset($text_arr);
						} else {
							unset($text_arr);
							$entries = $xpath->query("//p[text()]");
							foreach ($entries as $entry) {
								$div[] = $entry->nodeValue;
							}
						}
						array_unique($div);
						$text = implode("\n", $div);
						unset($div);
						$prev_d = explode(" ", $text);
						$v_desc = trim(str_replace(array("\t", "  "), array("", " "), trim($text)));
						
						// Acquisisce l'id del feed
						$check_parent_id = $pdo->query("select * from `editorss_feeds` where `uri` = '" . addslashes($_GET["uri"]) . "' and `user` = '" . addslashes($_GET["user"]) . "'");
						if ($check_parent_id->rowCount() !== 0){
							while ($dato_parent_id = $check_parent_id->fetch()){
								$parent_id = $dato_parent_id["id"];
							}
						}
						$add_feed = $pdo->prepare("insert into `editorss_feeds_news` (`title`, `description`, `date`, `link`, `parent_id`, `tags`, `user`) values(?, ?, ?, ?, ?, ?, ?)");
						$add_feed->bindParam(1, addslashes($v["title"]));
						$add_feed->bindParam(2, addslashes(trim($v_desc)));
						$add_feed->bindParam(3, converti_data(date("D, d M Y", strtotime($v["date"]))));
						$add_feed->bindParam(4, addslashes(trim($v["link"])));
						$add_feed->bindParam(5, addslashes($parent_id));
						$add_feed->bindParam(6, addslashes($v["tags"]));
						$add_feed->bindParam(7, addslashes($_GET["user"]));
						if ($add_feed->execute()){
							$added = true;
						} else {
							//print $pdo->errorCode();
							print "Si &egrave; verificato un errore durante il salvataggio:\n" . $pdo->errorCode();
							break;
							$added = false;
						}
					} else {
						$edit_feed =  $pdo->prepare("update `editorss_feeds_news` set `title` = ('" . addslashes(trim($v["title"])) . "'), `description` = ('" . parse_content(strip_tags(utf8_decode(addslashes(trim($v["desc"]))))) . "'), `date` = ('" . converti_data(date("D, d M Y", strtotime($v["date"]))) . "'), `tags` = ('" . addslashes($v["tags"]) . "') where `link` = '" . addslashes($v["link"]) . "'");
						if ($edit_feed->execute()) {
							$added = true;
						} else {
							print "Si è verificato un errore durante il salvataggio:\n" . $pdo->errorCode();
							break;
							$added = false;
						}
					}
				}
			}
			if ($added == true){
				print "added";
			}
			break;
		case "check_page":
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $_GET["uri"]);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
			if ($output === false || $info != 200) {
				$output = null;
			}
			if ($output !== null){
				preg_match('@^(?:http://)?([^/]+)@i', $_GET["uri"], $matches);
				$full_host = $matches[0];
				preg_match_all('/<a.*?href="([^javascript].*)".*?>(.*)</U', $output, $links, PREG_PATTERN_ORDER);
			}
			if (is_array($links)){
				foreach($links[1] as $k => $v) {
					preg_match('@^(?:http(s)://).*@i', $v, $vmatches);
					if (count($vmatches) == 0){
						if (substr($v, 0, 1) !== "/"){
							$v2 = $full_host . "/" . $v;
						} else {
							$v2 = $full_host . $v;
						}
					} else {
						$v2 = $v;
					}
					$truelink[$k]["url"] = $v2;
					$truelink[$k]["text"] = $links[2][$k];
				}
				print json_encode(array("uri" => $truelink));
			} else {
				print "invalid uri";
			}
			curl_close($ch);
			break;
		case "check":
			print count($description) . "";
			break;
		case "test":
			print json_encode($description);
			break;
	}
} else {
	print "no get";
}
?>