<?php
/**
* Replace main definitions in core scripts
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
switch($origin){
	case "ext":
		$funcs_path = "../..";
		break;
	case "ajax";
		$funcs_path = "../../../../include";
		break;
	default:
		$funcs_path = "common/include";
		break;
}
require_once($funcs_path . "/funcs/_converti_data.php");
require_once($funcs_path . "/funcs/_blowfish.php");
$key = $config["system"]["key"];
$crypted_user_key = $_COOKIE["iack"];
$decrypted_user = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);

$content_body = utf8_decode(str_replace(
				array("{PAGE}",
					 "{PAGE_M}",
					 "{PAGE_ID}",
					 "{PAGE_Q}",
					 "{WIKI_PAGE_TITLE}",
					 "{WIKI_PAGE_SUBTITLE}",
					 "{WIKI_CONTENT}",
					 "{ABSOLUTE_PATH}",
					 "{ABSOLUTE_PATH90}",
					 "{ALLOW_DISCUSSIONS}",
					 "{RESTRICT_TO_LEVEL}",
					 "{RIGHT_PANEL_CHECKED}",
					 "{RIGHT_PANEL_TOC_CHECKED}",
					 "{RIGHT_PANEL_TOCS_CHECKED}",
					 "{DECRYPTED_USER}",
					 "{DEVELOPERS_MAIL}",
					 "{DATE}",
					 "{FORM_DATE}",
					 "{HOUR}"
					),
				array($GLOBALS["page"],
					 $GLOBALS["page_m"],
					 $GLOBALS["page_id"],
					 $GLOBALS["page_q"],
					 $content_title,
					 $content_subtitle,
					 stripslashes($content_wiki),
					 $absolute_path,
					 $absolute_path90,
					 $discussion_checkbox,
					 $level_checkbox,
					 $right_panel,
					 $right_panel_toc,
					 $right_panel_tocs,
					 $decrypted_user,
					 "airs.dev@inran.it",
					 date("d/m/Y"),
					 date("Y-m-d"),
					 date("H:i")
				),
				$content_body));
?>