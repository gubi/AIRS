<?php
/**
* This script generates contents of wiki pages
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
require_once("classes/class.error_template.php");
$message = new Message();
$message->set_i18n($i18n);

if ($GLOBALS["function_part"] == $i18n["search_string"]){
	$GLOBALS["page_m"] = $GLOBALS["function_part"];
}
if ($GLOBALS["page"] == $i18n["page_name_main"]){
	$content_query = "select * from `airs_content` where `id` = '1' and `visible` = '1'";
} else {
	if ($GLOBALS["next_is_var"] == 1){
		$content_query = "select * from `airs_content` where `" . $GLOBALS["page_last_level"] . "` = '" . addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) . "' and `restrict_to_level` <= '" . $GLOBALS["user_level"] . "' and `visible` = '1'";
	} else {
		if (!is_numeric($GLOBALS["page_q"])){
			$content_query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_q"]) . "' and `restrict_to_level` <= '" . $GLOBALS["user_level"] . "' and `visible` = '1'";
		} else {
			$content_query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '{1}' and `restrict_to_level` <= '" . $GLOBALS["user_level"] . "' and `visible` = '1'";
		}
	}
}
$content = $pdo->query($content_query);
if ($content->rowCount() == 0){
	$content_query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . addslashes($GLOBALS["page_title"]) . "' or `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `" . $GLOBALS["page_level"] . "` = '{1}'";
}
$content = $pdo->query($content_query);
//print $content_query . "\n";
if ($content->rowCount() > 0){
	// TOCS
	$subcontent = $pdo->query("select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `visible` = '1'");
	if ($subcontent->rowCount() > 0){
		while ($dato_subcontent = $subcontent->fetch()){
			$pages[] = array($dato_subcontent["subname"] => $dato_subcontent["sub_subname"]);
		}
		array_unique($pages);
		foreach($pages as $k => $v){
			foreach($v as $subname => $sub_subname){
				if (strlen($sub_subname) == 0){
					if ($subname != $GLOBALS["page"]){
						$toks[] = "<h1>" .  utf8_decode(str_replace("_", " ", utf8_encode($subname))) . "</h1>";
					}
				} else {
					$toks[] = "<h2>" .  utf8_decode(str_replace("_", " ", utf8_encode($sub_subname))) . "</h2>";
				}
			}
		}
	}
	while ($dato_content = $content->fetch()){
		// Redireziona se è inserito nel contenuto
		if(!$GLOBALS["is_functioned"]){
			preg_match('/.*?\-\->(.*)/', $dato_content["body"], $matched);
			if (count($matched) > 0){
				if (strstr($matched[0], ":")){
					redirect("/" . $matched[1]);
				} else {
					redirect($matched[1]);
				}
				exit();
			}
		}
		$content_id = $dato_content["id"];
		$GLOBALS["content_id"] = $dato_content["id"];
			$content_subpage = $dato_content["subname"];
			$content_sub_subpage = $dato_content["sub_subname"];
		$content_title = stripslashes($dato_content["title"]);
		$content_subtitle = stripslashes($dato_content["subtitle"]);
			if (strlen(trim($dato_content["manual_content_page"])) == 0){
				$content_last_edit = date("<b>d M Y</b>\{\B\R\}H:i:s", strtotime($dato_content["date"]));
			} else {
				$content_last_edit = "";
			}
			$content_last_edit = converti_data($content_last_edit, "it", "month_first", "short");
			$content_last_edit = str_replace("{BR}", "<br />", $content_last_edit);
		$content_wiki = $dato_content["body"];
		$show_right_panel = $dato_content["show_right_panel"];
		$show_right_panel_toc = $dato_content["show_right_panel_toc"];
		$show_right_panel_tocs = $dato_content["show_right_panel_tocs"];
		$show_top_menu = $dato_content["show_top_menu"];
		
		$GLOBALS["allow_discussions"] = $dato_content["allow_discussions"];
		$GLOBALS["allow_edits"] =  $dato_content["allow_edits"];
		$GLOBALS["allow_chronology"] = $dato_content["allow_chronology"];
		$restrict_to_level = $dato_content["restrict_to_level"];
		
		if ($is_functioned && $GLOBALS["function"] == "Pdf"){
			require_once("common/include/lib/FPDF/get_pdf_of_page.php");
			require_once("Text/Wiki.php");
			require_once("common/include/conf/Wiki/rendering.php");
			$output = $wiki->transform(stripslashes(utf8_decode($content_wiki)), "Xhtml");
			$output = utf8_decode(mb_convert_encoding($output, "UTF-8", "HTML-ENTITIES"));
			$content_body .= stripslashes($output);
			pdf($content_title, $content_body, get_link($content_title));
			exit();
		}
		// Se c'Ã¨ la funzione sulla pagina
		if($GLOBALS["is_functioned"]){
			switch(strtolower($GLOBALS["function_part"])){
				case strtolower($i18n["page_name_special"]):
					switch(strtolower($GLOBALS["function"])){
						case strtolower($i18n["page_name_login"]):
								$GLOBALS["allow_edits"] = 0;
								$GLOBALS["allow_discussions"] = 0;
								$GLOBALS["allow_chronology"] = 0;
							if(isset($_COOKIE["iac"])){
								require_once("common/tpl/__no_login.tpl");
							} else {
								require_once("common/tpl/login.tpl");
							}
							break;
					}
					break;
				case "file":
					
					break;
				case strtolower($i18n["menu_discussion"]):
					if ($GLOBALS["allow_discussions"] == 1){
						require_once("common/tpl/discussion.tpl");
					} else {
						$content_body = $message->generate_error(405);
					}
					break;
				case strtolower($i18n["menu_edit"]);
					if (!is_function_page($GLOBALS["page_title"])){
						if ($GLOBALS["user_level"] > 0){
							if ($GLOBALS["allow_edits"] == "1"){
								if (strlen($dato_content["manual_edit_page"]) > 0){
									require_once("common/tpl/" . $dato_content["manual_edit_page"]);
								} else {
									//$content_title = $GLOBALS["page_title"];
									//$content_subtitle = "Modifica della pagina";
									require_once("common/tpl/create_page.tpl");
								}
							} else {
								$content_body = $message->generate_error(405);
							}
						} else {
							$GLOBALS["is_functioned"] = false;
							require_once("common/tpl/fieldset_login.tpl");
							$modules = $content_body;
							$content_body = $message->generate_error(401);
						}
					} else {
						redirect($GLOBALS["page"]);
					}
					break;
				case strtolower($i18n["chronology_string"]):
					if ($GLOBALS["user_level"] > 0){
						if ($GLOBALS["allow_chronology"] == 1){
							require_once("common/tpl/chronology.tpl");
						} else {
							$content_body = $message->generate_error(405);
						}
					} else {
						require_once("common/tpl/fieldset_login.tpl");
						$modules = $content_body;
					}
					break;
				case strtolower($i18n["search_string"]):
					$GLOBALS["allow_discussions"] = 1;
					$GLOBALS["allow_edits"] = 0;
					$GLOBALS["allow_chronology"] = 0;
					break;
			}
		} else {
			require_once("common/tpl/_top_menu.tpl");
			if (strlen($dato_content["manual_content_page"]) == 0){
				require_once("Text/Wiki.php");
				require_once("common/include/conf/Wiki/rendering.php");
				$output = $wiki->transform(stripslashes(utf8_decode($content_wiki)), "Xhtml");
				
				$content_body .= stripslashes($output);
			} else {
				if ($restrict_to_level <= $GLOBALS["user_level"]){
					$GLOBALS["allow_discussions"] = $dato_content["allow_discussions"];
					if (strlen($content_wiki) > 0){
						require_once("Text/Wiki.php");
						require_once("common/include/conf/Wiki/rendering.php");
						$output = $wiki->transform(stripslashes(utf8_decode($content_wiki)), "Xhtml");
						
						$content_body .= stripslashes($output);
						$content_body .= "<hr />";
					}
					require_once("common/tpl/" . $dato_content["manual_content_page"]);
				} else {
					$content_body = $message->generate_error(405);
				}
			}
		}
	}
} else {
	$content_title = str_replace("_", " ", $GLOBALS["page"]);
	
	$GLOBALS["allow_discussions"] = 1;
	$show_right_panel = 1;
	$show_right_panel_toc = 1;
	
	switch($GLOBALS["page_m"]){
		case $i18n["user_string"]:
			$show_right_panel = 0;
			$show_right_panel_toc = 0;
			
			$users_list = $pdo->query("select `name`, `lastname`, `level` from `airs_users` where `username` = '" . addslashes(strtolower($GLOBALS["page_id"])) . "'");
			if ($users_list->rowCount() > 0){
				while ($dato_users = $users_list->fetch()){
					$content_title = ucwords($dato_users["name"] . " " . $dato_users["lastname"]);
					
					$users_level = $pdo->query("select * from `airs_levels` where level = '" . addslashes($dato_users["level"]) . "'");
					while ($dato_level = $users_level->fetch()){
						$content_subtitle = $dato_level["text"];
					}
				}
			}
			$GLOBALS["allow_discussions"] = 0;
			$GLOBALS["allow_chronology"] = 0;
			
			// Se c'è la funzione sulla pagina
			if($GLOBALS["is_functioned"]){
				switch(strtolower($GLOBALS["function_part"])){
					case strtolower($i18n["menu_edit"]);
						if (strtolower($decrypted_user) == strtolower($GLOBALS["page"])){
							$content_subtitle = $i18n["page_title_personal"];
							require_once("common/tpl/create_personal_page.tpl");
						} else {
							$content_body = $message->generate_error(405);
						}
						break;
					default:
						$content_body = $message->generate_error(405);
						break;
				}
			} else {
				$content_subtitle = $i18n["page_title_personal"];
				require_once("common/tpl/manage_users/user_page.tpl");
			}
			break;
		case "Meeting":
			if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
				if ($GLOBALS["page_title"]){
					if(strlen(trim($GLOBALS[$GLOBALS["page_last_level_type"]])) > 0){
						$query = "select * from `airs_content` where `" . $GLOBALS["page_last_level"] . "` = '" . addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) . "' and (`" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}')";
					} else {
						$query = "select * from `airs_content` where `" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}'";
					}
				} else {
					$query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . $GLOBALS["page_q"] . "'";
				}
				$check_content = $pdo->query($query);
				if ($check_content->rowCount() == 0){
					require_once("common/tpl/create_page.tpl");
				} else {
					while($dato_var_content = $check_content->fetch()){
						require_once("common/tpl/" . $dato_var_content["manual_content_page"]);
						$GLOBALS["allow_discussions"] = $dato_var_content["allow_discussions"];
						$show_right_panel = $dato_var_content["show_right_panel"];
						$show_right_panel_toc = $dato_var_content["show_right_panel_toc"];
					}
				}
			}
			break;
		default:
			// Se c'è la funzione sulla pagina
			if($GLOBALS["is_functioned"]){
				switch(strtolower($GLOBALS["function_part"])){
					case strtolower($i18n["search_string"]):
						if ($GLOBALS["user_level"] > 0){
							$show_right_panel = 1;
							$show_right_panel_toc = 0;
						} else {
							$show_right_panel = 0;
							$show_right_panel_toc = 0;
						}
						if ($GLOBALS["page"] !== $i18n["page_title_search"]){
							$content_title = "<a href=\"" . $i18n["search_string"] . ":\" title=\"" . $i18n["go_to_research_main_page"] . "\">" . $i18n["research_string"] . ":</a> <span>" . $GLOBALS["page_title"] . "</span>";
							$content_subtitle = $i18n["subtitle_results_between_content_of_site"];
							
							require_once("common/include/funcs/search.php");
						} else {
							require_once("common/tpl/search_page.tpl");
						}
						
						break;
					case strtolower($i18n["chronology_string"]):
					case strtolower($i18n["menu_discussion"]):
						if (!is_function_page($GLOBALS["page_title"])){
							if(isset($_COOKIE["iac"])){
								$content_body = $message->generate_error(404);
							} else {
								$content_body = $message->generate_error(401, true);
							}
						} else {
							redirect($GLOBALS["page"]);
						}
						break;
					case "file":
						$show_right_panel = 0;
						$show_right_panel_toc = 0;
						
						if ($GLOBALS["user_level"] > 0){
							if(!isset($_POST["edit_file_btn"]) && !isset($_POST["edit_rdf_btn"])){
								require_once("common/tpl/file_page.tpl");
							} else {
								require_once("common/tpl/upload_file.tpl");
							}
						} else {
							require_once("common/tpl/file_page.tpl");
						}
						break;
					case strtolower($i18n["menu_edit"]);
						if (!is_function_page($GLOBALS["page_title"])){
							if ($GLOBALS["user_level"] > 0){
								if ($GLOBALS["page_title"]){
									if(strlen(trim($GLOBALS[$GLOBALS["page_last_level_type"]])) > 0){
										$query = "select * from `airs_content` where `" . $GLOBALS["page_last_level"] . "` = '" . addslashes($GLOBALS[$GLOBALS["page_last_level_type"]]) . "' and (`" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}')";
									} else {
										if($GLOBALS["next_is_var"] == 1){
											$query = "select * from `airs_content` where `" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' or `" . $GLOBALS["page_level"] . "` = '{1}'";
										} else {
											$query = "select * from `airs_content` where `" . $GLOBALS["page_level"] . "` = '" . addslashes($GLOBALS["page_title"]) . "' and `" . $GLOBALS["page_level"] . "` = ''";
										}
									}
								} else {
									$query = "select * from `airs_content` where `name` = '" . addslashes($GLOBALS["page_m"]) . "' and `subname` = '" . addslashes($GLOBALS["page_id"]) . "' and `sub_subname` = '" . $GLOBALS["page_q"] . "'";
								}
								$check_content = $pdo->query($query);
								if ($check_content->rowCount() == 0){
									require_once("common/tpl/create_page.tpl");
								} else {
									while($dato_var_content = $check_content->fetch()){
										if($dato_var_content["manual_edit_page"] !== ""){
											require_once("common/tpl/" . $dato_var_content["manual_edit_page"]);
										}
									}
								}
							} else {
								require_once("common/tpl/login.tpl");
							}
						} else {
							redirect($GLOBALS["page"]);
						}
						break;
					case strtolower($i18n["page_name_special"]):
						switch(strtolower($GLOBALS["page"])){
							case strtolower($i18n["page_name_login"]):
								$GLOBALS["allow_edits"] = 0;
								$GLOBALS["allow_discussions"] = 0;
								$GLOBALS["allow_chronology"] = 0;
								if(isset($_COOKIE["iac"])){
									require_once("common/tpl/__no_login.tpl");
									redirect("./" . $i18n["page_name_main"]);
								} else {
									require_once("common/tpl/login.tpl");
								}
								break;
						}
						break;
				}
			} else {
				$show_right_panel_toc = 0;
				$show_right_panel_tocs = 0;
				$modules = $content_body;
				
				if(!isset($_COOKIE["iac"])){
					$content_body = $message->generate_error(404);
				} else {
					if ($restrict_to_level <= $GLOBALS["user_level"]){
						$content_body = $message->generate_error(401);
					}
				}
			}
			break;
	}
}
if (strlen($content_last_edit) > 0){
	$content_last_edit = "<p title=\"" . $i18n["last_edit_string"] . "\">" . $content_last_edit . "</p>";
} else {
	$content_last_edit = "";
}
?>
<table cellspacing="0" cellpadding="0">
	<tr>
		<td valign="top" id="content_wrapper">
			<?php
			if ($show_right_panel == 1){
				$panel_btn_class = "horizontal";
				$right_panel_class = "opened";
			} else {
				$panel_btn_class = "vertical";
				$right_panel_class = "closed";
			}
			?>
			<div id="content_wrapper_title">
				<?php
				$content_title = ($GLOBALS["content_title"] ? $GLOBALS["content_title"] : $content_title);
				if (strlen(trim($content_title)) > 0){
					if (strlen(trim($GLOBALS["page_last_level"])) > 0){
						if($GLOBALS["page_level_key"] == "q"){
							$prev_link = $GLOBALS["page_m"] . "/" . $GLOBALS["page_id"];
						} else {
							$prev_link = $GLOBALS["page_m"];
						}
						$content_last_edit = '<a href="./' . $prev_link . '" title="' . $i18n["go_to_page"] . ' ' . $GLOBALS[$GLOBALS["page_last_level_type"]] . '">' . $GLOBALS[$GLOBALS["page_last_level_type"]] . '</a>: ';
					}
					?>
					<h1><?php print $content_last_edit; ?><span class="title"><?php print $content_title; ?></span></h1>
					<?php
				}
				$content_subtitle = ($GLOBALS["content_subtitle"] ? $GLOBALS["content_subtitle"] : $content_subtitle);
				if (strlen(trim($content_subtitle)) > 0){
					?>
					<h2><?php print $content_subtitle; ?></h2>
					<?php
				}
				?>
			</div>
			<div id="content_wrapper_main_content">
				<?php
				// OKKIO: rischio squaqquarellamenti di codifica...
				print mb_convert_encoding($content_body, "UTF-8", "ASCII");
				?>
			</div>
			<div id="content_wrapper_dynamic_content"></div>
		</td>
		<?php
		//if(!$GLOBALS["is_functioned"] || $show_right_panel == 1 || $show_right_panel_toc == 1){
		if(!$GLOBALS["is_functioned"] && $show_right_panel == 1 || !$GLOBALS["is_functioned"] && $show_right_panel_toc == 1){
			?>
			<td valign="top" id="content_right_panel" class="<?php print $right_panel_class; ?>">
				<?php
				// Pulsanti laterali
				require_once("common/tpl/right_panel_btns.tpl");
				
				if ($show_right_panel == 1){
					$right_panel_style = "";
				} else {
					$right_panel_style = " style=\"display: none;\"";
				}
				if ($show_right_panel_toc == 1){
					?>
					<div id="toc"<?php print $right_panel_style; ?>>
						<h1><?php print strtoupper($i18n["index_string"]); ?></h1>
						<div class="toc_content">
							<?php
							require_once("common/include/funcs/generate_toc_tree.php");
							print generate_toc_tree($content_body, "section");
							?>
						</div>
					</div>
					<?php
				}
				if ($show_right_panel_tocs == 1){
					?>
					<div id="tocs"<?php print $right_panel_style; ?>>
						<h1><?php print strtoupper($i18n["sub_pages_string"]); ?></h1>
						<div class="toc_content">
							<?php
							require_once("common/tpl/_subpages_menu.tpl");
							?>
						</div>
					</div>
					<?php
				}
				if(!isset($modules)){
					$content_modules = $pdo->prepare("select * from `airs_content_modules` where `content_id` = '" . addslashes($content_id) . "' and `visible` = '1'");
					if($content_modules->execute()){
						if ($content_modules->rowCount() > 0){
							while ($content_modules = $content_modules->fetch()){
								$modules = $dato_modules["modules"];
							}
							if (strstr($modules, ",")){
								$modules_array = explode(",", $modules);
								foreach($modules_array as $mod){
									require_once("common/tpl/" . trim($mod));
								}
							} else {
								require_once("common/tpl/" . trim($modules));
							}
						} else {
							if (strlen($modules) > 0){
								print "<div>" . $modules . "</div>";
							}
						}
					}
				} else {
					require_once("common/tpl/" . trim($modules));
				}
				?>
			</td>
			<?php
		}
		?>
	</tr>
</table>
