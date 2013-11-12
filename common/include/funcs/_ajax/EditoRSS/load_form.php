<?php
/**
* Load GUI form for feed addiction
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
if (isset($_GET["type"]) && trim($_GET["type"]) !==""){
	switch ($_GET["type"]){
		case "single":
			require_once("../../../../tpl/EditoRSS/_ajax/form_single_feed.tpl");
			break;
		case "feeds_page":
			require_once("../../../../tpl/EditoRSS/_ajax/form_page_feed.tpl");
			break;
		case "file":
			require_once("../../../../tpl/EditoRSS/_ajax/form_file_feed.tpl");
			break;
	}
}
?>