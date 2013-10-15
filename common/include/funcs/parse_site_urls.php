<?php
/**
* Retrieve each url from a site page
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

if (isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	require_once("HTTP/Client.php");
	require_once("HTTP/Request/Listener.php");
	
	$baseurl = $_GET["uri"];
	
	function get_urls($page){
		$base = preg_replace("/\/([^\/]*?)$/", "/", $page);
		
		$client = new HTTP_Client();
		$client -> get($page);
		$resp = $client -> currentResponse();
		$body = $resp["body"];
		
		preg_match_all("/(\<a.*?\>.*?\<\/a\>)/is", $body, $matches);
		
		foreach($matches[0] as $match){
			preg_match("/href=(.*?)[\s|\>]/i", $match, $href);
				preg_match("/\<a.*?\>(.*?)\<\/a\>/i", $match, $href_a);
			if ($href != null){
				$href = $href[1];
				$href = preg_replace("/^\"/", "", $href);
				$href = preg_replace("/\"$/", "", $href);
				if (preg_match("/^mailto:/", $href)){
				} elseif (preg_match("/^http:\/\//", $href)){
					if (isset($_GET["exception"]) && trim($_GET["exception"]) !== ""){
						if (isset($_GET["exception_type"]) && trim($_GET["exception_type"]) !== ""){
							$referringPage = parse_url($href);
							parse_str($referringPage[$_GET["exception_type"]], $queryVars);
							
							if($queryVars[addslashes($_GET["exception"])]){
								//print_r($referringPage);
								//print_r($queryVars);
								//print $href . "\n";
								$out[$href] = $href_a;
							}
						}
					}
				} else {
					$out[$base . $href] = $href_a;
				}
			}
		}
		return $out;
	}
	//print get_urls($baseurl);
	//print_r(get_urls($baseurl));
	$links = array();
	if (count(get_urls($baseurl)) !== 0){
		$f = 0;
		foreach (get_urls($baseurl) as $k => $v){
			$f++;
			foreach ($v as $kk => $vv){
				$links[$f]["name"] = utf8_decode(html_entity_decode(strip_tags($vv)));
				$links[$f]["link"] = $k;
			}
		}
	}
	$links["count"] = count($links);
	$str = json_encode($links);
	print_r($str);
}
?>