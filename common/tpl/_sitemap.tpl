<?php
/**
* Generates sitemap page
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

function explodeTree($array, $delimiter = '_', $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE	 = '/' . preg_quote($delimiter, '/') . '/';
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts	= preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart = array_pop($parts);
 
		// Build parent structure
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
				$parentArr[$part] = array();
			} elseif (!is_array($parentArr[$part])) {
				if ($baseval) {
					$parentArr[$part] = array('__base_val' => $parentArr[$part]);
				} else {
					$parentArr[$part] = array();
				}
			}
			$parentArr = &$parentArr[$part];
		}
 
		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			$parentArr[$leafPart] = $val;
		} elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]['__base_val'] = $val;
		}
	}
	return $returnArr;
}
$allow_edits = 0;
$allow_discussions = 0;
$allow_chronology = 0;

$menu = array();
$sitemap = $pdo->query("select * from `airs_menu` where `level` <= '" . addslashes($GLOBALS["user_level"]) . "' and `is_function_page` = '0' and `name` != 'Voce' order by `order`");
if ($sitemap->rowCount() > 0){
	while ($dato_sitemap = $sitemap->fetch()){
		if ($dato_sitemap["name"] == "Pagina principale"){
			$the_link = "PaginaPrincipale";
		} else {
			$the_link = $dato_sitemap["name"];
		}
		$menu[$dato_sitemap["position"] . "/" . $the_link] = $the_link;
		// Ricerca i links alle pagine di secondo livello
		$subcontent = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($dato_sitemap["name"]) . "' and `subname` != '{1}' and `sub_subname` != '{1}'");
		if ($subcontent->rowCount() > 0){
			while ($dato_subcontent = $subcontent->fetch()){
				if (trim($dato_subcontent["subname"]) !== ""){
					$menu[$dato_sitemap["position"] . "/" . $the_link . "/" . $dato_subcontent["subname"]] = $the_link . "/" . $dato_subcontent["subname"];
					
					if (trim($dato_subcontent["sub_subname"]) !== ""){
						$menu[$dato_sitemap["position"] . "/" . $the_link . "/" . $dato_subcontent["subname"] . "/" . $dato_subcontent["sub_subname"]] = $the_link . "/" . $dato_subcontent["subname"] . "/" . $dato_subcontent["sub_subname"];
					}
				}
			}
		}
	}
}
$tree = explodeTree($menu, "/");

$k = 0;
foreach ($tree as $position => $name_arr){
	$k++;
	$content_body .= "<ul>";
	foreach ($name_arr as $name => $link){
		if (!is_array($link)){
			$content_body .= "<li><a href=\"" . $link . "\">" . str_replace("_", " ", $name) . "</a></li>";
		} else {
			$content_body .= "<li><a href=\"" . $name . "\">" . str_replace("_", " ", $name) . "</a></li>";
			
			$content_body .= "<ul>";
			foreach ($link as $sub_name => $sub_link){
				if (!is_array($sub_link)){
					$content_body .= "<li><a href=\"" . $sub_link . "\">" . str_replace("_", " ", $sub_name) . "</a></li>";
				} else {
					$content_body .= "<li><a href=\"" . $sub_name . "\">" . str_replace("_", " ", $sub_name) . "</a></li>";
					
					$content_body .= "<ul>";
					foreach ($sub_link as $sub_subname => $sub_sublink){
						if (!is_array($sub_sublink)){
							$content_body .= "<li><a href=\"" . $sub_sublink . "\">" . str_replace("_", " ", $sub_subname) . "</a></li>";
						} else {
							$content_body .= "<li><a href=\"" . $sub_subname . "\">" . str_replace("_", " ", $sub_subname) . "</a></li>";
						}
					}
					$content_body .= "</ul>";
				}
			}
			$content_body .= "</ul>";
		}
	}
	$content_body .= "</ul>";
	if ($k == count($menu)){
		break;
	} else {
		$content_body .= "</ul>";
	}
}
?>