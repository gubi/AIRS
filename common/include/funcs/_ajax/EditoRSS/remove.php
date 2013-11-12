<?php
/**
* Remove feeds or news from EditoRSS Database
* 
* PHP versions 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");

if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	if (isset($_GET["type"])){
		$pdo_e = db_connect("editorss");
		$pdo = db_connect("");
		switch (trim($_GET["type"])){
			case "feed":
				$check_feed = $pdo_e->query("select * from `editorss_feeds` where `id` = '" . addslashes($_GET["id"]) . "'");
				if ($check_feed->rowCount() > 0){
					while ($dato_check_feed = $check_feed->fetch()) {
						$check_automation = $pdo->query("select * from `airs_automation` where `user` = '" . addslashes($dato_check_feed["user"]) . "'");
						if ($check_automation->rowCount() > 0){
							while ($dato_check_automation = $check_automation->fetch()) {
								if (strstr($dato_check_automation["action"], $dato_check_feed["uri"])){
									$remove_parent_news =  $pdo->prepare("delete from `airs_automation` where `id` = '" . addslashes($dato_check_automation["id"]) . "'");
									if (!$remove_parent_news->execute()) {
										$act = "Si &egrave; verificato un errore durante la rimozione dell'automazione:<br />" . $pdo->errorCode();
									} else {
										$remove_feed =  $pdo_e->prepare("delete from `editorss_feeds` where `id` = '" . addslashes($_GET["id"]) . "'");
										if (!$remove_feed->execute()) {
											$act = "Si &egrave; verificato un errore durante la rimozione del feed:<br />" . $pdo->errorCode();
										} else {
											$remove_parent_news =  $pdo_e->prepare("delete from `editorss_feeds_news` where `parent_id` = '" . addslashes($_GET["id"]) . "'");
											if (!$remove_parent_news->execute()) {
												$act = "Si &egrave; verificato un errore durante la rimozione delle news:<br />" . $pdo->errorCode();
											} else {
												$act = "ok";
											}
										}
									}
								}
							}
						} else {
							$remove_feed =  $pdo_e->prepare("delete from `editorss_feeds` where `id` = '" . addslashes($_GET["id"]) . "'");
							if (!$remove_feed->execute()) {
								$act = "Si &egrave; verificato un errore durante la rimozione del feed:<br />" . $pdo->errorCode();
							} else {
								$remove_parent_news =  $pdo->prepare("delete from `editorss_feeds_news` where `parent_id` = '" . addslashes($_GET["id"]) . "'");
								if (!$remove_parent_news->execute()) {
									$act = "Si &egrave; verificato un errore durante la rimozione delle news:<br />" . $pdo->errorCode();
								} else {
									$act = "ok";
								}
							}
						}
					}
				}
				break;
			case "news":
				$check_feed_news = $pdo_e->query("select * from `editorss_feeds_news` where `id` = '" . addslashes($_GET["id"]) . "'");
				if ($check_feed_news->rowCount() > 0){
					while ($dato_check_feed_news = $check_feed_news->fetch()) {
						$check_feed = $pdo_e->query("select * from `editorss_feeds` where `id` = '" . addslashes($dato_check_feed_news["parent_id"]) . "'");
						if ($check_feed->rowCount() > 0){
							while ($dato_check_feed = $check_feed->fetch()) {
								$check_automation = $pdo->query("select * from `airs_automation` where `user` = '" . addslashes($dato_check_feed["user"]) . "'");
								if ($check_automation->rowCount() > 0){
									while ($dato_check_automation = $check_automation->fetch()) {
										if (strstr($dato_check_automation["action"], $dato_check_feed["uri"])){
											$remove_parent_news =  $pdo->prepare("delete from `airs_automation` where `id` = '" . addslashes($dato_check_automation["id"]) . "'");
											if (!$remove_parent_news->execute()) {
												$act = "Si &egrave; verificato un errore durante la rimozione dell'automazione:<br />" . $pdo->errorCode();
											}
										}
										$remove_feed =  $pdo_e->prepare("delete from `editorss_feeds_news` where `id` = '" . addslashes($_GET["id"]) . "'");
										if (!$remove_feed->execute()) {
											$act = "Si &egrave; verificato un errore durante la rimozione del feed:<br />" . $pdo->errorCode();
										} else {
											$remove_parent_news =  $pdo_e->prepare("delete from `editorss_feeds_news` where `parent_id` = '" . addslashes($_GET["id"]) . "'");
											if (!$remove_parent_news->execute()) {
												$act = "Si &egrave; verificato un errore durante la rimozione delle news:<br />" . $pdo->errorCode();
											} else {
												$act = "ok";
											}
										}
									}
								} else {
									$remove_feed =  $pdo_e->prepare("delete from `editorss_feeds_news` where `id` = '" . addslashes($_GET["id"]) . "'");
									if (!$remove_feed->execute()) {
										$act = "Si &egrave; verificato un errore durante la rimozione del feed:<br />" . $pdo->errorCode();
									} else {
										$act = "ok";
									}
								}
							}
						}
					}
				}
				break;
			default:
				$act = "no type";
				break;
		}
	} else {
		$act ="no action";
	}
	print $act;
}
?>