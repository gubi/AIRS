<?php
/**
* Generate a json of connection history for main page graph
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
* @package	AIRS_manage_users
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");

$k = -1;
if(isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	$pdsl = db_connect("system_logs");
	$user_data = $pdsl->query("select * from `airs_login` where `user` = '" . addslashes($_GET["user"]) . "'");
	if($user_data->rowCount() > 0){
		while ($dato_user_log = $user_data->fetch()){
			$k++;
			$a = strptime($dato_user_log["data"], '%Y-%m-%d');
			$timestamp = mktime(0, 0, 0, $a['tm_mon']+1, $a['tm_mday'], $a['tm_year']+1900);
			$user_history[$k][0] = $timestamp . "000";
				if($dato_user_log["action"] == "login"){
					$login_one_more = date("G:i:s", strtotime("+30 minutes", strtotime($dato_user_log["ora"])));
				}
				if($dato_user_log["action"] == "logout"){
					$login_one_minus = date("G:i:s", strtotime("-30 minutes", strtotime($dato_user_log["ora"])));
				}
				
			$user_history[$k][1] = ($dato_user_log["action"] == "login") ? date("G.i", strtotime($dato_user_log["ora"])) : date("G.i", strtotime($login_one_minus));
			$user_history[$k][2] = ($dato_user_log["action"] == "logout") ? date("G.i", strtotime($dato_user_log["ora"])) : date("G.i", strtotime($login_one_more));
		}
	}
} else {
	$user_history = null;
}
if($_GET["debug"] == "true"){
	print_r($user_history);
}
$json = str_replace(array(",\"", "\", ", "[\"", "\"]"), array(", ", ", ", "[", "]"), json_encode($user_history));
?>