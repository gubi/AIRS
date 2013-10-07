<?php
/**
* Returns a list of translated Country Names
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
	$pdo = db_connect(".airs");
	
	$term = strtolower(addslashes($_GET["term"]));
	if (strlen($term) == 2){
		$query = "select * from `ISO_3166-1` where `Country_codes` = '" . $term . "' or `Country` like '" . $term . "%' order by `Country_codes` asc limit 3";
	} else {
		$query = "select * from `ISO_3166-1` where `Country` like '" . $term . "%' order by `Country` asc limit 3";
	}
	$query_se_countries = $pdo->query($query);
	if ($query_se_countries->rowCount() == 0){
		if (strlen($term) == 2){
			$query = "select * from `ISO_3166-1` where `Country_codes` = '" . $term . "' or `Country` like '" . translate($term, "it", "en") . "%' order by `Country_codes` asc limit 3";
		} else {
			$query = "select * from `ISO_3166-1` where `Country` like '" . translate($term, "it", "en") . "%' order by `Country` asc limit 3";
		}
		$query_se_countries = $pdo->query($query);
	}
	if ($query_se_countries->rowCount() == 0){
		$query_se_countries = $pdo->query("select * from `ISO_3166-1` where `Country` like '%" . $term . "%' order by `Country` asc limit 3");
	}
	if ($query_se_countries->rowCount() == 0){
		$query_se_countries = $pdo->query("select * from `ISO_3166-1` where `Country` like '%" . translate($term, "it", "en") . "%' order by `Country` asc limit 3");
	}
	$i = 0;
	if ($query_se_countries->rowCount() > 0){
		while($dato_se_countries = $query_se_countries->fetch()){
			$results["id"] = $dato_se_countries["Country_codes"];
			$results["label"] = ucwords(strtolower(translate($dato_se_countries["Country"])));
			$results["value"] = ucwords(strtolower(translate($dato_se_countries["Country"])));
			$result[] = $results;
		}
	}
	print json_encode($result);
}
?>