<?php
/**
* This function connect to System database
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

function db_connect($table = ""){
	$config = parse_ini_file("conf/airs.conf", 1);
	switch(strtolower($table)){
		case "editorss":
			$dbname = "editorss";
			break;
		case "air":
			$dbname = "air";
			break;
		case "decas":
			$dbname = "decas";
			break;
		case "system_logs":
			$dbname = "system_logs";
			break;
		case "i18n":
			$dbname = "system_i18n";
			break;
		case "meetings":
			$dbname = "meetings";
			break;
		case "slm":
			$dbname = "system_living_module";
			break;
		case "airs":
		case "main":
		default:
			$dbname = ".airs";
			break;
	}
	$pdo = new PDO("mysql:host=" . $config["database"]["host"] . ";dbname=" . $dbname, $config["database"]["username"], $config["database"]["password"], array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
	return $pdo;
}
?>