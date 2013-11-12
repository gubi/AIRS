<?php
/**
* Add a search engine to AIR
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
header("content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");

if (isset($_POST) || isset($_GET)){
	require_once("../../_converti_data.php");
	require_once("../../calculate_tags.php");
	$pdo = db_connect("air");
	
	$params = array("se_search_var", "se_lang_var", "se_site_var", "se_filetype_var", "se_lastdate_var");
	foreach ($params as $k => $v){
		if (substr(trim($_POST[$k]), -1, 1) !== "="){
			$_POST[$k] .= "=";
		}
		$_POST[$k] = str_replace("==", "=", $_POST[$k]);
	}
	if (trim($_POST["se_quoting_var"]) !== ""){
		$_POST["se_quoting_var"] = $_POST["se_quoting_var"] . "{1}" . $_POST["se_quoting_var"];
	}
	$se_search_var = $_POST["se_search_var"] . "=";
	
	$add_se = $pdo->prepare("insert into `air_search_engines` (`name`, `description`, `search_page`, `search_var`, `language_var`, `site_var`, `filetype_var`, `last_date_var`, `last_date_val`, `country_var`, `quoting_var`, `or_var`, `exclusion_var`) values(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$add_se->bindParam(1, addslashes($_POST["se_title"]));
	$add_se->bindParam(2, addslashes($_POST["se_description"]));
	$add_se->bindParam(3, addslashes($_POST["se_uri"]));
	$add_se->bindParam(4, addslashes($se_search_var));
	$add_se->bindParam(5, addslashes($_POST["se_lang_var"]));
	$add_se->bindParam(6, addslashes($_POST["se_site_var"]));
	$add_se->bindParam(7, addslashes($_POST["se_filetype_var"]));
	$add_se->bindParam(8, addslashes($_POST["se_lastdate_var"]));
	$add_se->bindParam(9, addslashes($_POST["se_lastdate_val"]));
	$add_se->bindParam(10, addslashes($_POST["se_country_var"]));
	$add_se->bindParam(11, addslashes($_POST["se_quoting_var"]));
	$add_se->bindParam(12, addslashes($_POST["se_OR_var"]));
	$add_se->bindParam(13, addslashes($_POST["se_NOT_var"]));
	if (!$add_se->execute()) {
		$act = "error:Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		$act = "added";
	}
	print $act;
}
?>