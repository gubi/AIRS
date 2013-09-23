<?php
/**
* Generates TOC tree from wiki page
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
// Va snellito e pulito il codice ma così funziona
function generate_toc_tree($html, $link_type = "section") {
	switch ($link_type) {
		case "section":
			if (is_array($html)){
				$html = implode(",", $html);
			}
			preg_match_all("/(<h([0-6]{1})[^<>]*>)(.*?)(<\/h[0-6]{1}>)/", $html, $matches, PREG_SET_ORDER);
			
			$li = 0; // list Item Count
			$hl = 2; // Heading Level
			$sub_heading = false;
			foreach ($matches as $val) {
				if (strtolower($val[3]) !== "indice"){
					++$li;
					$start_link = $GLOBALS["current_pos"] . "#";
					preg_match("/.*href=\"(.*?)\".*/", $val[3], $link_matched);
					if(count($link_matched) > 0){
						$txt = $link_matched[1];
					} else {
						$txt = $val[3];
					}
					if ($val[2] == $hl) {
						$list["$li"] = "<li><a href=\"" . $start_link . $val[3] . "\">" .  $txt . "</a></li>";
					} else if ($val[2] > $hl) {
						$list["$li"] = "<li><ul><li><a href=\"" . $start_link . $txt . "\">" . $txt . "</a></li>";
						if ($sub_heading === true) {
							$sub_heading = false;
						} else {
							$sub_heading = true;
						}
					} else if ($val[2] < $hl) {
						if ($li > 1){
							$close_ul = "</ul>";
							if ($val[2] == 1 && $matches[$li-2][2] > 2){
								$close_ul .= "</ul>";
							}
						}
						$list["$li"] = $close_ul . "<li><a href=\"" . $start_link . $txt . "\">" .  $txt . "</a></li>";
					}
				}
				$Sections["$li"] = $val[1] . $val[2] . $val[3]; // Original heading to be Replaced.
				$SectionWIDs["$li"] = "<h" . $val[2] . " id=\"" . $val[3] . $val[4] . "\">" . $val[3] . $val[4]; // This is the new Heading.
				$hl = $val[2];
			}
			switch ($hl) {
				case 3:
					$list["$li"] = $list["$li"] . "</ul></li>";
					break;
				case 4:
					$list["$li"] = $list["$li"] . "</ul></li></ul></li>";
					break;
				case 5:
					$list["$li"] = $list["$li"] . "</ul></li></ul></li></ul></li>";
					break;
				case 6:
					$list["$li"] = $list["$li"] . "</ul></li></ul></li></ul></li></ul></li>";
					break;
			}
			$settu = "";
			if (is_array($list)){
				foreach ($list as $val) { // Puts together the list.
					$settu = $settu . $val;
				}
			
				return "<ul>" . $settu . "</ul>";
			} else {
				return "<div style=\"width: 100%; text-align: center; color: #999;\"><i>nessun indice per questa pagina</i></div>";
			}
			break;
		case "path":
			if (is_array($html)){
				$html = implode(",", $html);
			}
			preg_match_all("/(<h([0-6]{1})[^<>]*>)(.*?)(<\/h[0-6]{1}>)/", $html, $matches, PREG_SET_ORDER);
			
			$link = Array();
			$li = 0; // list Item Count
			foreach ($matches as $val) {
				$level = $val[2];
				$text = $val[3];
				if (strlen($val[3]) > 0){
					if ($val[2] == 1){
						$li++;
					}
					for ($i = 1; $i <= $level; $i++){
						if ($i == $val[2]){
							$list_init .= "<ul><li>";
							$list_close .= "</li></ul>";
							$link[$li][$val[2]] = $text;
							//print $li . " ~ " . $val[2] . " = " . $text . "<br />";
						}
					}
				}
			}
			$tree = "<ul>";
			$link_ref = 0;
			foreach($link as $row => $list){
				$tree .= "<li>";
				foreach($list as $l => $text){
					if ($l > 1){
						$link_ref ++;
						$tree .= "<ul><li><a href=\"/" . $GLOBALS["current_pos"] . "/" . str_replace(" ", "_", $link[$row][$l - $link_ref] . "/" . $link[$row][$l]) . "\">" . $link[$row][$l] . "</a></li></ul>";
					} else {
						$link_ref = 0;
						$tree .= "<a href=\"/" . $GLOBALS["current_pos"] . "/" . str_replace(" ", "_", $link[$row][$l]) . "\">" . $link[$row][$l] . "</a>";
					}
				}
				$tree .= "</li>";
			}
			$tree .= "</ul>";
			return $tree;
			break;
	}
}
?>