<?php
/**
* Generates subpages tree in right panel
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

require_once("common/include/funcs/explode_tree.php");

$pdo = db_connect("");
$_top_menu = $pdo->query("select `id` from `airs_components` where `page` = '" . addslashes($GLOBALS["page_m"]) . "'");
if ($_top_menu->rowCount() > 0){
	print "<ul>";
		while($dato_top_menu = $_top_menu->fetch()){
			$menu = $pdo->query("select * from `airs_components_functions` where `parent_id` = '" . addslashes($dato_top_menu["id"]) . "'");
			if ($menu->rowCount() > 0){
				while ($dato_menu = $menu->fetch()){
					if (str_replace(" ", "_", $dato_menu["name"]) !== $GLOBALS["page"]){
						$menu_link = "<a href=\"" . $dato_menu["page"] . "\" title=\"" . $dato_menu["description"] . "\">" . $dato_menu["name"] . "</a>";
					}
					if ($dato_menu["is_subfunction"] == 0){
						print "<li>" . $menu_link . "</li>";
					} else {
						print "<li><ul><li>" . $menu_link . "</li></ul></li>";
					}
				}
			}
		}
	print "</ul>";
} else {
	$subpage = array();
	$query = "select * from `airs_content` where `" . (addslashes($GLOBALS["page_last_level"]) ? addslashes($GLOBALS["page_last_level"]) : addslashes($GLOBALS["page_level"])) . "` = '" . (addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) ? addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) : addslashes($GLOBALS["page_m"])) . "' and `" . (addslashes($GLOBALS["page_last_level"]) ? addslashes($GLOBALS["page_level"]) : addslashes($GLOBALS["page_next_level"])) . "` != '{1}' and `" . (addslashes($GLOBALS["page_last_level"]) ? addslashes($GLOBALS["page_level"]) : addslashes($GLOBALS["page_next_level"])) . "` != '" . (addslashes($GLOBALS["page_last_level"]) ? addslashes($GLOBALS["page"]) : addslashes($GLOBALS[$GLOBALS["page_last_level_type"]])) . "' order by `" . (addslashes($GLOBALS["page_last_level"]) ? addslashes($GLOBALS["page_level"]) : addslashes($GLOBALS["page_next_level"])) . "` asc, `" . addslashes($GLOBALS["page_next_level"]) . "` asc";
	//print $query . "<br />";
	$subcontent = $pdo->query($query);
	if ($subcontent->rowCount() > 0){
		while ($dato_subcontent = $subcontent->fetch()){
			$subpage[$dato_subcontent["name"] . "/" . $dato_subcontent["subname"] . "/" . $dato_subcontent["sub_subname"]] = $dato_subcontent["name"] . "/" . $dato_subcontent["subname"] . "/" . $dato_subcontent["sub_subname"];
		}
	}
}
//print trim($GLOBALS["page_id"]) . "<br />";
//print ($GLOBALS[$GLOBALS["page_last_level_type"]] ? $GLOBALS[$GLOBALS["page_last_level_type"]] : $GLOBALS["page_m"]);
//print_r($subpage);

$tree = explodeTree($subpage, "/");
//print_r($tree);

$k = 0;
if(is_array($tree)){
	foreach ($tree as $position => $name_arr){
		$k++;
		$tree_menu .= "<ul>";
		foreach ($name_arr as $name => $link){
			if (!is_array($link)){
				$tree_menu .= "<li><a href=\"" . $link . "\">" . str_replace("_", " ", $name) . "</a></li>";
			} else {
				$tree_menu .= "<li><a href=\"" . $name . "\">" . str_replace("_", " ", $name) . "</a></li>";
				
				$tree_menu .= "<ul>";
				foreach ($link as $sub_name => $sub_link){
					if (!is_array($sub_link)){
						$tree_menu .= "<li><a href=\"" . $sub_link . "\">" . str_replace("_", " ", $sub_name) . "</a></li>";
					} else {
						$tree_menu .= "<li><a href=\"" . $sub_name . "\">" . str_replace("_", " ", $sub_name) . "</a></li>";
						
						$tree_menu .= "<ul>";
						foreach ($sub_link as $sub_subname => $sub_sublink){
							if (!is_array($sub_sublink)){
								$tree_menu .= "<li><a href=\"" . $sub_sublink . "\">" . str_replace("_", " ", $sub_subname) . "</a></li>";
							} else {
								$tree_menu .= "<li><a href=\"" . $sub_subname . "\">" . str_replace("_", " ", $sub_subname) . "</a></li>";
							}
						}
						$tree_menu .= "</ul>";
					}
				}
				$tree_menu .= "</ul>";
			}
		}
		$tree_menu .= "</ul>";
		if ($k == count($menu)){
			break;
		} else {
			$tree_menu .= "</ul>";
		}
	}
	print $tree_menu;
}
?>