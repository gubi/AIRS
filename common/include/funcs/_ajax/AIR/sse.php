<?php
/**
* Crawl Search Engines
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
* @package	AIRS_AIR
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8;");
ini_set("max_execution_time", 0);
set_time_limit(0);

require_once("../../_blowfish.php");
require_once("../../../.mysql_connect.inc.php");
require_once("../../../browser.php");
require_once("../../../lib/simplehtmldom_1_5/simple_html_dom.php");

ob_start();
function scrap_se_content($URI, $page_no, $search_uri){
	require_once("../../../.mysql_connect.inc.php");
	$pdo = db_connect("air");
	
	$uri = str_replace(" ", "+", urldecode($URI));
	$browsed = browse($uri, $GLOBALS["decrypted_user"], "mixed");
	if (count($browsed) == 0){
		$browsed = browse($uri, $GLOBALS["decrypted_user"], "mixed");
		exit();
	}
	$info = $browsed[0];
	$html = $browsed[1];
	
	$url = parse_url($uri);
	$host = "http://" . $url["host"];
	//print $host . "\n\n";
	
	preg_match("/<body[^>]*>(.*?)<\/body>/si", $html, $matched_body);
	$body = $matched_body[1];
	// Elimina tutti i caratteri tag ("	")
	$body = str_replace("\t", "", $body);
	// Elimina tutti gli spazi eccessivi ("  ")
	$body = preg_replace("/\s\s+/", "", $body);
	// Elimina tutti gli script
	$body = preg_replace("/<script[^>]*>.*?<\/script>/siu", "", $body);
	// Elimina tutti i menu
	$body = preg_replace("/<li[^>]*><a.*>(\w+)<\/a><\/li>/siu", "", $body);
	$body = preg_replace("/<li[^>]*><\/li>/siu", "", $body);
	// Toglie tutti i tags eccetto "div", "p", "span" e "a"
	$body = strip_tags($body, "<div><p><span><a><b><i><u><ul><ol><li><strike>");
	// Inserisce ogni riga in un array
	$items = explode("\n", $body);
	foreach ($items as $i => $period){
		if (strlen(strip_tags($period)) == 0){
			// Pulisce l'array dai valori vuoti
			unset($items[$i]);
		} else {
			$central_nav = $period;
		}
	}
	$html_ = str_get_html($central_nav);
	
	// TROVA I RISULTATI
	$k = 0;
	foreach($html_->find("ol li") as $li){
		$j = 0;
		$k++;
		foreach($li->find("a") as $a){
			$link_info_txt = "";
			$cached_info_txt = "";
			$j++;
			if  ($j == 1){
				$href = urldecode(strstr($a->href, "http"));
				$only_url = str_replace("http://", "", $href);
				$only_url_parsed = parse_url($href);
				//print_r($only_url_parsed);
				$only_url_host = $only_url_parsed["host"];
				$only_url_path = $only_url_parsed["path"];
				$browsed_link = browse($href, $GLOBALS["decrypted_user"], "mixed");
				$link_info = $browsed_link[0];
				$link_html = $browsed_link[1];
				foreach($link_info as $link_info_type => $link_info_value){
					$link_info_txt .= "[" . $link_info_type . "] => " . $link_info_value . "\n";
				}
				$res[$k]["info"] = $link_info_txt;
				$res[$k]["link"]["text"] = mb_convert_encoding(trim($a->plaintext), "UTF-8", "HTML-ENTITIES");
				$res[$k]["link"]["href"] = $href;
				
				$l_html = str_get_html($link_html);
				$res[$k]["link"]["content"] = $l_html->plaintext;
				$res[$k]["link"]["entire_content"] = $link_html;
				$l_html->clear(); 
				unset($l_html);
			}
			if  ($j == 2){
				$cachehref = urldecode(strstr($a->href, "http"));
				$browsed_cached = browse($cachehref, $GLOBALS["decrypted_user"], "mixed");
				$cached_info = $browsed_cached[0];
				$cached_html = $browsed_cached[1];
				foreach($cached_info as $cache_info_type => $cache_info_value){
					$cached_info_txt .= "[" . $cache_info_type . "] => " . $cache_info_value . "\n";
				}
				$res[$k]["cache"]["info"] = $cached_info_txt;
				$res[$k]["cache"]["href"] = $cachehref;
				$res[$k]["cache"]["entire_content"] = $cached_html;
			}
			
			$a->outertext = "";
		}
		$li->plaintext = strip_tags($li->innertext);
		$li->plaintext = trim(str_replace($only_url, "", $li->plaintext));
		$li->plaintext = trim(str_replace($only_url_host, "", $li->plaintext));
		$li->plaintext = trim(str_replace($only_url_path, "", urldecode($li->plaintext)));
		
		$res[$k]["description"] = utf8_decode(mb_convert_encoding($li->plaintext, "UTF-8", "HTML-ENTITIES"));
	}
	$html_->clear(); 
	unset($html_);
	
	$add_se = $pdo->prepare("insert into `air_research_results` (`research_id`, `search_uri`, `result_uri`, `user`) values(?, ?, ?, ?)");
	$add_se->bindParam(1, addslashes($_GET["id"]));
	$add_se->bindParam(2, addslashes($search_uri));
	$add_se->bindParam(3, addslashes($uri));
	$add_se->bindParam(4, addslashes($GLOBALS["decrypted_user"]));
	if (!$add_se->execute()) {
		print "Si è verificato un errore durante il salvataggio:\n" . $pdo->errorCode();
		break;
	} else {
		$last_id = $pdo->query("select max(`id`) as 'maxid' from `air_research_results`");
		while($dato_last_id = $last_id->fetch()){
			$the_id = $dato_last_id["maxid"];
		}
	}
	return $res;
	//exit();
}
function scrap_se($URI, $type = "first", $page_no = 1, $continues_for_the_next = 3){
	require_once("../../../.mysql_connect.inc.php");
	$pdo = db_connect("air");
	
	$uri = str_replace(" ", "+", urldecode($URI));
	$browsed = browse($uri, $GLOBALS["decrypted_user"], "mixed");
	if (count($browsed) == 0){
		$browsed = browse($uri, $GLOBALS["decrypted_user"], "mixed");
		exit();
	}
	$info = $browsed[0];
	$html = $browsed[1];
	
	// Se la pagina risponde Status OK (200)
	if ($info["http_code"] == "200"){
		$res["page"]["info"] = $info;
		
		$url = parse_url($uri);
		$host = "http://" . $url["host"];
		//print $host . "\n\n";
		
		preg_match("/<body[^>]*>(.*?)<\/body>/si", $html, $matched_body);
		$body = $matched_body[1];
		// Elimina tutti i caratteri tag ("	")
		$body = str_replace("\t", "", $body);
		// Elimina tutti gli spazi eccessivi ("  ")
		$body = preg_replace("/\s\s+/", "", $body);
		// Elimina tutti gli script
		$body = preg_replace("/<script[^>]*>.*?<\/script>/siu", "", $body);
		// Elimina tutti i menu
		$body = preg_replace("/<li[^>]*><a.*>(\w+)<\/a><\/li>/siu", "", $body);
		$body = preg_replace("/<li[^>]*><\/li>/siu", "", $body);
		// Toglie tutti i tags eccetto "div", "p", "span" e "a"
		$body = strip_tags($body, "<div><p><span><a><b><i><u><ul><ol><li><strike>");
		// Inserisce ogni riga in un array
		$items = explode("\n", $body);
		foreach ($items as $i => $period){
			if (strlen(strip_tags($period)) == 0){
				// Pulisce l'array dai valori vuoti
				unset($items[$i]);
			} else {
				$central_nav = $period;
			}
		}
		if (strlen($central_nav) == 0){
			$browsed = browse($uri, $GLOBALS["decrypted_user"], "mixed");
			exit();
		}
		$html_ = str_get_html($central_nav);
		
		$page_counter = 1;
		// TROVA I NUMERI DELLE PAGINE
		foreach($html_->find("a") as $page_link){
			if (is_numeric($page_link->plaintext)){
				if ($type == "first"){
					if ($page_counter < $continues_for_the_next){
						$page_counter++;
					}
				} else {
					if ($page_counter < $continues_for_the_next){
						$page_counter++;
					}
				}
				// Definisce le scansioni delle pagine seguire
				if ((int)$page_link->plaintext >= $page_no){
					$next = ($page_no + $page_counter);
					//print $page_no . " ~ " . (int)$page_link->plaintext . " -> " . $next . "\n";
					if ((int)$page_link->plaintext <= $next){
						$res["page"][$page_no]["next_pages"][$page_link->plaintext] = $host . mb_convert_encoding($page_link->href, "ASCII", "HTML-ENTITIES");
					}
				}
			}
		}
		$html_->clear(); 
		unset($html_);
		// Check della numerazione delle pagine
		foreach($res["page"][$page_no]["next_pages"] as $pn => $pl){
			// Se la pagina indicata è nell'array delle pagine seguenti 
			//print $pn . " ~ " . (int)$page_no . "\n";
			if ((int)$page_no == $pn){
				// Scansione della pagina attuale
				$res = scrap_se($pl, "next", $pn, $continues_for_the_next);
				$res["page"][$pn]["results"] = scrap_se_content($pl, $pn, $search_uri);
			} else {
				usleep(rand(0, 500000));
				// Prossime pagine da scansionare
				$res["page"][$pn]["results"] = scrap_se_content($pl, $pn, $search_uri);
				//print "next line added\n" . str_repeat(".", 100);
				//ob_flush();
			}
		}
		//print_r($res);
	} else {
		return $info["http_code"];
	}
}
flush();
if (!isset($GLOBALS["decrypted_user"])){
	$GLOBALS["decrypted_user"] = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);
}
//print urldecode($_GET["search_uri"]) . "\n";
if (!isset($_GET["search_uri"]) || trim($_GET["search_uri"]) == ""){
	print "È necessario inserire una ricerca da effettuare";
} else {
	scrap_se($_GET["search_uri"], "first", 1, 10);
}

ob_end_flush();
?>