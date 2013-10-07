<?php
/**
* Returns a list of translated languages
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
require_once("../translate.php");

if (isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$pdo = db_connect("");
	
	$term = strtolower(addslashes($_GET["term"]));
	$query_se_languages = $pdo->query("select * from `ISO_639-3` where `Ref_Name` like '" . $term . "%' and `Part1` != '' order by `Ref_Name` asc limit 3");
	if ($query_se_languages->rowCount() == 0){
		//print "provo ricerca in italiano: " . strtolower(translate($term, "it", "en"));
		$query_se_languages = $pdo->query("select * from `ISO_639-3` where `Ref_Name` like '" . strtolower(translate($term, "it", "en")) . "%' and `Part1` != '' order by `Ref_Name` asc limit 3");
	}
	if ($query_se_languages->rowCount() > 0){
		while($dato_se_lang = $query_se_languages->fetch()){
			$results["id"] = $dato_se_lang["Part1"];
			$results["label"] = ucwords(translate(strtolower($dato_se_lang["Ref_Name"])));
			$results["value"] = ucwords(translate(strtolower($dato_se_lang["Ref_Name"])));
		}
	}
	print json_encode(array($results));
}
?>