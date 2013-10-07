<?php
/**
* Returns calls in page discussions
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
header("Content-type: text/plain");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["term"]) && trim($_GET["term"]) !== ""){
	$pdo = db_connect("");
	$select_user = $pdo->query("select * from `airs_users` where `username` like '%" . addslashes($_GET["term"]) . "%' or `name` like '%" . addslashes($_GET["term"]) . "%' or `lastname` like '%" . addslashes($_GET["term"]) . "%'");
	if ($select_user->rowCount() > 0){
		while ($dato_select_user = $select_user->fetch()){
			$call[] = ucfirst(trim($dato_select_user["username"]));
		}
	}
	if (count($call) > 0){
		$call = array_slice($call, 0, 3);
		$call = array_unique($call);
		sort($call);
		print json_encode($call);
	}
}
?>