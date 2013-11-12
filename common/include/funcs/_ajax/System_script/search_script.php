<?php
/**
* Search a script
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
* @package	AIRS_System_scripts
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");

if(isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$term = addslashes($_GET["term"]);
	$pdLSM = db_connect("slm");
	$system_search = $pdLSM->query("select * from `current_scripts` where `path` like '%" . $term . "%'");
	if($system_search->rowCount() > 0){
		$r = -1;
		$results = null;
		while($dato_system_search = $system_search->fetch()){
			if(is_file($_SERVER["DOCUMENT_ROOT"] . "/" . $dato_system_search["path"])){
				$info = pathinfo($dato_system_search["path"]);
				similar_text($info["basename"], $term, $percent);
				if(round($percent, 2) >= 52){
					$r++;
					$results[$r]["id"] = array($dato_system_search["id"]);
					$results[$r]["label"] = $info["basename"];
				}
			}
		}
	} else {
		$results = null;
	}
	print json_encode(array("results" => $results));
}
?>