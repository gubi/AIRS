<?php
/**
* Generates Wiki menu
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

$i = 0;
$menu_groups = $pdo->query("select * from `airs_menu_groups` where `visible` = '1' and `position` = '" . $menu_position . "' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order`");
if ($menu_groups->rowCount() > 0){
	while ($dato_menu_groups = $menu_groups->fetch()){
		$i++;
		print "<ul>";
			$menu = $pdo->query("select * from `airs_menu` where `visible` = '1' and `position` = '" . $menu_position . "' and `group` = '" . addslashes($dato_menu_groups["id"]) . "' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order` asc");
			if ($menu->rowCount() > 0){
				while ($dato_menu = $menu->fetch()){
					if ($dato_menu["has_link"] == 1){
						$link = $dato_menu["link"];
					} else {
						$link = get_link($dato_menu["name"]);
					}
					$discussion = $pdo->query("select count(`id`) as `total_discussion` from `airs_discussions` where `name` = '" . addslashes($dato_menu["name"]) . "'");
					if ($discussion->rowCount() > 0){
						while($dato_discussion = $discussion->fetch()){
							if ($dato_discussion["total_discussion"] > 0){
								$discussion_symbol = "<tt class=\"heart\"><sup>" . $dato_discussion["total_discussion"] . "</sup></tt>";
							} else {
								$discussion_symbol = "";
							}
						}
					}
					if ($link == trim($GLOBALS["page"])){
						if ($dato_menu["rel"] !== "nofollow" && $dato_menu["rel"] !== "next"){
							print "<li>" . $dato_menu["name"] . "</li>";
						} else {
							print "<li><a href=\"" . $link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . $discussion_symbol . "</a></li>";
						}
					} else {
						print "<li><a href=\"" . $link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . $discussion_symbol . "</a></li>";
					}
				}
			}
			if ($dato_menu_groups["position"] == "left" && $i == 1){
				if ($GLOBALS["page_m"] !== $i18n["menu_guide"]){
					$find_existing = $pdo->query("select * from `airs_content` where `name` = 'Guide' and `subname` = '" . addslashes($GLOBALS["page_m"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_id"]) . "'");
					if ($find_existing->rowCount() > 0){
						if(strlen($GLOBALS["page_m"]) == 0){
							$guidelink = "";
						} else {
							//$guidelink = "/" . str_replace("/" . $GLOBALS["page_q"], "", $GLOBALS["current_pos"]);
							$guidelink = "/" . $GLOBALS["current_pos"];
						}
						$guide_title = $i18n["menu_guide_titile"];
						$guide_text = $i18n["menu_guide"] . " <tt class=\"heart\">&hearts;</tt>";
					} else {
						$guidelink = "/" . str_replace($GLOBALS["page_q"], "", str_replace($GLOBALS["page_id"], $i18n["menu_edit"] . ":" . $GLOBALS["page_id"], $GLOBALS["current_pos"]));
						$guide_title = $i18n["menu_guide_titile_unexisting"];;
						$guide_text = "<span style=\"color: #767676;\">Guida</span>";
					}
					print "<li><a href=\"./Guide" . $guidelink . "\" title=\"" . $guide_title . "\">" . $guide_text . "</a></li>";
				} else {
					print "<li><span style=\"color: #999; font-weight: bold;\">" . $i18n["menu_guide"] . "</span></li>";
				}
			}
		print "</ul>";
	}
}
?>