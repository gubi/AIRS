<?php
/**
* Generates page menu (top of page contents)
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

if(trim($GLOBALS["allow_edits"]) == ""){
	$GLOBALS["allow_edits"] = 0;
}
if(trim($GLOBALS["allow_discussions"]) == ""){
	$GLOBALS["allow_discussions"] = 0;
}
if(trim($GLOBALS["allow_chronology"]) == ""){
	$GLOBALS["allow_chronology"] = 0;
}
$menu_groups = $pdo->query("select * from `airs_menu_groups` where `visible` = '1' and `position` = 'page' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order`");
if ($menu_groups->rowCount() > 0){
	while ($dato_menu_groups = $menu_groups->fetch()){
		print "<ul title=\"" . $dato_menu_groups["name"] . "\">";
			if (strlen(trim($_GET["m"])) == 0){
				$GLOBALS["page_m"] = "PaginaPrincipale";
			}
			$menu = $pdo->query("select * from `airs_menu` where `visible` = '1' and `position` = 'page' and `group` = '" . addslashes($dato_menu_groups["id"]) . "' and `level` <= '" . $GLOBALS["user_level"] . "' order by `order` asc");
			if ($menu->rowCount() > 0){
				while ($dato_menu = $menu->fetch()){
					if ($GLOBALS["next_is_var"] == 1){
						$allow_menu = $pdo->query("select * from `airs_content` where `" . $GLOBALS["page_last_level"] . "` = '" . addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) . "' and `" . $GLOBALS["page_level"] . "` = ''");
					} else {
						$allow_menu = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
					}
					if ($allow_menu->rowCount() == 0){
						$allow_menu = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}'");
					}
					if ($allow_menu->rowCount() > 0){
						while ($dato_allow_menu = $allow_menu->fetch()){
							if ($GLOBALS["page_m"] == "Utente" && strlen($_GET["id"]) > 0 && $GLOBALS["page_id"] !== "Impostazioni_personali"){
								if(strtolower($decrypted_user) == strtolower($GLOBALS["page"])){
									$GLOBALS["allow_discussions"] = 0;
									$GLOBALS["allow_edits"] = 1;
									$GLOBALS["allow_chronology"] = 1;
									$may_not_allow_menu = array("Discussione", "Modifica", "Cronologia");
								} else {
									$GLOBALS["allow_discussions"] = 0;
									$GLOBALS["allow_edits"] = 0;
									$GLOBALS["allow_chronology"] = 0;
									$may_not_allow_menu = array("Discussione", "Modifica", "Cronologia");
								}
							} else {
								$GLOBALS["allow_discussions"] = $dato_allow_menu["allow_discussions"];
								$GLOBALS["allow_edits"] = $dato_allow_menu["allow_edits"];
								$GLOBALS["allow_chronology"] = $dato_allow_menu["allow_chronology"];
								$may_not_allow_menu = array("Discussione", "Modifica", "Cronologia");
							}
						}
					} else {
						if ($GLOBALS["page_m"] == "Utente"){
							$GLOBALS["allow_discussions"] = 0;
							$GLOBALS["allow_chronology"] = 0;
							$may_not_allow_menu = array("Discussione", "Cronologia");
							if (strtolower($decrypted_user) !== strtolower($GLOBALS["page"])){
								$GLOBALS["allow_edits"] = 0;
								$may_not_allow_menu[] = "Modifica";
							}
						} else {
							$may_not_allow_menu = array();
						}
					}
					if ($dato_menu["has_link"] == 1){
						$page_menu_link = "";
						if (strlen(trim($GLOBALS["page_m"])) > 0 && $GLOBALS["page"] !== $GLOBALS["page_m"]){
							$page_menu_link .= $GLOBALS["page_m"];
						}
						if (strlen(trim($GLOBALS["page_id"])) > 0 && $GLOBALS["page"] !== $GLOBALS["page_id"] && $GLOBALS["page_m"] !== "Tags"){
							if (strlen(urldecode($GLOBALS["current_pos"])) > 0){
								$page_menu_link .= "/" . str_replace("{1}", ucfirst($decrypted_user), $GLOBALS["page_id"]);
							} else {
								$page_menu_link .= "/";
							}
						}
						if (strlen(trim($GLOBALS["page_q"])) > 0 && $GLOBALS["page"] !== $GLOBALS["page_q"]){
							$page_menu_link .= "/" . $GLOBALS["page_q"];
						}
						if (strlen(urldecode($GLOBALS["current_pos"])) > 0){
							$page_menu_link .= "/" . str_replace($GLOBALS["page"], str_replace("{PAGE}", $GLOBALS["page"], $dato_menu["link"]), urldecode($GLOBALS["page"]));
						} else {
							$page_menu_link .= "/" . str_replace("{PAGE}", $GLOBALS["page"], $dato_menu["link"]);
						}
					} else {
						$page_menu_link = get_link($dato_menu["name"]);
					}
					$page_menu_link = str_replace("//", "/", $page_menu_link);
					
					if ($dato_menu["name"] == trim($GLOBALS["function_part"]) || trim($GLOBALS["function_part"]) == "" && $dato_menu["name"] == "Voce"){
						print "<li class=\"selected\"><a href=\"javascript: void(0);\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . "</a></li>";
					} else {
						if (in_array($dato_menu["name"], $may_not_allow_menu)){
							if ($dato_menu["name"] == "Discussione" && $GLOBALS["allow_discussions"] == 1 || $dato_menu["name"] == "Modifica" && $GLOBALS["allow_edits"] == 1 || $dato_menu["name"] == "Cronologia" && $GLOBALS["allow_chronology"]){
								if ($dato_menu["name"] == "Discussione"){
									$discussions = $pdo->query("select * from `airs_discussions` where `name` = '" . $GLOBALS["page"] . "'");
									if ($discussions->rowCount() > 0){
										$dcount = " <span style=\"font-size: 0.9em;\">(" . $discussions->rowCount() . ")</span>";
									} else {
										$dcount = "";
									}
								} else {
									$dcount = "";
								}
								print "<li><a href=\"" . $page_menu_link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . $dcount . "</a></li>";
							}
						} else {
							print "<li><a href=\"" . $page_menu_link . "\" title=\"" . $dato_menu["title"] . "\" rel=\"" . $dato_menu["rel"] . "\">" . $dato_menu["name"] . "</a></li>";
							if ($GLOBALS["page_m"] == "Utente" && isset($_COOKIE["iac"])){
								print "<li><a href=\"./Utente/Impostazioni_personali\" title=\"Vai alle impostazioni personali\" rel=\"nofollow\">Impostazioni personali</a></li>";
							}
						}
					}
				}
			}
		print "</ul>";
	}
}
?>