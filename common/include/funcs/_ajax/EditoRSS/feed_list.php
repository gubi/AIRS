<?php
/**
* List feeds
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
header("Content-type: text/plain; charset=utf-8");
require_once("../../_taglia_stringa.php");
require_once("../../_create_link.php");
require_once("../../../.mysql_connect.inc.php");
$absolute_path = str_replace(array("/web/htdocs/", "/common/include/funcs/_ajax/EditoRSS/feed_list.php", "/home"), array("http://", "", ""), realpath(__FILE__));

if (isset($_GET["user"]) && trim($_GET["user"]) !== ""){
	$pdo = db_connect("editorss");
	
	switch($_GET["type"]){
		case "groups":
			$tables = array("group" => "gruppi");
			break;
		case "tags":
			$tables = array("tags" => "tags");
			break;
		default:
			$tables = array("group" => "Gruppi", "valid_resources" => "Numero di news", "origin" => "Origine", "tags" => "tags");
			break;
	}
	$feeds = array("id" => "feeds", "name" => "Feeds", "data" => array("level" => 0, "name" => "Mappa mentale sui feeds RSS"));
	$feed_g = array();
	
	foreach ($tables as $table => $dec){
		if(!isset($_GET["node"]) || trim($_GET["node"]) == ""){
			$feed_list = $pdo->query("select distinct(`" . addslashes($table) . "`) from `editorss_feeds` where `user` = '" . addslashes($_GET["user"]) . "' order by `" . addslashes($table) . "`");
			if ($feed_list->rowCount() > 0){
				while ($dato_feed = $feed_list->fetch()){
					
					$feed_groups = explode(",", $dato_feed[$table]);
					sort($feed_groups);
					foreach($feed_groups as $feeds_v){
						if ($dec == "Origine"){
							$feeds_v_newsno = $pdo->query("select * from `editorss_origins_type` where `origin` = '" . addslashes($feeds_v) . "'");
							if ($feeds_v_newsno->rowCount() > 0){
								while ($dato_feeds_v_newsno = $feeds_v_newsno->fetch()){
									$feeds_v = ucfirst(strtolower($dato_feeds_v_newsno["name"]));
								}
							}
						}
						$feed_g[$dec][] = $feeds_v;
					}
					$feed_h[strtolower($dec)] = array_unique($feed_g[$dec]);
				}
			}
			$feeds["children"][] = array(
				"id" => strtolower($dec), 
				"name" => ucfirst($dec), 
				"data" => array("level" => 1, "name" => ucfirst($dec)),
				"children" => array()
			);
			$c = 0;
			foreach($feeds["children"] as $k => $f){
				foreach($feed_h as $kk => $ff){
					foreach($ff as $kkk => $fff){
						if ($f["id"] == $kk){
							$c++;
							$feeds["children"][$k]["children"][] = array(
								"id" => strtolower($fff), 
								"name" => ucfirst($fff), 
								"data" => array("level" => 2, "name" => ucfirst($fff)),
								"children" => array()
							);
						}
					}
				}
			}
		} else {
			$database = "editorss_feeds";
			$level = 3;
			
			$feeds_v_newsno = $pdo->query("select * from `editorss_origins_type` where `name` = '" . addslashes($_GET["node"]) . "'");
			if ($feeds_v_newsno->rowCount() > 0){
				while ($dato_feeds_v_newsno = $feeds_v_newsno->fetch()){
					$table = "origin";
					$_GET["node"] = $dato_feeds_v_newsno["origin"];
				}
			} else {
				$feeds_v_newsno = $pdo->query("select * from `editorss_feeds` where `title` = '" . addslashes($_GET["node"]) . "'");
				if ($feeds_v_newsno->rowCount() > 0){
					while ($dato_feeds_v_newsno = $feeds_v_newsno->fetch()){
						$database = "editorss_feeds_news";
						$table = "parent_id";
						$_GET["node"] = $dato_feeds_v_newsno["id"];
						$level = 4;
					}
				}
			}
			$feed_list_data = $pdo->query("select * from `editorss_feeds` order by `valid_resources`");
			if ($feed_list_data->rowCount() > 0){
				while ($dato_feed_data = $feed_list_data->fetch()){
					switch($table){
						case "group";
						case "tags":
							$feed_groups = explode(",", $dato_feed_data[$table]);
							sort($feed_groups);
							foreach($feed_groups as $feeds_v){
								if (strtolower($_GET["node"]) == strtolower($feeds_v)){
									$news_list = $pdo->query("select * from `editorss_feeds` where `id` = '" . addslashes($dato_feed_data["id"]) . "'");
									if ($news_list->rowCount() > 0){
										while ($dato_news = $news_list->fetch()){
											if (trim($dato_news["tags"]) == ""){
												$tag = "<i>nessuno</i>";
											} else {
												$tag = str_replace(",", ", ", $dato_news["tags"]);
											}
											$news_feed[] = array(
															"id" => taglia_stringa(strtolower(stripslashes($dato_news["title"])), 90),
															"name" => taglia_stringa(ucfirst(stripslashes($dato_news["title"])), 90),
															"data" => array(
																		"level" => $level,
																		"name" => ucfirst(stripslashes($dato_news["title"])),
																		"id" => $dato_news["id"],
																		"uri" => $dato_news["link"],
																		"tags" => $tag,
																		"description" => taglia_stringa(create_link(stripslashes(str_replace(array("\n\n", "\t"), array("\n", ""), $dato_news["description"]))), 250)
																	),
															"children" => array()
														);
										}
									}
								}
							}
							break;
						default:
							$news_list = $pdo->query("select * from `" . $database . "` where `" . $table . "` = '" . addslashes($_GET["node"]) . "'");
							if ($news_list->rowCount() > 0){
								while ($dato_news = $news_list->fetch()){
									if (trim($dato_news["tags"]) == ""){
										$tag = "<i>nessuno</i>";
									} else {
										$tag = str_replace(",", ", ", $dato_news["tags"]);
									}
									$news_feed[] = array(
												"id" => taglia_stringa(strtolower(stripslashes($dato_news["title"])), 90),
												"name" => ucfirst(taglia_stringa(strtolower(stripslashes($dato_news["title"])), 90)),
												"data" => array(
															"level" => $level,
															"name" => ucfirst(stripslashes($dato_news["title"])),
															"id" => $dato_news["id"],
															"uri" => $dato_news["link"],
															"tags" => $tag,
															"description" => taglia_stringa(create_link(stripslashes(str_replace(array("\n\n", "\t"), array("\n", ""), $dato_news["description"]))), 250)
														),
												"children" => array()
											);
								}
							}
							break;
					}
				}
			}
			$feeds = array("id" => $_GET["node"], "name" => $_GET["node"], "data" => array(), "children" => $news_feed);
		}
	}
}
print json_encode($feeds);
//print_r($feeds);
?>