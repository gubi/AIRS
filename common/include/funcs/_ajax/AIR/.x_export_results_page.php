<?php
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");

if (isset($_POST["id"]) && trim($_POST["id"])){
	$card_title = str_replace("_", " ", $_POST["id"]);
	// Connette ai database
	$pdo = db_connect("air");
	// Estrae i dati della pagina
	$page_card = $pdo->query("select * from `air_research_results` where `result_link_text` = '" . addslashes($card_title) . "'");
	if ($page_card->rowCount() > 0){
		$content = "\"DATA DI SCANSIONE\",\"DESCRIZIONE DELLA RICERCA\",\"RICERCA DI AFFERENZA\",\"PAGINA\",\"NUMERO\",\"LINK ALLA PAGINA DEI RISULTATI\",\"TAG\",\"LINK AL RISULTATO\",\"DESCRIZIONE DEL RISULTATO\",\"CONTENUTO SCANSIONATO\",\"INDIRIZZO COPIA CACHE\";\n";
		while($dato_page_card = $page_card->fetch()){
			$research = $pdo->query("select * from `air_research` where id = '" . addslashes($dato_page_card["research_id"]) . "'");
			if ($research->rowCount() > 0){
				while($dato_research = $research->fetch()){
					$research_date = converti_data(date("D, d M Y \a\l\l\e H.i:s", strtotime($dato_page_card["date"])), "it", "month_first");
					$research_title = $dato_research["title"];
					$research_description = $dato_research["description"];
					$research_tag = $dato_research["tags"];
					$research_page = $dato_research["result_page"];
					$research_no = $dato_research["result_id"];
						$research_link_description = ucfirst(stripslashes($dato_page_card["result_description"]));
						$research_link = $dato_page_card["search_uri"];
						$research_result_text = $dato_page_card["result_link_text"];
						$research_result_link = $dato_page_card["result_uri"];
						
						$research_content = ucfirst(stripslashes($dato_page_card["result_content"]));
						$research_entire_html_content = preg_replace(array('/\>\n+/', '/\t+/', '/\r+/', '/\n+/'), array('>', '', '', ' '), $dato_page_card["result_entire_html_content"]);
						$research_entire_html_content = str_replace("\"", "\\\"", $research_entire_html_content);
						
						$research_cache_uri = $dato_page_card["result_cache_uri"];
						$research_cache_html_content = preg_replace(array('/\>\n+/', '/\t+/', '/\r+/', '/\n+/'), array('>', '', '', ' '), $dato_page_card["result_cache_html_content"]);
						$research_cache_html_content = str_replace("\"", "\\\"", $research_cache_html_content);
				}
			}
		}
		$tags_arr = explode(",", $research_tag);
		foreach($tags_arr as $tag){
			$tags[] = trim($tag);
		}
		$research_tags = utf8_decode(implode(", ", $tags));
		$content .= "\"$research_date\",\"$research_description\",\"$research_title\",\"$research_page\",\"$research_no\",\"$research_link\",\"$research_tags\",\"$research_result_link\",\"$research_link_description\",\"$research_content\",\"$research_cache_uri\"";
	} else {
		require_once("common/tpl/AIR/_no_results.tpl");
	}

	if (isset($_POST["format"])){
		switch ($_POST["format"]){
			case "pdf":
				header("Content-type: application/pdf");
				break;
			case "csv":
				header("Content-type: application/csv; charset=utf-8");
				break;
			case "rdf":
				header("Content-type: application/rdf+xml; charset=utf-8");
				break;
		}
	}
	//header("Content-type: text/plain; charset=utf-8");header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename= " . $_POST["filename"] . "." . $_POST["format"]);
	header("Content-Transfer-Encoding: binary");
	
	print $content;
}
?>