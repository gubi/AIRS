<?php
/**
* Export to CSV format
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
require_once("../../../.mysql_connect.inc.php");

//header("Content-type: text/plain");
header("Content-type: application/force-download; charset=utf-8");
header("Content-Disposition: attachment; filename=feeds_editorss_" . date("d-m-Y") . "." . $_POST["format"]);

$pdo = db_connect("editorss");
if (isset($_GET["ids"]) && trim($_GET["ids"]) !== ""){
	$ids = explode(",", $_GET["ids"]);
	foreach($ids as $id){
		$idds[] = $id;
	}
	sort($idds);
	switch($_POST["format"]){
		case "pdf":
			break;
		case "csv":
			print "\"ID\",\"TITOLO\",\"DESCRIZIONE\",\"URI\",\"RISORSE VALIDE\",\"TAG\",\"GRUPPI\",\"UTENTE DI AFFERENZA\",\"DATA DI INSERIMENTO\",\"STATO DELLE SCANSIONI\"\r\n";
			break;
		case "rdf":
			break;
	}
	foreach($idds as $id){
		$feed = $pdo->query("select * from `editorss_feeds` where `id` = '" . addslashes($id) . "'");
		while($dato_feed = $feed->fetch()){
			if ($dato_feed["is_active"] == "1"){
				$scan_status = "attiva";
			} else {
				$scan_status = "disattivata";
			}
			switch($_POST["format"]){
				case "pdf":
					break;
				case "csv":
					print "\"" . $dato_feed["id"] . "\",\"" . $dato_feed["title"] . "\",\"" . utf8_encode(html_entity_decode($dato_feed["description"])) . "\",\"" . $dato_feed["uri"] . "\",\"" . $dato_feed["valid_resources"] . "\",\"" . $dato_feed["tags"] . "\",\"" . $dato_feed["group"] . "\",\"" . $dato_feed["user"] . "\",\"" . date("d/m/Y H:i:s", strtotime($dato_feed["last_insert_date"])) . "\",\"" . $scan_status . "\"\r\n";
					break;
				case "rdf":
					break;
			}
		}
	}
} else {
	switch($_POST["format"]){
		case "pdf":
			break;
		case "csv":
			print "\"ID\",\"TITOLO\",\"DESCRIZIONE\",\"URI\",\"RISORSE VALIDE\",\"TAG\",\"GRUPPI\",\"UTENTE DI AFFERENZA\",\"DATA DI INSERIMENTO\",\"STATO DELLE SCANSIONI\"\r\n";
			break;
		case "rdf":
			break;
	}
	
	$feed = $pdo->query("select * from `editorss_feeds`");
	while($dato_feed = $feed->fetch()){
		if ($dato_feed["is_active"] == "1"){
			$scan_status = "attiva";
		} else {
			$scan_status = "disattivata";
		}
		switch($_POST["format"]){
			case "pdf":
				break;
			case "csv":
				print "\"" . $dato_feed["id"] . "\",\"" . $dato_feed["title"] . "\",\"" . utf8_encode(html_entity_decode($dato_feed["description"])) . "\",\"" . $dato_feed["uri"] . "\",\"" . $dato_feed["valid_resources"] . "\",\"" . $dato_feed["tags"] . "\",\"" . $dato_feed["group"] . "\",\"" . $dato_feed["user"] . "\",\"" . date("d/m/Y H:i:s", strtotime($dato_feed["last_insert_date"])) . "\",\"" . $scan_status . "\"\r\n";
				break;
			case "rdf":
				break;
		}
	}
}
?>