<?php
/**
* This script provide to redirecting pages
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
function redirect($url){
	$use_sts = true;
	if($use_sts && !isset($_SERVER['HTTPS'])){
		$http = "http://";
	} else {
		$http = "https://";
	}
	$absolute_path = $http . $_SERVER["HTTP_HOST"] . "/";
	/*
	if (isset($_GET["id"])){
		$absolute_path .= $_GET["m"] . "/";
		if (isset($_GET["q"])){
			$absolute_path .= $_GET["id"] . "/";
		}
	}
	*/
	$url = str_replace("./", $absolute_path, $url);
	if (!headers_sent()) {
		header("Location: " . $url);
		exit();
	} else {
		print '<meta http-equiv="refresh" content="0; url="' . $url . '">';
		print '<script language="javascript">location.href = "' . $url . '";</script>';
		exit();
	}
}
function is_function_page($page){
	$pdo = $GLOBALS["pdo"];
	$content = $pdo->prepare("select * from `airs_menu` where `name` = '" . addslashes($page) . "' and `is_function_page` = '1'");
	if (!$content->execute()){
		return false;
	} else {
		return $content->rowCount();
	}
}

if (isset($_POST["cerca"])){
	if (trim($_POST["cerca"]) !== '') {
		header("Location: " . $absolute_path . $i18n["search_string"] . ":" . urlencode(str_replace(" ", "_", str_replace("&", "(e)", str_replace("?", "", $_POST["cerca"])))));
	} else {
		header("Location: " . $absolute_path . $i18n["search_string"] . ":");
	}
}

if (trim($this_page) == $i18n["page_name_main"]){
	header("Location: ./");
	exit();
}
if (trim($this_page) == $i18n["menu_discussion"] . ":"){
	header("Location: " . $i18n["menu_discussion"] . ":" . $i18n["page_name_main"]);
	exit();
}
if (trim($this_page) == $i18n["menu_edit"] . ":"){
	header("Location:" . $i18n["menu_edit"] . ":" . $i18n["page_name_main"]);
	exit();
}
if (trim($this_page) == $i18n["page_name_chronology"] . ":"){
	header("Location:" . $i18n["page_name_chronology"] . ":" . $i18n["page_name_main"]);
	exit();
}
if ($GLOBALS["page"] == $i18n["page_name_main"]){
	$content = $pdo->query("select * from `airs_content` where `id` = '1'");
} else {
	$content = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "'");
}

$use_sts = true;
if($use_sts && !isset($_SERVER['HTTPS'])){
	$http = "http://";
} else {
	$http = "https://";
}
if ($content->rowCount() == 0){
	// Se c'è la funzione sulla pagina
	if(!$GLOBALS["is_functioned"]){
		if ($GLOBALS["user_level"] > 0){
			if (!is_function_page($GLOBALS["page_title"])){
				if (is_numeric($GLOBALS["page_title"])){
					$query = "select * from `airs_content` where `" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}'";
				} else {
					$query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . $GLOBALS["page_q"] . "'";
				}
				$check_content = $pdo->query($query);
				if ($check_content->rowCount() == 0){
					if (trim($GLOBALS["page_last_level"]) !== ""){
						$parent_content = $pdo->query("select * from `airs_content` where `" . $GLOBALS["page_last_level"] . "` = '" . addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) . "' and `" . $GLOBALS["page_level"] . "` = '" . addslashes($_GET[$GLOBALS["page_level_key"]]) . "'");
						if ($parent_content->rowCount() > 0){
							while($dato_parent_content = $parent_content->fetch()){
								$GLOBALS["next_is_var"] = $dato_parent_content["next_is_var"];
								$GLOBALS["page_html_title"] = $dato_parent_content["title"];
								if ($dato_parent_content["next_is_var"] == 0){
									redirect($http . $_SERVER["HTTP_HOST"] . "/" . str_replace($GLOBALS["page"], $i18n["menu_edit"] . ":" . $GLOBALS["page"], $GLOBALS["current_pos"]));
								}
							}
						} else {
							if (!is_numeric($GLOBALS["page"])){
								if($GLOBALS["page_m"] == $i18n["user_string"] && strlen($GLOBALS["page_id"]) > 0 && $GLOBALS["page_id"] !== $i18n["page_name_personal_settings"]){
									if(strtolower($decrypted_user) == strtolower($GLOBALS["page"])){
										redirect($http . $_SERVER["HTTP_HOST"] . "/" . str_replace("//", "/", str_replace($GLOBALS["page"], $i18n["menu_edit"] . ":" . $GLOBALS["page"], $GLOBALS["current_pos"])));
									}
								} else {
									if($GLOBALS["page_m"] !== "Meeting"){
										if($GLOBALS["page"] !== $_SERVER["HTTP_HOST"]){
											redirect($http . $_SERVER["HTTP_HOST"] . "/" . str_replace($GLOBALS["page"], $i18n["menu_edit"] . ":" . $GLOBALS["page"], $GLOBALS["current_pos"]));
										} else {
											redirect($http . $_SERVER["HTTP_HOST"] . "/");
										}
									}
								}
							}
						}
					} else {
						redirect($http . $_SERVER["HTTP_HOST"] . "/Modifica:" . $GLOBALS["page"]);
						exit();
					}
				}
			}
		}
	}
}
?>