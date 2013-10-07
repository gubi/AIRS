<?php
/**
* Check mail and returns smtp host
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
require_once("../email_verify_source.php");

if(!isset($_POST["mail_pop_username"]) || trim($_POST["mail_pop_username"]) == "") {
	$error = true;
	$message = "&Egrave; necessario indicare uno username per collegare il Sistema alla sua posta";
}
if(!isset($_POST["mail_pop_password"]) || trim($_POST["mail_pop_password"]) == "") {
	$error = true;
	$message = "&Egrave; necessario indicare una password per collegare il Sistema alla sua posta";
}
if(!isset($_POST["mail_smtp_username"]) || trim($_POST["mail_smtp_username"]) == "") {
	$error = true;
	$message = "&Egrave; necessario indicare uno username per permettere al Sistema di inviare e-mail";
}
if(!isset($_POST["mail_smtp_password"]) || trim($_POST["mail_smtp_password"]) == "") {
	$error = true;
	$message = "&Egrave; necessario indicare una password per permettere al Sistema di inviare e-mail";
}
if(!$error) {
	// Example from http://www.tienhuis.nl/files/email_verify_example.php
	print validateEmail($_POST["mail_system_address"], true, true, "airs.dev@inran.it", "mail.inran.it");
} else {
	print "error::" . $message;
}
?>