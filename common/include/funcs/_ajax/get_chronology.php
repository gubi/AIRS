<?php
/**
* Generates page cronology template
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
header("Content-type: text/plain");
require_once("../../.mysql_connect.inc.php");

if (isset($_GET["page"]) && trim($_GET["page"]) && isset($_GET["rows_per_page"]) && trim($_GET["rows_per_page"]) && isset($_GET["page_no"]) && trim($_GET["page_no"])){
	function create_reason($analize, $id){
		if (!is_array($analize)){
			$items = array(array($analize));
		} else {
			$items = $analize;
		}
		foreach($items as $k => $item){
			$diff = new Text_Diff('auto', $item);
			$renderer = new Text_Diff_Renderer_inline(
				array(
					'ins_prefix' => '%g',
					'ins_suffix' => '%n',
					'del_prefix' => '%r',
					'del_suffix' => '%n',
				)
			);
			$output = htmlspecialchars_decode($renderer->render($diff));
			
			preg_match_all("/\%g(.*?)\%n/", $output, $added_text);
			preg_match_all("/\%r(.*?)\%n/", $output, $removed_text);
			if (strlen($output) > 0){
				foreach($added_text[0] as $added_txt){
					$txt["added"][] = array($k,  $added_txt);
				}
				foreach($removed_text[0] as $removed_txt){
					$txt["removed"][] = array($k, $removed_txt);
				}
			}
		}
		if (is_array($txt)){
			$a = 0;
			$r = 0;
			
			$word_txt[] = "Cambiamenti";
			$word_txt[] = "nel";
			foreach($txt as $action => $txt2){
				switch($txt2[0][0]){
					case 0:	$type[] = "titolo";					break;
					case 1:	$type[] =	"sottotitolo";				break;
					case 2:	$type[] =	"corpo della pagina";		break;
				}
			}
			$type = array_unique($type);
			$c_t = 0;
			foreach($type as $tipo){
				$c_t++;
				$word_txt[] = "%p" . $tipo . "%n";
				if (count($type) > 1 && $c_t < count($type)){
					if ($c_t !== count($type)-1){
						$word_txt[] = ", nel";
					} else {
						$word_txt[] = "e nel";
					}
				}
			}
			return "<a href=\"javascript:void(0);\" onclick=\"show_details('" . $id . "')\" title=\"Visualizza\">" . colorize(join(" ", $word_txt)) . "</a>";
		} else {
			return "<i style=\"color: #999;\">Nessuna differenza con la versione attuale</i>";
		}
	}
	
	print "<table cellspacing=\"0\" cellpadding=\"10\" class=\"list\">";
	print "<thead><tr><td style=\"width: 173px;\">Data</td><td style=\"width: 697px;\">Sommario dei cambiamenti</td><td style=\"width: 120px;\">Utente</td></tr></thead>";
	print "<tbody style=\"height: auto;\">";
	
	// Testo corrente
	$pdo = db_connect("");
	$current_body = $pdo->query("select * from airs_content where `name` = '" . addslashes(urldecode($_GET["page"])) . "' and `subname` = '" . addslashes(urldecode($_GET["subpage"])) . "' and `sub_subname` = '" . addslashes(urldecode($_GET["sub_subpage"])) . "'");
	if ($current_body->rowCount() > 0){
		while ($dato_current_body = $current_body->fetch()){
			$current_title = array($dato_current_body["title"]);
			$current_subtitle = array($dato_current_body["subtitle"]);
			$current = array($dato_current_body["body"]);
		}
		$chronology_count = $pdo->query("select * from airs_chronology where `name` = '" . addslashes(urldecode($_GET["page"])) . "' and `subname` = '" . addslashes(urldecode($_GET["subpage"])) . "' and `sub_subname` = '" . addslashes(urldecode($_GET["sub_subpage"])) . "' order by `id` desc");
		if ($chronology_count->rowCount() > 0){
			$pages = round($chronology_count->rowCount()/$_GET["rows_per_page"]);
			if ($pages == 0){
				$pages = 1;
			}
			$n = 0;
			$current_page = 0;
			while ($dato_chronology_count = $chronology_count->fetch()){
				$n++;
				if ($n % $_GET["rows_per_page"] == 1){
					$current_page++;
				}
					$paging[] = array($current_page => $dato_chronology_count["id"]);
			}
		}
		foreach($paging as $k => $v){
			foreach($v as $page_n => $id){
				if ($_GET["page_no"] == $page_n){
					$chronology = $pdo->query("select * from airs_chronology where `id` = '" . addslashes($id) . "' and `name` = '" . addslashes(urldecode($_GET["page"])) . "' and `subname` = '" . addslashes(urldecode($_GET["subpage"])) . "' and `sub_subname` = '" . addslashes(urldecode($_GET["sub_subpage"])) . "'");
					
					require_once("Console/Color.php");
					require_once("Text/Diff.php");
					require_once("Text/Diff/Renderer.php");
					require_once("Text/Diff/Renderer/inline.php");
					require_once("../colorize.php");
					
					if ($chronology->rowCount() > 0){
						$current_page = 1;
						
						while ($dato_chronology = $chronology->fetch()){
								if (strlen($dato_chronology["reason"]) > 0){
									$reason = "<a href=\"javascript:void(0);\" onclick=\"show_details('" . $dato_chronology["id"] . "')\" title=\"Visualizza\">" . colorize("%b" . ucfirst(stripslashes($dato_chronology["reason"])) . "%n") . "</a>";
								} else {
									$reason = create_reason(array(array(array($dato_chronology["title"]), $current_title), array(array($dato_chronology["subtitle"]), $current_subtitle), array(array($dato_chronology["body"]), $current)), $dato_chronology["id"]);
								}
								$is_actual = 0;
									$chronology_version = $pdo->query("select * from airs_content where `chronology_version` = '" . addslashes($dato_chronology["id"]) . "'");
									if ($chronology_version->rowCount() > 0){
										$reason .= " (versione attuale)";
										$is_actual = 1;
									}
								$new_output = "<tr class=\"view\"><td title=\"Data\" style=\"width: 180px;\" valign=\"top\">" . date("<b>d M Y</b> \a\l\l\e H:i:s", strtotime($dato_chronology["date"])) . " </td><td id=\"td_" . $dato_chronology["id"] .  "\" title=\"Sommario dei cambiamenti\">" . $reason . "<div class=\"detail\" style=\"display: none;\"><input type=\"hidden\" id=\"stored_content_" . $dato_chronology["id"] . "\" />";
								$new_output .= "	<div id=\"toolbar\">";
								$new_output .= "	<a class=\"show_formatted_btn formatted_btn_" . $dato_chronology["id"] . "\" href=\"javascript:void(0);\" onclick=\"show_formatted('" . $dato_chronology["id"] . "')\" title=\"Vedi testo formattato\"></a>";
								$new_output .= "	<a class=\"show_monospaced_btn monospaced_btn_" . $dato_chronology["id"] . "\" href=\"javascript:void(0);\" onclick=\"show_monospaced('" . $dato_chronology["id"] . "')\" title=\"Vedi con testo monospaziato\"></a>";
								$new_output .= "	<a class=\"send_via_mail_btn\" href=\"javascript:void(0);\" onclick=\"send_via_mail('" . $dato_chronology["id"] . "')\" title=\"Invia il testo per e-mail\"></a>";
								if (!$is_actual){
									$new_output .= "	<a class=\"restore_btn\" href=\"javascript:void(0);\" onclick=\"restore('" . $dato_chronology["id"] . "')\" title=\"Ripristina questa versione\"></a>";
								}
								$new_output .= "	</div>";
								$new_output .= "<div class=\"details_" . $dato_chronology["id"] . "_content\"></div></div></td><td title=\"Utente\" style=\"width: 120px;\" valign=\"bottom\"><a href=\"User/" . ucfirst($dato_chronology["user"]) . "\" title=\"Vai alla pagina dell'utente\">" . ucfirst($dato_chronology["user"]) . "</a></td></tr>";
								
								$outputs[$dato_chronology["id"]] = $new_output;
						}
					} else {
						print "<tr><td style=\"font-style: italic;\">Non sono presenti differenze di contenuto per questa pagina</td></tr>";
					}
				}
			}
		}
		// Stampa l'elenco...
		if (is_array($outputs)){
			$i = 0;
			foreach ($outputs as $id => $output){
				$i++;
				//$output_table .= $output;
				//$output_table .= "<form method=\"post\" action=\"\"><table cellpadding=\"0\" cellspacing=\"0\"><tr><td><input type=\"hidden\" name=\"chronology_id\" value=\"" . $id . "\" /><input type=\"submit\" name=\"restore_chronology_btn\" value=\"Ripristina\" /></td></tr></table></form>";
				
				print $output;
			}
		}
	} else {
		print "<tr><td style=\"font-style: italic;\">Non sono presenti contenuti per questa pagina</td></tr>";
	}
	print "</tbody></table>";
	print "<input type=\"hidden\" name=\"current_page\" id=\"current_page\" value=\"" . $current_page . "\" />";
	print "<div id=\"page_numbers\">";
	if ($_GET["page_no"] == 1){
		print "<span title=\"sei alla prima pagina\">&laquo;</span>&nbsp;&nbsp;&nbsp;";
	} else {
		print "<a href=\"javascript:void(0);\" onclick=\"get_chronology('" . $_GET["page"] . "', '" . $_GET["subpage"] . "', '" . $_GET["sub_subpage"] . "', '" . $_GET["rows_per_page"] . "', '1');\" title=\"Vai alla prima pagina\">&laquo;</a>&nbsp;&nbsp;&nbsp;";
	}
		for($i = 1; $i <= $pages; $i++){
			if ($i == $_GET["page_no"]){
				print "<span title=\"pagina " . $i . "\">" . $i . "</span> ";
			} else {
				print "<a href=\"javascript:void(0);\" onclick=\"get_chronology('" . $_GET["page"] . "', '" . $_GET["subpage"] . "', '" . $_GET["sub_subpage"] . "', '" . $_GET["rows_per_page"] . "', '" . $i . "');\" title=\"vai alla pagina " . $i . "\">" . $i . "</a> ";
			}
		}
	if ($_GET["page_no"] == $pages){
		print "&nbsp;&nbsp;&nbsp;<span title=\"sei all'ultima pagina\">&raquo;</span>";
	} else {
		print "&nbsp;&nbsp;&nbsp;<a href=\"javascript:void(0);\" onclick=\"get_chronology('" . $_GET["page"] . "', '" . $_GET["subpage"] . "', '" . $_GET["sub_subpage"] . "', '" . $_GET["rows_per_page"] . "', '" . $pages . "');\" title=\"Vai all'ultima pagina\">&raquo;</a>";
	}
	print "</div>";
}
?>