<?php
/**
* Delete a research from AIR database
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
header("Content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");

if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	$pdo = db_connect("air");
	$remove_research =  $pdo->prepare("delete from `air_research` where `id` = '" . addslashes($_GET["id"]) . "'");
	if (!$remove_research->execute()) {
		print "Si &egrave; verificato un errore durante la rimozione della ricerca:<br />" . $pdo->errorCode();
	} else {
		print "removed";
	}
}
?>