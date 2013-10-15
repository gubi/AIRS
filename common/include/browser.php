<?php
/**
* AIRS System browser which crawl web pages in search of contents
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

require_once(".mysql_connect.inc.php");

function browse($uri, $decrypted_user, $options = "", $timeout = true){
	// Estrae uno User Agent casuale dal database
	$pdo = db_connect("");
	$uas = $pdo->query("select `string` from `UAS` order by rand() limit 0,1");
	if ($uas->rowCount() > 0){
		while ($dato_uas = $uas->fetch()){
			$user_agent = $dato_uas["string"];
		}
	}
	$cookie = tempnam("/tmp", "airs_browse_cookie_");
	$ch = curl_init();
	
	curl_setopt($ch, CURLOPT_URL, $uri);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);
	if($timeout == true){
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	}
	curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
	curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
	curl_setopt($ch, CURLOPT_PROXY, "http://127.0.0.1:8118");
	$html = curl_exec($ch);
	$html = mb_convert_encoding($html, mb_detect_encoding($html));
	if($options == "header_code"){
		$info = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	} else {
		$info = curl_getinfo($ch);
	}
	curl_close($ch);
	
	$logpdo = db_connect("system_logs");
	$add_se = $logpdo->prepare("insert into `airs_browser_scraping` (`uri`, `user`, `data`, `ora`) values(?, ?, ?, ?)");
	$add_se->bindParam(1, addslashes($uri));
	$add_se->bindParam(2, addslashes($decrypted_user));
	$add_se->bindParam(3, date("Y-m-d"));
	$add_se->bindParam(4, date("H:i:s"));
	if (!$add_se->execute()) {
		return "Si è verificato un errore durante il salvataggio:\n" . $pdo->errorCode();
	} else {
		switch($options){
			case "header":
			case "header_code":
				return $info;
				break;
			case "mixed":
				return array($info, utf8_decode($html));
				break;
			default:
				return utf8_decode($html);
				break;
		}
	}
}
?>