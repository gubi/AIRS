<?php
/**
* Edit a research from AIR database
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
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");
require_once("../../translate.php");

if (isset($_GET["se_title"]) && trim($_GET["se_title"]) !== ""){
	if (isset($_GET["se_search_engines"]) && count($_GET["se_search_engines"]) !== 0){
		if (isset($_GET["se_query"]) && trim($_GET["se_query"]) !== ""){
			if (is_array($_GET["se_tags"])) {
				if (count($_GET["se_tags"]) > 1){
					$se_tags = implode(",", $_GET["se_tags"]);
				} else {
					$se_tags = $_GET["se_tags"][0];
				}
			} else {
				$se_tags = $_GET["se_tags"];
			}
			if (is_array($_GET["se_search_engines"])){
				if (count($_GET["se_search_engines"]) > 1){
					$se_search_engines = implode(",", $_GET["se_search_engines"]);
				} else {
					$se_search_engines = $_GET["se_search_engines"][0];
				}
			} else {
				$se_search_engines = $_GET["se_search_engines"];
			}
			if (is_array($_GET["se_lang"])){
				if (count($_GET["se_lang"]) > 1){
					$se_langs = implode(",", $_GET["se_lang"]);
				} else {
					$se_langs = $_GET["se_lang"][0];
				}
			} else {
				$se_langs = $_GET["se_lang"];
			}
			if (is_array($_GET["se_country"])){
				if (count($_GET["se_lang"]) > 1){
					$se_countries = implode(",", $_GET["se_country"]);
				} else {
					$se_countries = $_GET["se_country"][0];
				}
			} else {
				$se_countries = $_GET["se_country"];
			}
			if (isset($_GET["se_lastdate_val"])){
				$filter_date = date("Y-m-d", strtotime(converti_data(substr($_GET["se_lastdate_val"], -11), "en", "month")));
			} else {
				$filter_date = "";
			}
			
			$pdo = db_connect("air");
			$edit_se = $pdo->prepare("update `air_research` set `title` = ('" . addslashes($_GET["se_title"]) . "'), `description` = ('" . addslashes($_GET["se_description"]) . "'), `tags` = ('" . addslashes($se_tags) . "'), `search_engines` = ('" . addslashes($se_search_engines) . "'), `query` = ('" . addslashes($_GET["se_query"]) . "'), `languages` = ('" . addslashes($se_langs) . "'), `filter_domain` = ('" . addslashes($_GET["se_site"]) . "'), `filter_filetype` = ('" . addslashes($_GET["se_filetype_var"]) . "'), `filter_date` = ('" . addslashes($filter_date) . "'), `filter_region` = ('" . addslashes($se_countries) . "') where `id` = '" .  addslashes($_GET["se_id"]) . "'");
			if (!$edit_se->execute()) {
				$act = "error:Si è verificato un errore durante il salvataggio:\n";
			} else {
				$last_research_id = $_GET["se_id"];
				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
				// AUTOMAZIONE
				// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
							// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
							// CREAZIONE DELL'URI DI RICERCA
							// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
							// Estrapolazione dei codici ISO-639-3 per le lingue
							$pdo = db_connect("");
							$lang_var = $se_langs;
							$country_var = $se_countries;
							// Simboli accettate per suddividere i parametri (query di ricerca | variabile uri)
							$accept_var_query = array(":", " ", "\"");
							
								// Accoppiamento colonne_database~variabili_GET in un array
								$se_db_column = array("search_var" => urlencode($_GET["se_query"]));
								if (strlen(trim($lang_var)) > 0){
									$se_db_column["language_var"] = $lang_var;
								}
								if (strlen(trim($_GET["se_site"])) > 0){
									$se_db_column["site_var"] = urlencode($_GET["se_site"]);
								}
								if (strlen(trim($_GET["se_filetype_var"])) > 0){
									$se_db_column["filetype_var"] = urldecode($_GET["se_filetype_var"]);
								}
								// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DA VEDERE: country region
								/*
								if (strlen(trim($country_var)) > 0){
									$se_db_column["country_var"] = strtolower($country_var);
								}
								*/
								// Controllori
								//print_r($se_db_column);
								//print_r($se_db_column2);
							$pdo = db_connect("air");
							// Costruzione della query di ricerca
							foreach($_GET["se_search_engines"] as $search_engine){
								// Cerca nel database dei motori di ricerca selezionati dall'utente
								$check_se = $pdo->query("select * from `air_search_engines` where `id` = '" . addslashes($search_engine) . "'");
								while($dato_se = $check_se->fetch()){
									// Inizializza l'array dell'url
									$url_arr[$dato_se["id"]][] = $dato_se["search_page"] . "?";
									foreach($se_db_column as $column => $se_value){
										//print $column . " ~ " . $se_value . "\n";
										
										if (in_array(substr($dato_se[$column], -1), $accept_var_query)){
											// Variabili nella query
											$se_params[$dato_se["id"]]["query"][$column] = $dato_se[$column];
											$query_arr[$dato_se["id"]][] = " " . $dato_se[$column] . $se_value;
										} else {
											// Variabili nell'url
											if (strpos($dato_se[$column], ",") === false){
												// Se non hanno la virgola
												if (substr($dato_se[$column], -1) != "="){
													$dato_se[$column] .= "=";
												}
											} else {
												// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ DA RIVEDERE
												// Se hanno la virgola è un array
												//$column_arr = explode(",", $dato_se[$column]);
												
												// Dato che è da rivedere adesso salta la ricerca
												break;
											}
											$se_params[$dato_se["id"]]["url"][$column] = $dato_se[$column];
											$url_arr[$dato_se["id"]][] = $dato_se[$column] . $se_value;
										}
									}
								}
							}
							// Inserisce i parametri nella query, se sono richiesti dai parametri del browser
							foreach ($url_arr as $id => $v){
								if (is_array($query_arr[$id])){
									$url_arr[$id][1] = str_replace(" ", "+", $url_arr[$id][1] . implode("", $query_arr[$id]));
								}
							}
				// Inserimento nel database delle automazioni
				$pdo2 = db_connect("");
				// Per ogni url creato (in base a quanti motori sono stati scelti)
				foreach($url_arr as $query_id => $query_params) {
					// GIOIELLINO D'ORO: l'uri di ricerca
					//print $query_id . " = " . implode("&", $query_params) . "\n";
					$research_uri = "common/include/funcs/_ajax/AIR/scrap_research_engines.php?id=" . $last_research_id . "&search_uri=" . urlencode(implode("&", $query_params));
					// Crea un array degli indirizzi di ricerca
					$research_uri_arr[] = $research_uri;
					
					// Se non è stata impostata una data di inizio la imposta ad oggi
					if (!isset($_GET["automation_date"])){
						$_GET["automation_date"] = converti_data(date("D, d M Y"));
					}
					// Se non è stata impostata un'ora di inizio la imposta ad adesso
					if (!isset($_GET["automation_time"])){
						$_GET["automation_time"] = date("H:i");
					}
					$start_date = date("Y-m-d", strtotime(converti_data($_GET["automation_date"], "en")));
					$start_time = date("H:i:s", strtotime($_GET["automation_time"]));
					$is_uri = true;
					
					if ($_GET["automation"] == "on"){
						$edit_automation =  $pdo2->prepare("update `airs_automation` set `action` = ('" . addslashes($research_uri) . "'), `frequency` = ('" . addslashes($_GET["automation_cadence"]) . "'), `is_uri_execution` = ('" . $is_uri . "'), `start_date` = ('" . $start_date . "'), `start_time` = ('" . $start_time . "'), `user` = ('" . addslashes($_GET["decrypted_user"]) . "') where `action` = '" . addslashes($_GET["action"]) . "'");
						if (!$edit_automation->execute()) {
							$act = "error:Si è verificato un errore durante il salvataggio<br />" . $pdo->errorCode();
						}
					} else {
						$edit_automation =  $pdo2->prepare("delete from `airs_automation` where `action` = '" . addslashes($_GET["action"]) . "'");
						if (!$edit_automation->execute()) {
							$act = "error:Si è verificato un errore durante la cancellazione<br />" . $pdo->errorCode();
						}
					}
				}
				// Inserisce l'uri di ricerca
				$research_uris = implode(",", $research_uri_arr);
				$pdo = db_connect("air");
				$add_se_uries = $pdo->prepare("update `air_research` set `research_uris` = ('" . $research_uris . "') where `id` = '" . $last_research_id . "'");
				if (!$add_se_uries->execute()) {
					$act = "error:Si è verificato un errore durante il salvataggio degli indirizzi di ricerca:<br />" . $pdo->errorCode();
				} else {
					$act = "edited:" . $last_research_id;
				}
			}
		} else {
			$act = "error:È necessario inserire una query di ricerca:se_query";
		}
	} else {
		$act = "error:È necessario specificare almeno un motore di ricerca:se_search_engines";
	}
} else {
	$act = "error:È necessario inserire un titolo:se_title";
}
print $act;
?>