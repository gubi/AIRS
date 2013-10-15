<?php
/**
* Retrieve username from cookie
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
if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== "" && isset($_COOKIE["iack"]) && trim($_COOKIE["iack"]) !== ""){
	require_once("_blowfish.php");
	
	$decrypted_user = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);
	$GLOBALS["decrypted_user"] = $decrypted_user;
} else {
	$GLOBALS["decrypted_user"] = "System";
}
?>