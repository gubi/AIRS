<?php
header("Content-type: text/plain; charset=utf-8");
require_once("../../calculate_tags.php");
require_once("FirePHPCore/FirePHP.class.php");
$firephp = FirePHP::getInstance(true);

if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	require_once("../../../lib/php-readability-master/Readability.php");
	if (isset($_GET["type"]) && trim($_GET["type"]) !== ""){
		$show = $_GET["type"];
	}
	
	$absolute_path = str_replace(array("/web/htdocs/", "index.php", "/home", "/common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php"), array("http://", "", "", ""), realpath(__FILE__));
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
	if (@fopen($_GET["uri"], 'r')){
		$doc = new DOMDocument();
		if (@$doc->load($_GET["uri"])){
			$arrFeeds = array();
			$arrDescs = array();
			foreach ($doc->getElementsByTagName('item') as $node) {
				$itemRSS = array ( 
					'title' => $node->getElementsByTagName('title')->item(0)->nodeValue,
					'desc' => $node->getElementsByTagName('description')->item(0)->nodeValue,
					'link' => $node->getElementsByTagName('link')->item(0)->nodeValue,
					'date' => $node->getElementsByTagName('pubDate')->item(0)->nodeValue
				);
				array_push($arrFeeds, $itemRSS);
			}
			foreach($doc->getElementsByTagName("channel") as $channel){
				$categories = $channel->getElementsByTagName('category');
				$i = -1;
				foreach ($categories as $cats) {
					$i++;
					$categorie[] = $channel->getElementsByTagName('category')->item($i)->nodeValue;
				}
				$description = array(
					"title" => trim(ucfirst($channel->getElementsByTagName('title')->item(0)->nodeValue)),
					"description" => trim(ucfirst($channel->getElementsByTagName('description')->item(0)->nodeValue)),
					"link" => trim($channel->getElementsByTagName('link')->item(0)->nodeValue),
					"lastBuildDate" => $channel->getElementsByTagName('lastBuildDate')->item(0)->nodeValue,
					"generator" => trim($channel->getElementsByTagName('generator')->item(0)->nodeValue),
					"category" => $categorie,
					"valid_resources" => count($arrFeeds)
				);
				$description["tags"] = calculate_tags(strip_tags(utf8_decode(trim($channel->getElementsByTagName('description')->item(0)->nodeValue))));
				array_push($arrDescs, $description);
				
			}
		} else {
			$description = array("response" => "invalid uri");
		}
	} else {
		$description = array("response" => "invalid uri");
	}
	switch($show){
		case "test":
			$c = 0;
			foreach ($arrFeeds as $k => $v){
				$c++;
				if ($c == 1){
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
					//print count(array_diff($prev_d, $prev));
					print str_replace(array("\t", "  "), array("", " "), trim($text));
				}	
			}
			break;
	}
}
?>