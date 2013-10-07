<?php
/**
* Retrieve list of Country_codes from IANA database
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
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
header("Content-type: text/plain; charset=utf8;");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$pdo = db_connect(".airs");
	$term = strtoupper(addslashes($_GET["term"]));
	
	// Apre il file dello IANA
	$filename = "http://data.iana.org/TLD/tlds-alpha-by-domain.txt";
	$handle = fopen($filename, "r");
	$contents = stream_get_contents($handle);
	$line = explode("\n", $contents);
	// Versione del documento
	$response["version"] = $line[0];
	
	// Controlla nel database l'elenco dei Paesi
	if (strlen($term) == 2){
		$query = "select * from `ISO_3166-1` where `Country_codes` = '" . $term . "' or `Country` like '" . $term . "%' order by `Country_codes` asc limit 3";
	} else {
		$query = "select * from `ISO_3166-1` where `Country` like '" . $term . "%' order by `Country` asc limit 3";
	}
	$query_se_countries = $pdo->query($query);
	if ($query_se_countries->rowCount() == 0){
		$query_se_countries = $pdo->query("select * from `ISO_3166-1` where `Country` like '%" . $term . "%' order by `Country` asc limit 3");
	}
	if ($query_se_countries->rowCount() > 0){
		while($dato_se_countries = $query_se_countries->fetch()){
			// Se il Paese è nell'elenco dello IANA
			if (in_array(strtoupper($dato_se_countries["Country_codes"]), $line)){
				$results["id"] = $dato_se_countries["Country_codes"];
				$results["label"] = "." . strtolower($dato_se_countries["Country_codes"]);
				$results["value"] = "." . strtolower($dato_se_countries["Country_codes"]);
			} 
		}
		print json_encode(array($results));
	} else {
		foreach($line as $extension){
			similar_text($term, $extension, $percentage);
			if ($percentage > 50){
				$results["id"] = strtoupper($term);
				$results["label"] = "." . strtolower($extension);
				$results["value"] = "." . strtolower($extension);
			}
		}
		print json_encode(array($results));
	}
}
?>