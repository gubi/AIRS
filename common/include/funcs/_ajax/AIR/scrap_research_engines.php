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
require_once("../../calculate_tags.php");
require_once("../../../.mysql_connect.inc.php");
require_once("../../../browser.php");
require_once("../../../lib/simplehtmldom_1_5/simple_html_dom.php");

function check_tot_res($html, $txt = "risultat"){
	$enTxt = "result";
	$simple_html = new simple_html_dom();
	$simple_html->load($html);
	$aObj = $simple_html->find("text");
	foreach($aObj as $key => $oLove) {
		$plaintext = $oLove->plaintext;
		if (strpos($plaintext, $txt) !== false) {
			preg_match_all("/([0-9\(\)+,.\- ]+) \w+/", $oLove->parent()->plaintext, $tm);
			preg_match_all("/\w+ ([0-9\(\)+,.\- ]+)/", $oLove->parent()->plaintext, $tm2);
			$tot_res_it_[] = trim($tm[1][0]);
			$tot_res_it_[] = trim($tm2[1][0]);
		}
		if (strpos($plaintext, $enTxt) !== false) {
			preg_match_all("/([0-9\(\)+,.\- ]+) \w+/", $oLove->parent()->plaintext, $tm);
			preg_match_all("/\w+ ([0-9\(\)+,.\- ]+)/", $oLove->parent()->plaintext, $tm2);
			$tot_res_it_[] = trim($tm[1][0]);
			$tot_res_it_[] = trim($tm2[1][0]);
		}
	}
	if(is_array($tot_res_it_)){
		$tot_res_it_unique = array_unique($tot_res_it_);
		foreach($tot_res_it_unique as $k_tot_res_it => $v_tot_res_it){
			if(strlen(trim($v_tot_res_it))){
				$tot_res_it[] = $v_tot_res_it;
			}
		}
		return preg_replace("/[\s\W]+/", "", @join("", $tot_res_it));
		
		$simple_html->clear();
		unset($simple_html);
	} else {
		check_tot_res($html);
	}
}

