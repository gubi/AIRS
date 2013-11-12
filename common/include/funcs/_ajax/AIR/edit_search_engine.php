<?php
/**
* Edit a search engine from AIR database
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
header("Content-type: text/plain;charset=utf-8");
require_once("../../../.mysql_connect.inc.php");

if (isset($_POST) || isset($_GET)){
	require_once("../../_converti_data.php");
	require_once("../../calculate_tags.php");
	$pdo = db_connect("air");
	
	$params = array("se_search_var", "se_lang_var", "se_site_var", "se_filetype_var", "se_lastdate_var");
	foreach ($params as $k => $v){
		if (trim($_POST[$k]) !== ""){
			if (substr(trim($_POST[$k]), -1, 1) !== "="){
				$_POST[$k] .= "=";
			}
			$_POST[$k] = str_replace("==", "=", $_POST[$k]);
		}
	}
	if (trim($_POST["se_quoting_var"]) !== ""){
		$_POST["se_quoting_var"] = $_POST["se_quoting_var"] . "{1}" . $_POST["se_quoting_var"];
	}
	$se_search_var = $_POST["se_search_var"] . "=";
	
	$edit_se = $pdo->prepare("update `air_search_engines` set `name` = ('" . addslashes($_POST["se_title"]) . "'), `description` = ('" . addslashes($_POST["se_description"]) . "'), `search_page` = ('" . addslashes($_POST["se_uri"]) . "'), `search_var` = ('" . addslashes($se_search_var) . "'), `language_var` = ('" . addslashes($_POST["se_lang_var"]) . "'), `site_var` = ('" . addslashes($_POST["se_site_var"]) . "'), `filetype_var` = ('" . addslashes($_POST["se_filetype_var"]) . "'), `last_date_var` = ('" . addslashes($_POST["se_lastdate_var"]) . "'), `last_date_val` = ('" . addslashes($_POST["se_lastdate_val"]) . "'), `country_var` = ('" . addslashes($_POST["se_country_var"]) . "'), `quoting_var` = ('" . addslashes($_POST["se_quoting_var"]) . "'), `or_var` = ('" . addslashes($_POST["se_OR_var"]) . "'), `exclusion_var` = ('" . addslashes($_POST["se_NOT_var"]) . "') where `id` = '" .  addslashes($_POST["se_id"]) . "'");
	if (!$edit_se->execute()) {
		$act = "error:Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
	} else {
		$act = "edited";
	}
	print $act;
}
?>