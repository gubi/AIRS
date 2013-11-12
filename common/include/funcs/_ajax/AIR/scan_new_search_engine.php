<?php
/**
* Scan new search engine
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
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");

require_once("../../../browser.php");
require_once("../../_get_html_item-scrap.php");

if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	if(!strstr($_GET["uri"], "http")){
		$_GET["uri"] = "http://" . $_GET["uri"];
		if (substr($_GET["uri"], -1) == "/"){
			$_GET["uri"] = substr($_GET["uri"], 0, -1);
		}
	}
	// Scansiona il Motore di Ricerca
	$domDocument = new DOMDocument();
	@$domDocument->loadHTML(browse($_GET["uri"], $_GET["user"]));
	$documentTitle = $domDocument->getElementsByTagName("title");
	$documentDescription = $domDocument->getElementsByTagName("meta");
	$documentForm = $domDocument->getElementsByTagName("form");
	// Ne acquisisce il titolo
	$json["title"] = $documentTitle->item(0)->nodeValue;
	// E la descrizione
	for($a = 0; $a < $documentDescription->length; $a++) {
		$el = $documentDescription->item($a);
		
		if($el->getAttribute("name") == "description"){
			// Ne acquisisce la descrizione
			$json["description"] = utf8_decode($el->getAttribute("content"));
		}
	}
	//Cerca nel database se il motore è già presente
	$pdo = db_connect("air");
	$check_uri = $pdo->query("select * from `air_search_engines` where `search_page` like '%" . addslashes($_GET["uri"]) . "%'");
	if ($check_uri->rowCount() > 0){
		while ($dato_check_uri = $check_uri->fetch()) {
			$the_id = $dato_check_uri["id"];
			$is_global = $dato_check_uri["is_global"];
		}
		if ($is_global == "0"){
			$check_user_se = $pdo->query("select * from `air_users_search_engines` where `user` = '" . addslashes($_GET["user"]) . "' and `se_id` = '" . $the_id . "'");
			if ($check_user_se->rowCount() > 0){
				$json["error"] = "user already have this one";
				exit();
			} else {
				$add_se = $pdo->prepare("insert into `air_users_search_engines` (`user`, `se_id`) values(?, ?)");
				$add_se->bindParam(1, addslashes($_GET["user"]));
				$add_se->bindParam(2, addslashes($the_id));
				if (!$add_se->execute()) {
					$act = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
				} else {
					$json["error"] = "search engine already exists";
				}
			}
		}
	// Se non c'è
	} else {
		if ($documentForm->length > 0){
			for($i = 0; $i < $documentForm->length; $i++) {
				$documentForm = $documentForm->item($i);
				
				if(!strstr($documentForm->getAttribute("action"), "http")){
					if (substr($_GET["uri"], -1) == "/"){
						$_GET["uri"] = substr($_GET["uri"], 0, -1);
					}
					// Ne acquisisce il giusto URI
					$json["uri"] = $_GET["uri"] . $documentForm->getAttribute("action");
				} else {
					$json["uri"] = $documentForm->getAttribute("action");
				}
				
				$input = $documentForm->getElementsByTagName("input");
				for($j = 0; $j < $input->length; $j++) {
					$inp = $input->item($j);
					if($inp->getAttribute("type") == "text" || $inp->getAttribute("type") == "search"){
						// Ne acquisisce la variabile di ricerca
						$json["search_var"] = $inp->getAttribute("name");
					}
				}
			}
			
			// Effettua una ricerca di prova
			$ch1 = curl_init();
			curl_setopt($ch1, CURLOPT_URL, $json["uri"]);
			curl_setopt($ch1, CURLOPT_HEADER, 1);
			curl_setopt($ch1, CURLOPT_NOBODY, 1);
			curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch1, CURLOPT_BINARYTRANSFER, 1);
			curl_setopt($ch1, CURLOPT_TIMEOUT, 0);
			curl_setopt($ch1, CURLOPT_POST, 1);
			curl_setopt($ch1, CURLOPT_USERAGENT, "AIRS/1.0 (airs@inran.it)");
			curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
				$data = array($json["search_var"] => "Hello world");
			curl_setopt($ch1, CURLOPT_POSTFIELDS, $data);
			$output1 = curl_exec_follow($ch1);
			$info1 = curl_getinfo($ch1);
			if ($output1 === false || $info1["http_code"] != 200) {
				$output1 = null;
			} else {
				// Ne ricava l'uri per la ricerca avanzata
				preg_match_all("/\<a.*?href\=\"(.*?)\".*?\>(.*?)\<\/a\>/", $output1, $matched);
				if (is_array($matched)){
					foreach($matched[1] as $mk => $mv){
						if (strstr($mv, "advanced")){
							$json["advanced_search"] = str_replace(array("?" . $json["search_var"] . "=", "&" . $json["search_var"] . "=", urlencode("Hello world")), "", $matched[1][$mk]);
						}
					}
				}
				// Controlla l'elenco ufficiale delle nomenclature dei paesi secondo lo standard ISO 3166
				//require_once("../../get_iso_3166_list.php");
				//$iso_3166 = iso_3166();
				
				//print_r($json["dev_links"]);
				$json["error"] = "none";
			}
		} else {
			$json["error"] = "no form";
		}
	}
	print json_encode($json);
}
?>