<?php
/**
* Generates top menu
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

$pdo = db_connect("");
if (isset($_COOKIE["iac"]) && trim($_COOKIE["iac"]) !== ""){
	$_top_menu = $pdo->query("select `id` from `airs_components` where `name` = '" . addslashes($GLOBALS["page_m"]) . "'");
	if ($_top_menu->rowCount() > 0){
		if($show_top_menu){
			$display = "block";
		} else {
			$display = "none";
		}
		print "<div id=\"components_top_menu\" style=\"display: " . $display . ";\"><ul>";
			while($dato_top_menu = $_top_menu->fetch()){
				$menu = $pdo->query("select * from `airs_components_functions` where `parent_id` = '" . addslashes($dato_top_menu["id"]) . "'");
				if ($menu->rowCount() > 0){
					while ($dato_menu = $menu->fetch()){
						if (str_replace(" ", "_", $dato_menu["name"]) !== $GLOBALS["page"]){
							print "<li><a href=\"" . $dato_menu["page"] . "\" title=\"" . $dato_menu["description"] . "\">" . $dato_menu["name"] . "</a></li>";
						}
					}
				}
			}
		print "</ul></div>";
	}
}
?>