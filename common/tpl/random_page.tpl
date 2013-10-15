<?php
/**
* Redirect to random page
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

function rand_page(){
	$pdo = $GLOBALS["pdo"];
	$content = $pdo->query("select * from airs_content where `restrict_to_level` <= '" . $GLOBALS["user_level"] . "' and `rand_show` = '1' order by rand() limit 1");
	if ($content->rowCount() > 0){
		while ($dato_content = $content->fetch()){
			$rand_page = $dato_content["name"];
			if (strlen($dato_content["subname"]) > 0){
				$rand_page .= "/" . $dato_content["subname"];
				if (strlen($dato_content["sub_subname"]) > 0){
					$rand_page .= "/" . $dato_content["sub_subname"];
				}
			}
		}
	}
	if (strlen($rand_page) == 0){
		rand_page();
	} else {
		return $rand_page;
	}
}
header("Location: " . rand_page());
?>