function scrap_se($uri){
	// Indirizzo principale del browser (utile per le pagine successive);
	$url = parse_url($uri);
	$host = "http://" . $url["host"];
	$self = str_replace(strstr(urldecode($uri), "?"), "", urldecode($uri));
	// Richiama il browser di sistema e acquisisce tutti i dati possibili della pagina del motore di ricerca
	$browsing = browse(str_replace(" ", "+", $uri), $GLOBALS["decrypted_user"], "mixed");
	$GLOBALS["html"] = $browsing[1];
	$GLOBALS["info"] = $browsing[0];
	
	if ($GLOBALS["info"]["http_code"] == "200"){
		$simple_html = new simple_html_dom();
		$simple_html->load($GLOBALS["html"]);
		// Estrae il tag body
		$body = $simple_html->find("body");
		$body = join("", $body);
	
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
				$periodo = $period;
			}
		}
		
		// ELENCO DEI LINKS DI PAGINE
		// Ricerca i tag che contengono numeri
		preg_match_all("/<(.*?)\s.*?>(\d+)<\/(.*?)>/", $periodo, $page[]);
		// Per ogni risultato della lista controlla il tipo di tag
		foreach($page[0][0] as $k => $tag_type){
			// Se la pagina non è la n° 1
			if (trim($page[0][2][$k]) !== "1"){
				preg_match_all("/<a.*href=\"(.*?)\".*?>(.*?)<\/a>/siu", $page[0][0][$k], $matched_page_links[$k]);
				// Aggiunge il link delle prossime pagine
				
				// Numero prossima pagina
				$next_page_link["next_pages"][$k]["result_number"] = $page[0][2][$k];
				// Indirizzo prossima pagina
				$next_page_link["next_pages"][$k]["href"] = $self . strstr($matched_page_links[$k][1][0], "?");
			}
		}
		// Variabile globale con l'elenco di tutte le prossime pagine
		$res["next_pages_list"] = $next_page_link;
		
		$GLOBALS["total_result"] = check_tot_res($GLOBALS["html"]);
		$localhost = $host;
		preg_match_all("/<ol[^>]*>(.*)<\/ol>/siu", $periodo, $ol);
		preg_match_all("/<li[^>]*>(.*?)<\/li>/siu", $ol[1][0], $li);
		if (is_array($li[1])){
			foreach($li[1] as $k => $matched_li){
				// Pulisce tutto, tranne i tag "a" e "div"
				$match = strip_tags($matched_li, "<a><div>");
				if (strlen(trim($match)) > 0){
					$matched[] = $match;
					// Ricava tutti i links
					preg_match_all("/<a.*?href=\"(.*?)\".*?>(.*?)<\/a>/siu", $match, $match_a[$k][]);
				}
				// Acquisisce informazioni sulla pagina
				$the_url = str_replace(" ", "+", $match_a[$k][0][1][0]);
				if(substr($the_url, 0, 1) == "/"){
					$the_url = $localhost . $the_url;
				}
				$page_data = browse($the_url, $GLOBALS["decrypted_user"], "mixed");
				$page_info = $page_data[0];
				$page_html_content = $page_data[1];
				
				if ($page_info["http_code"] == "200"){
					preg_match('~charset=([-a-z0-9_]+)~i', $page_html_content, $met);
					$charset_page = $met[1];
					
					// Pulizia per l'anteprima
					$matched_preview = preg_replace("/<a[^>]*>.*?<\/a>/siu", "", $match);
					$matched_preview = preg_replace("/<[^>]*>.*<\/*>/siu", "", $matched_preview);
					if (strlen(trim(strip_tags($matched_preview))) > 0){
						// Aggiunge il link
						if (strlen($match_a[$k][0][2][0]) > 3){
							$res[$k]["link"]["text"] = utf8_decode(mb_convert_encoding($match_a[$k][0][2][0], "UTF-8", mb_detect_encoding($$match_a[$k][0][2][0])));
							if (substr($page_info["url"], 0, 1) == "/"){
								$result_link = $localhost . $page_info["url"];
							} else {
								$result_link = $page_info["url"];
							}
							//print $result_link . "\n";
							$res[$k]["link"]["href"] = urldecode(strstr(str_replace($localhost, "", $page_info["url"]), "http://"));
							// Aggiunge l'anteprima
							$res[$k]["description"] = utf8_decode(mb_convert_encoding(trim(strip_tags($matched_preview)), "UTF-8", mb_detect_encoding($matched_preview)));
							// Aggiunge il link della cache
							$res[$k]["cache"]["text"] = utf8_decode(mb_convert_encoding($match_a[$k][0][2][1], "UTF-8", mb_detect_encoding($match_a[$k][0][2][1])));
							if (substr($match_a[$k][0][1][1], 0, 2) == "//"){
								$res[$k]["cache"]["href"] = urldecode("http:" . $match_a[$k][0][1][1]);
							} else {
								$res[$k]["cache"]["href"] = urldecode(strstr(str_replace($localhost, "", $match_a[$k][0][1][1]), "http://"));
							}
						}
					}
				}
				// Inserisce dati aggiuntivi
				foreach($page_info as $pi_k => $pi_v){
					$res[$k][$pi_k] = $pi_v;
				}
				$res[$k]["html_content"] = utf8_decode(mb_convert_encoding($page_html_content, "UTF-8", mb_detect_encoding($charset_page)));
			}
			// Reimposta le chiavi primarie dell'array
			$results = @array_merge(array(), $res);
		} else {
			// Non sono stati trovati elenchi
			$results = array();
		}
		return $results;
	}
}
$_GET["search_uri"] = urldecode($_GET["search_uri"]);
if (!isset($_GET["search_uri"]) || trim($_GET["search_uri"]) == ""){
	print "È necessario inserire una ricerca da effettuare";
} else {
	// Connette ai database
	$pdo = db_connect("");
	$pdoa = db_connect("air");
	// Acquisisce l'utenza di riferimento
	//print "select * from `air_research` where `id` = '" . addslashes($_GET["id"]) . "'";
	$check_user = $pdo->prepare("select `user` from `air_research` where `id` = '" . addslashes($_GET["id"]) . "'");
	if($check_user->execute()){
		while($dato_check_user = $check_user->fetch()){
			$GLOBALS["decrypted_user"] = $dato_check_user["user"];
		}
	}
	// Scansiona la pagina 1
	$res[] = scrap_se($_GET["search_uri"]);
	
	// Scansiona le pagine da 2 a 10
	$p = 1;
	if (is_array($res[0]["next_pages_list"])){
		foreach($res[0]["next_pages_list"]["next_pages"] as $key => $value){
			$p++;
			//print "- - - - - - - - - - - - - - - - - - - - - - - - - - - - - -\nPagina " . $p . ":\n- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - \n";
			//print_r(scrap_se(mb_convert_encoding($value["href"], "UTF-8", "HTML-ENTITIES"), "results_list"));
			$res[] = scrap_se(mb_convert_encoding($value["href"], "UTF-8", mb_detect_encoding($value["href"])), "results_list");
			if ($p == 10){
				// Trovare la maniera di visualizzare anche le pagine oltre il 10
				//print_r(scrap_se($value["href"], "page_list"));
			}
		}
	}
	$c = 0;
	$pp = 0;
	
	if (is_array($res)){
		foreach($res as $pag => $page_res){
			$pp++;
			$risultato = 0;
			if (is_array($res[$pag])){
				foreach($res[$pag] as $result => $res_option){
					/*
					// Controlla che il risultato non ci sia già
					print $res[$pag][$result]["link"]["href"];
					exit();
					$check_existing = $pdo->prepare("select * from `air_research_results` where `result_uri` = '" . $res[$pag][$result]["link"]["href"] . "'");
					if($check_existing->rowCount() == 0){
						*/
						$risultato++;
						$c++;
						//print utf8_encode($res[$pag][$result]["link"]["text"]) . "\n";
						
						// Inserimento delle ricerche nel database
						$scraped_content = browse(str_replace(" ", "+", $res[$pag][$result]["link"]["href"]), $GLOBALS["decrypted_user"]);
						$scraped_content_cache = browse(str_replace(" ", "+", $res[$pag][$result]["cache"]["href"]), $GLOBALS["decrypted_user"]);
						// Pulisce le pagine da eventuali script malevoli
						preg_match("/<meta(.*?)>/si", $scraped_content, $meta_array);
						//print_r($meta_array);
						$scraped_content = preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", "", stripslashes($scraped_content));
						$scraped_content_cache = preg_replace("/<script\b[^>]*>(.*?)<\/script>/is", "", stripslashes($scraped_content_cache));
							//Rileva le keywords
							if(strlen($scraped_content) > 0) {
								preg_match_all('/<meta name="(\w+)" content="(.*?)"[^>]*>/i', stripslashes($scraped_content), $met);
								foreach($met as $m => $v){
									foreach($v as $mm => $vv){
										$meta[$met[1][$mm]] = mb_convert_encoding(stripslashes($met[2][$mm]), mb_detect_encoding($met[2][$mm]));
									}
								}
							}
						// Scansiona con linx per avere un risultato di solo testo
						$scraped_plain_content = shell_exec("lynx -dump -nolist -accept_all_cookies=on -hiddenlinks=ignore -display_charset=UTF-8 " . $res[$pag][$result]["link"]["href"]);
						if(strlen($scraped_plain_content) > 0) {
							// Calcola i tags
							if(is_array(calculate_tags($scraped_plain_content))){
								$tags = join(",", calculate_tags(stripslashes($scraped_plain_content)));
							}
							// Conta le parole
							$words = explode(" ", str_replace(array("\n", "\t"), " ", $scraped_plain_content));
						}
						$num_words = count($words);
						//$scraped_plain_content = @str_get_html($scraped_content)->plaintext;
						//$scraped_plain_content = utf8_decode(mb_convert_encoding($scraped_plain_content, "UTF-8", mb_detect_encoding($scraped_plain_content)));
						
						$add_se = $pdoa->prepare("insert into `air_research_results` (`research_id`, `search_engine_id`, `search_uri`, `search_total_results`, `result_uri`, `result_id`, `result_page`, `result_link_text`, `result_description`, `result_content`, `result_entire_html_content`, `result_cache_uri`, `result_cache_html_content`, `keywords`, `tags`, `words_count`, `user`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
						$add_se->bindParam(1, addslashes($_GET["id"]));
						$add_se->bindParam(2, addslashes($_GET["se_id"]));
						$add_se->bindParam(3, addslashes(urldecode($_GET["search_uri"])));
						$add_se->bindParam(4, $GLOBALS["total_result"]);
						$add_se->bindParam(5, $res[$pag][$result]["link"]["href"]);
						$add_se->bindParam(6, $risultato);
						$add_se->bindParam(7, $pp);
						$add_se->bindParam(8, addslashes($res[$pag][$result]["link"]["text"]));
						$add_se->bindParam(9, addslashes($res[$pag][$result]["description"]));
						$add_se->bindParam(10, addslashes($scraped_plain_content));
						$add_se->bindParam(11, addslashes($scraped_content));
						$add_se->bindParam(12, addslashes($res[$pag][$result]["cache"]["href"]));
						$add_se->bindParam(13, addslashes($scraped_content_cache));
						$add_se->bindParam(14, addslashes($meta["keywords"]));
						$add_se->bindParam(15, addslashes($tags));
						$add_se->bindParam(16, addslashes($num_words));
						$add_se->bindParam(17, addslashes($GLOBALS["decrypted_user"]));
						if (!$add_se->execute()) {
							//print "NO: insert into `air_research_results` (`research_id`, `search_uri`, `result_uri`, `result_id`, `result_page`, `result_link_text`, `result_description`, `result_content`, `result_entire_html_content`, `result_cache_uri`, `result_cache_html_content`, `user`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
							//break;
						} else {
							if ($c == count($res[$pag])){
								print "OK";
							}
						}
					//}
				}
			}
		}
	}
}
?>