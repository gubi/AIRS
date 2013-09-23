<?php
/**
* AIRS Search Engine
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
/*
************************* REGISTRA OGNI RICERCA SU DATABASE *************************
*/

$termine = trim(urldecode(addslashes(str_replace("_", " ", $GLOBALS["page"]))));

$logpdo = db_connect("system_logs");
// salva l'accesso
$query_add_log = $logpdo->prepare("insert into `airs_searches` (`user`, `ip`, `word`, `data`, `ora`, `referer`) values(?, ?, ?, ?, ?, ?)");
$query_add_log->bindParam(1, addslashes($GLOBALS["user"]));
$query_add_log->bindParam(2, $_SERVER["REMOTE_ADDR"]);
$query_add_log->bindParam(3, $termine);
$query_add_log->bindParam(4, date("Y-m-d"));
$query_add_log->bindParam(5, date("H:i:s"));
$query_add_log->bindParam(6, addslashes($GLOBALS["referer_page"]));
if (!$query_add_log->execute()) {
	print "Non posso inserire il log della ricerca.<br />";
	print "Codice errore: " . $query_add_log->errorCode() . "<br />";
	print "Informazioni: " . join(", ", $query_add_log->errorInfo()) . "<br />";
	exit();
}

//$return_arr = array();

function search($termine, $database, $tabella, $colonne, $type, $search_type = ""){
	$pdo = db_connect($database);
	
	switch ($type){
		case "attinence":
			$c = 0;
			foreach ($colonne as $cols){
				$c++;
				$colums .= "`" . $cols . "`";
				if ($c < count($colonne)){
					$colums .= ", ";
				}
			}
			// Ricerca per attinenza
			$search_content = $pdo->prepare("select *, match(" . $colums . ") against(:term) as 'score' from " . $tabella . " where match(" . $colums . ") against(:term) order by `score` desc");
			$search_content->execute(array(":term" => "+" . $termine));
			if ($search_content->rowCount() > 0){
				while ($dato_search_content = $search_content->fetch()) {
					$score = $dato_search_content["score"];
					$f++;
					if ($f == 1){
						$max_res = $dato_search_content["score"];
					}
					$score_percentage = ($score/$max_res)*100;
					$dato_search_content["percentage"] = round($score_percentage, 1);
					
					$row .= "<tr><td valign=\"top\" style=\"width: 50px;\">[" . $dato_search_content["percentage"] . "%]</td>";
					switch ($search_type){
						case "pagina":
							$row .= "<td><a href=\"" . $dato_search_content[$colonne[0]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "</a>";
							if (strlen($dato_search_content[$colonne[1]]) > 0){
								$row .= " &rsaquo; <a href=\"" . $dato_search_content[$colonne[0]] . "/" . $dato_search_content[$colonne[1]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[1]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content[$colonne[1]])) . "</a>";
							}
							if (strlen($dato_search_content[$colonne[2]]) > 0){
								$row .= " &rsaquo; <a href=\"" . $dato_search_content[$colonne[0]] . "/" . $dato_search_content[$colonne[1]] . "/" . $dato_search_content[$colonne[2]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[2]])) . "\">" . str_replace("_", " ", $dato_search_content[$colonne[2]]) . "</a>";
							}
							break;
						case "file":
							break;
						case "feed":
							$row .= "<td><a href=\"EditoRSS/Modifica_Feed/" . $dato_search_content["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "</a>";
							break;
						case "news_RSS":
							$row .= "<td><a href=\"EditoRSS/News/" . $dato_search_content["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "</a>";
							break;
						case "AIR":
							$row .= "<td><a href=\"AIR/Ricerche/" . $dato_search_content["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content[$colonne[0]])) . "</a>";
							break;
					}
					// Sottotitolo della pagina
					if (strlen($dato_search_content[$colonne[3]]) > 0){
						$row .= "<br /><i>" . stripslashes($dato_search_content[$colonne[3]]) . "</i>";
					} else {
						$row .= "";
					}
					// Corpo della pagina
					/*
					if (strlen($dato_search_content["body"]) > 0){
						require_once("common/include/lib/PEAR/Text/Wiki.php");
						require_once("common/include/conf/Wiki/rendering.php");
						require_once("common/include/funcs/_taglia_stringa.php");
						$output = $wiki->transform(stripslashes(utf8_decode($dato_search_content["body"])), "Xhtml");
						$row .= "<br /><iframe style=\"width: 100%; height: 300px; border: 0px none;\">" . stripslashes(utf8_encode($output)) . "</iframe>";
					}
					*/
					$row .= "</td></tr>";
					
					// Redirect
					if ($dato_search_content["percentage"] == 100 && $search_type == "pagina"){
						switch(strtolower($termine)) {
							case strtolower(str_replace("_", " ", $dato_search_content[$colonne[0]])):
								header("Location: " . $dato_search_content[$colonne[0]] . $GLOBALS["log_id"]);
								break;
							case strtolower(str_replace("_", " ", $dato_search_content[$colonne[1]])):
								header("Location: " . $dato_search_content[$colonne[0]] . "/" . $dato_search_content[$colonne[1]]);
								break;
							case strtolower(str_replace("_", " ", $dato_search_content[$colonne[2]])):
								header("Location: " . $dato_search_content[$colonne[0]] . "/" . $dato_search_content[$colonne[1]] . "/" . $dato_search_content[$colonne[2]]);
								break;
						}
					}
				}
				return utf8_decode("<div class=\"search_results\"><table cellspacing=\"0\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td valign=\"top\" style=\"font-size: 1.1em;\"><h2>" . ucfirst(str_replace("_", " ", $search_type)) . ": risultati per attinenza</h2><span>Ricerca in base alla frequenza e alla posizione del termine</span><table cellpadding=\"2\" cellspacing=\"2\">" . $row . "</table></td><td style=\"width: 128px;\"><img src=\"common/media/img/copy_128_ccc.png\" /></td></tr></table></div>");
			} else {
				//return "<div class=\"search_results\"><i>Non è stato trovato nessun risultato per attinenza</i></div>";
			}
			break;
		case "affinity":
			for ($c = 0; $c < count($colonne); $c++){
				$case .= "(case when `" . $colonne[$c] . "` like '%" . $termine . "%' then 1 else 0 end)";
				$where .= "`" . $colonne[$c] . "` like '%" . $termine . "%' ";
				if ($c < count($colonne)-1){
					$case .= " + ";
					$where .= "or ";
				}
			}
			$search_content_accurate = $pdo->prepare("select *, (" . $case . ") as `relevance` from `" . $tabella . "` where " . $where . " order by `relevance` desc");
			$search_content_accurate->execute();
			//print "<br /><br />select *, (" . $case . ") as `relevance` from `" . $tabella . "` where " . $where . " order by `relevance` desc<br />";
			if ($search_content_accurate->rowCount() > 0){
				while ($dato_search_content_accurate = $search_content_accurate->fetch()) {
					$relevance = $dato_search_content_accurate["relevance"];
					$ff++;
					if ($ff == 1){
						$max_res_a = $dato_search_content_accurate["relevance"];
					}
					$relevance_percentage = ($relevance/$max_res_a)*100;
					$dato_search_content_accurate["percentage"] = round($relevance_percentage, 1);
					
					$row_a .= "<tr><td valign=\"top\" style=\"width: 50px;\">[" . $dato_search_content_accurate["percentage"] . "%]</td>";
					switch ($search_type){
						case "pagina":
							$row_a .= "<td><a href=\"" . $dato_search_content_accurate[$colonne[0]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "</a>";
							if (strlen($dato_search_content_accurate[$colonne[1]]) > 0){
								$row_a .= " &rsaquo; <a href=\"" . $dato_search_content_accurate[$colonne[0]] . "/" . $dato_search_content_accurate[$colonne[1]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[1]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[1]])) . "</a>";
							}
							if (strlen($dato_search_content_accurate["sub_subname"]) > 0){
								$row_a .= " &rsaquo; <a href=\"" . $dato_search_content_accurate[$colonne[0]] . "/" . $dato_search_content_accurate[$colonne[1]] . "/" . $dato_search_content_accurate[$colonne[2]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[2]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[2]])) . "</a>";
							}
							break;
						case "file":
							break;
						case "feed":
							$row_a .= "<td><a href=\"EditoRSS/Modifica_Feed/" . $dato_search_content_accurate["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "</a>";
							break;
						case "news_RSS":
							$row_a .= "<td><a href=\"EditoRSS/News/" . $dato_search_content_accurate["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "</a>";
							break;
						case "AIR":
							$row_a .= "<td><a href=\"AIR/Ricerche/" . $dato_search_content_accurate["id"] . "\" title=\"Vai alla pagina " . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "\">" . str_replace("_", " ", stripslashes($dato_search_content_accurate[$colonne[0]])) . "</a>";
							break;
					}
					
					// Sottotitolo della pagina
					if (strlen($dato_search_content_accurate["subtitle"]) > 0){
						$row_a .= "<br /><i>" . stripslashes($dato_search_content_accurate[$colonne[3]]) . "</i>";
					} else {
						$row_a .= "";
					}
					$row_a .= "</td></tr>";
					
					// Redirect
					if ($dato_search_content_accurate["percentage"] == 100 && $search_type == "pagina"){
						switch(strtolower($termine)) {
							case strtolower(str_replace("_", " ", $dato_search_content_accurate[$colonne[0]])):
								header("Location: " . $dato_search_content_accurate[$colonne[0]] . $GLOBALS["log_id"]);
								break;
							case strtolower(str_replace("_", " ", $dato_search_content_accurate[$colonne[1]])):
								header("Location: " . $dato_search_content_accurate[$colonne[0]] . "/" . $dato_search_content_accurate[$colonne[1]]);
								break;
							case strtolower(str_replace("_", " ", $dato_search_content[$colonne[2]])):
								header("Location: " . $dato_search_content_accurate[$colonne[0]] . "/" . $dato_search_content_accurate[$colonne[1]] . "/" . $dato_search_content_accurate[$colonne[2]]);
								break;
						}
					}
				}
				return utf8_decode("<div class=\"search_results\"><table cellspacing=\"0\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td valign=\"top\" style=\"font-size: 1.1em;\"><h2>" . ucfirst(str_replace("_", " ", $search_type)) . ": risultati per affinità dei termini</h2><span>Ricerca in base al primo termine trovato in virtù della sua posizione</span><table cellpadding=\"2\" cellspacing=\"2\">" . $row_a . "</table></td><td style=\"width: 128px;\"><img src=\"common/media/img/paste_128_ccc.png\" /></td></tr></table></div>");
			} else {
				//return "<div class=\"search_results\"><i>Non è stato trovato nessun risultato per affinità dei termini</i></div>";
			}
			break;
		case "file":
			for ($c = 0; $c < count($colonne); $c++){
				$case .= "(case when `" . $colonne[$c] . "` like '%" . $termine . "%' then 1 else 0 end)";
				$where .= "`" . $colonne[$c] . "` like '%" . $termine . "%' ";
				if ($c < count($colonne)-1){
					$case .= " + ";
					$where .= "or ";
				}
			}
			$search_content_accurate = $pdo->query("select *, (" . $case . ") as `relevance` from `" . $tabella . "` where " . $where . " order by `relevance` desc");
			if ($search_content_accurate->rowCount() > 0){
				while ($dato_search_content_accurate = $search_content_accurate->fetch()) {
					$relevance = $dato_search_content_accurate["relevance"];
					$ff++;
					if ($ff == 1){
						$max_res_a = $dato_search_content_accurate["relevance"];
					}
					$relevance_percentage = ($relevance/$max_res_a)*100;
					$dato_search_content_accurate["percentage"] = round($relevance_percentage, 1);
					
					$row_a .= "<tr><td valign=\"top\" style=\"width: 50px;\">[" . $dato_search_content_accurate["percentage"] . "%]</td>";
					$row_a .= "<td><a href=\"file:" . $dato_search_content_accurate[$colonne[0]] . "\" title=\"Vai alla pagina " . str_replace("_", " ", $dato_search_content_accurate[$colonne[0]]) . "\">" . str_replace("_", " ", $dato_search_content_accurate[$colonne[0]]) . "</a>";
					
					// Sottotitolo della pagina
					if (strlen($dato_search_content_accurate["subtitle"]) > 0){
						$row_a .= "<br /><i>" . $dato_search_content_accurate[$colonne[2]] . "</i>";
					} else {
						$row_a .= "";
					}
					$row_a .= "</td></tr>";
					
					// Redirect
					if ($dato_search_content_accurate["percentage"] == 100){
						if (strtolower($termine) == strtolower(str_replace("_", " ", $dato_search_content_accurate[$colonne[0]]))){
							header("Location: file:" . $dato_search_content_accurate[$colonne[0]]);
						}
					}
				}
				return utf8_decode("<div class=\"search_results\"><table cellspacing=\"0\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td valign=\"top\" style=\"font-size: 1.1em;\"><div class=\"search_results\"><h2>" . ucfirst(str_replace("_", " ", $search_type)) . ": risultati per affinità dei termini tra i files</h2><span>Ricerca in base al primo termine trovato in virtù della sua posizione tra i files</span><table cellpadding=\"2\" cellspacing=\"2\">" . $row_a . "</table></td><td style=\"width: 128px;\"><img src=\"common/media/img/filepath_128_ccc.png\" /></td></tr></table></div>");
			} else {
				//return "<div class=\"search_results\"><i>Non è stato trovato nessun risultato per affinità dei termini tra i files</i></div>";
			}
			break;
	}
}
$content_body = search($termine, "airs", "airs_content", array("name", "subname", "sub_subname", "title", "subtitle", "body"), "attinence", "pagina");
$content_body .= search($termine, "airs", "airs_content", array("name", "subname", "sub_subname", "title", "subtitle", "body"), "affinity", "pagina");
$content_body .= search($termine, "airs", "airs_rdf_files", array("file", "title", "description", "tag", "origins", "origins_uri", "author", "author_uri", "author_entity", "author_entity_uri", "license", "license_uri"), "file");
$content_body .= search($termine, "editorss", "editorss_feeds", array("title", "description", "group", "tags"), "attinence", "feed");
$content_body .= search($termine, "editorss", "editorss_feeds", array("title", "description", "group", "tags"), "affinity", "feed");
$content_body .= search($termine, "editorss", "editorss_feeds_news", array("title", "description", "tags"), "attinence", "news_RSS");
$content_body .= search($termine, "editorss", "editorss_feeds_news", array("title", "description", "tags"), "affinity", "news_RSS");
$content_body .= search($termine, "air", "air_research", array("title", "description", "tags"), "attinence", "AIR");
$content_body .= search($termine, "air", "air_research", array("title", "description", "tags"), "affinity", "AIR");
?>