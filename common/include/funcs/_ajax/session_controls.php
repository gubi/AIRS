<?php
/**
* This script restore sossion of page editing
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
* @SLM_status	testing
*/
header("Content-type: text/plain");

if (isset($_GET["action"]) && trim($_GET["action"]) !== "" && isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	switch ($_GET["action"]){
		case "get":
			print $_SESSION["modifying_by"];
			break;
		case "restore":
			session_destroy();
			session_start();
			session_regenerate_id();
			$_SESSION = array();
			print session_cache_expire();
			break;
		case "delete":
			$_SESSION["modifying_by"] = array();
			break;
	}
}
?>