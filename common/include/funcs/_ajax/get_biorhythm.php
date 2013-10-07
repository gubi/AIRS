<?php
/**
* Returns biorhythms from date
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	Plugin
* @package	Biorhythm
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
if (isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	require_once("../../.mysql_connect.inc.php");
	require_once("../../classes/class.biorhythm.php");
	
	$pdo = db_connect("");
	$select_user_birth = $pdo->query("select `birth` from `airs_users` where `username` = '" . addslashes($_GET["user"]) . "'");
	if ($select_user_birth->rowCount() > 0){
		while ($dato_select_user_birth = $select_user_birth->fetch()){
			if($dato_select_user_birth["birth"] !== "0000-00-00"){
				$bio = new Biorhythm();
				$bio->set_date($dato_select_user_birth["birth"]);
				if (isset($_GET["thumb"]) && trim($_GET["thumb"]) !== ""){
					$bio->set_image(322, 100, 15);
				} else {
					$bio->set_image(1200, 450, 30);
				}
				$bio->generate_biorhythm();
			}
		}
	}
}
?>