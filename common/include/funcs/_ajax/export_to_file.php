<?php
/**
* This script exports list of contents and generates download-forced file
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
header("Content-type: text/plain");

foreach($_GET as $pk => $pv){
	$_POST[$pk] = $pv;
}
if(isset($_POST)){
	require_once("../../.mysql_connect.inc.php");
	require_once("../_converti_data.php");
		
		/*
		// For debugging
		foreach($_GET as $p => $pv){
			$_POST[$p] = $pv;
		}
		*/
	$pdo = db_connect("");
	$pdo_db = db_connect(addslashes($_POST["db"]));

	$user_data = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($_POST["user"]) . "'");
	if($user_data->rowCount() > 0){
		while ($dato_user_data = $user_data->fetch()){
			$user_name = ucwords(strtolower($dato_user_data["name"] . " " . $dato_user_data["lastname"]));
			$user_email = strtolower($dato_user_data["email"]);
		}
	}
	$check_export_schema = $pdo_db->prepare("select * from `__export_data_schema` where `reference` = '" . addslashes($_POST["table"]) . "'");
	if($check_export_schema->execute()){
		while($dato_schema = $check_export_schema->fetch()){
			$table_head[] = "\"" . strtoupper($dato_schema["title"]) . "\"";
			$table_head_[] = strtoupper($dato_schema["title"]);
				$table_head_qp[] = "*" . strtoupper($dato_schema["title"]) . "*";
				$table_head_no_quote[] = "<b>" . strtoupper($dato_schema["title"]) . "</b>";
			
			if ($dato_schema["has_more_results"] == 0){
				$fields[] = $dato_schema["column"];
				$tables[] = $dato_schema["table"];
				$export_mode[]["simple"] = $dato_schema["column"];
			} else {
				$fields[] = $dato_schema["column"];
				$complex_fields[] = $dato_schema["column"];
				$tables[] = $dato_schema["table"];
				
				//$dato_schema["column"] = str_replace("`", "", $dato_schema["column"]);
				$export_mode[]["complex"] = $dato_schema["column"];
			}
		}
	} else {
		$table_data = $pdo_db->query("show full columns from `" . addslashes($_POST["table"]) . "`");
		if ($table_data->rowCount() > 0){
			while($dato_table = $table_data->fetch()){
				if(strlen(trim($dato_table["Comment"])) > 0){
					$table_head[] = "\"" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "\"";
					$table_head_[] = strtoupper(strstr($dato_table["Comment"], "::", true));
						$table_head_qp[] = "*" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "*";
						$table_head_no_quote[] = "<b>" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "</b>";
					$fields[] = $dato_table["Field"];
					$tables[] = $_POST["table"];
				}
			}
		}
	}
	switch($_POST["format"]){
		case "mail":
			header("Content-type: text/plain");
			
			$results_qp .= "| " . implode(" | ", $table_head_qp) . " |\r\n";
		case "pdf":
			require_once("../../lib/FPDF/get_pdf_of_page.php");
			
			$results_no_quote = "<TABLE>\r\n<TR>\r\n<TD>" . implode("</TD>\r\n<TD>", $table_head_no_quote) . "</TD>\r\n</TR>\r\n";
			break;
		case "txt":
			header("Content-type: application/force-download");
			header("Content-Disposition: attachment; filename=" . $_POST["filename"] . "." . $_POST["format"]);
			
			$results_qp .= "| " . implode(" | ", $table_head_qp) . " |\r\n";
			break;
		case "csv":
			header("Content-type: application/force-download; charset=utf-8");
			header("Content-Disposition: attachment; filename=" . $_POST["filename"] . "." . $_POST["format"]);
			
			print implode(";", $table_head) . "\r\n";
			break;
		case "rdf":
			header("Content-type: application/rdf+xml; charset=utf-8");
			header("Content-Disposition: attachment; filename=" . $_POST["filename"] . "." . $_POST["format"]);
			
			break;
		case "xls":
			header("Content-type: application/vnd.ms-excel; charset=utf-8");
			header("Content-Disposition: attachment; filename=" . $_POST["filename"] . "." . $_POST["format"] . "x");
			
			break;
	}

	$tables_arr = array_unique($tables);
	$tables_ = "`" . implode("` join `", $tables_arr) . "`";

	$test = $pdo_db->prepare("select * from " . addslashes($tables_) . " order by " . addslashes($_POST["table"]) . ".data desc, " . addslashes($_POST["table"]) . ".ora desc");
	if(!$test->execute()){
		$test = $pdo_db->prepare("select * from " . addslashes($tables_) . " order by " . addslashes($_POST["table"]) . ".last_insert_date desc");
	}
	if(!$test->execute()){
		$test = $pdo_db->prepare("select air_research_results.*, air_research_results.id as 'the_id', air_research_results.tags as 'the_tags', air_research.* from " . addslashes($tables_) . " on air_research_results.research_id = air_research.id order by air_research_results.id");
	}
	$row_count = 0;
	$the_ids = explode(",", $_POST["ids"]);
	while ($dato_test = $test->fetch()){
		if(in_array($dato_test["the_id"], $the_ids)){
			$row_count++;
			foreach($fields as $field){
				switch($field){
					case "data":
					case "date":
						$value = converti_data(date("D, d M Y", strtotime($dato_test[$field])), "it", "month_first", "short");
						break;
					case "user":
						$value = ucfirst($dato_test[$field]);
						break;
					case "result_entire_html_content":
					case "result_cache_html_content":
						$value = mb_convert_encoding(quoted_printable_decode(htmlentities($dato_test[$field])), "UTF-8", mb_detect_encoding($dato_test[$field]));
						break;
					default:
						$value = mb_convert_encoding(quoted_printable_decode(str_replace("|", "¦", $dato_test[$field])), "UTF-8", mb_detect_encoding($dato_test[$field]));
						break;
				}
				if(is_array($export_mode)){
					foreach($export_mode as $exp_order => $export_data){
						foreach($export_data as $exp_type => $export_col){
							if($exp_type == "simple"){
								$value = mb_convert_encoding(quoted_printable_decode(htmlentities($dato_test[$export_col])), "UTF-8", mb_detect_encoding($dato_test[$export_col]));
								$res_[$row_count][$exp_order] = $value;
								$res[$row_count][] = "\"" . $value . "\"";
								$res_no_quote[$row_count][] = "<TD>" . $value . "</TD>";
							} else {
								$ert = $export_col;
								eval('$res_[$row_count][$exp_order] = ' . $ert . ';');
								eval('$res[$row_count][] = \'"\' . ' . $ert . '. \'"\';');
								eval('$res_no_quote[$row_count][] = \'"<TD>"\' . ' . $ert . ' . \'"</TD>"\';');
							}
						}
					}
				} else {
					$res_[$row_count][] = $value;
					$res[$row_count][] = "\"" . $value . "\"";
					$res_no_quote[$row_count][] = "<TD>" . $value . "</TD>";
				}
			}
			$result[] = implode(";", $res[$row_count]);
			$result_qp[] = "| " . implode(" | ", $res[$row_count]) . " |";
			$result_no_quote[] = implode("\r\n", $res_no_quote[$row_count]);
		}
	}
	$results .= implode("\r\n", $result);
	$results_qp .= implode("\r\n", $result_qp);
	$results_no_quote .= "<TR>\r\n" . implode("\r\n</TR>\r\n<TR>\r\n", $result_no_quote) . "\r\n</TABLE>";
	switch($_POST["format"]){
		case "mail":
			require_once("../mail.send.php");
			$body = "Ciao, è stato richiesto l'invio del seguente registro dal portale AIRS.\nCome da oggetto, il tipo di archivio è: \"" . $_POST["title"] . "\"\n\nA seguire la tabella con i dati registrati:\n\n" . str_repeat("-", 100) . "\n\n" . $results_qp;
			send_mail($_POST["dest"], $_POST["title"], $body);
			break;
		case "pdf":
			//print $results_no_quote;
			pdf($_POST["title"], utf8_decode(nl2br($results_no_quote)), $_POST["filename"]);
			break;
		case "txt":
			print utf8_decode(mb_convert_encoding(stripslashes($results_qp), "ISO-8859-1", "HTML-ENTITIES"));
			break;
		case "csv":
			print $results;
			break;
		case "rdf":
			require_once ('XML/Serializer.php');
			$options = array(
				"indent"     => "    ",
				"linebreak"  => "\n",
				"addDecl"    => true,
				"addDoctype" => false,
				"doctype"    => array(
					'uri' => 'http://airs.inran.it/dtd/pear/package-1.0.dtd',
					'id'  => '-//PHP//PEAR/DTD PACKAGE 0.1'
				)
			);
			$title = $_POST["title"];
			$rdf = <<<RDF
	<?xml version="1.0"?>
	<rdf:RDF
	  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
	  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#"
	  xmlns:owl="http://www.w3.org/2002/07/owl#">

	<owl:Ontology rdf:about="http://www.w3.org/2000/01/rdf-schema#"/>

	<rdf:Description rdf:about="http://airs.inran.it/AIR/Risultati_delle_ricerche">
		<research:title>$title</research:title>
		<research:link>http://airs.inran.it/AIR/Risultati_delle_ricerche</research:link>\n
RDF;
			foreach($table_head_ as $id => $head_title){
				foreach($res_ as $idd => $elem){
					$item[$idd][$table_head_[$id]] = $elem[$id];
				}
			}
			foreach($item as $item_no => $item_data){
				$rdf .= "	<research:result>\n";
					foreach($item_data as $item_id => $item_description){
						$rdf .= "		<rdf:Description rdf:ID=\"" . strtolower($item_id) . "\">\n";
							$item_description = stripslashes(trim($item_description));
							$item_description = mb_convert_encoding(html_entity_decode($item_description), "UTF-8", mb_detect_encoding($item_description));
							$item_description = mb_convert_encoding($item_description, "UTF-8", "HTML-ENTITIES");
							$item_description = utf8_decode(preg_replace("/[\r\n]+/", "\\\\r\\\\n", $item_description));
						$rdf .= "			<rdf:type rdf:resource=\"" . $item_description . "\" />\n";
						$rdf .= "		</rdf:Description>\n";
					}
				$rdf .= "	</research:result>\n";
			}
			$rdf .= "</rdf:Description>\n";
			$rdf .= "</rdf:RDF>";
			
			print $rdf;
			//print_r($item);
			break;
		case "xls":
			/*
			require_once("Spreadsheet/Excel/Writer.php");
			$workbook = new Spreadsheet_Excel_Writer();
			$workbook->setVersion(8);
			
			$format_bold =& $workbook->addFormat();
			$format_bold->setBold();

			$format_title =& $workbook->addFormat();
			$format_title->setItalic();
			$format_title->setColor("grey");
			// let's merge
			$format_title->setAlign("merge");

			$worksheet =& $workbook->addWorksheet("Esportazione dei dati del database");
			$worksheet->setInputEncoding('UTF-8');

			//$worksheet->write(0, 0, "Quarterly Profits for Dotcom.Com", $format_title);
			// Couple of empty cells to make it look better
			//$worksheet->write(0, 1, "", $format_title);
			//$worksheet->write(0, 2, "", $format_title);
			foreach($table_head_ as $c => $head){
				$worksheet->write(0, $c, $head, $format_bold);
				if($c > 0){
					foreach($res_ as $r => $content){
						foreach($content as $s => $content_){
							$content__ = stripslashes($content[$s]);
							$content__ = html_entity_decode($content__, ENT_QUOTES, "UTF-8");
							$content___ = utf8_decode(utf8_decode(mb_convert_encoding($content__, "UTF-8", mb_detect_encoding($content__))));
							//print $r . " ~ " . $s . " ~ " . $content___ . "\n";
							$worksheet->write($r, $s, $content___);
						}
					}
				}
			}
			//print_r($res_);
			//exit();
			$workbook->send($_POST["filename"] . "." . $_POST["format"]);
			$workbook->close();
			*/
			//error_reporting(E_ALL);
			require_once("PHPExcel/PHPExcel.php");
			require_once("PHPExcel/PHPExcel/Writer/Excel2007.php");
			$objPHPExcel = new PHPExcel();
			
			// Set properties
			//echo date('H:i:s') . " Set properties\n";
			$objPHPExcel->getProperties()->setCreator($user_name);
			$objPHPExcel->getProperties()->setLastModifiedBy($user_name);
			$objPHPExcel->getProperties()->setTitle($_POST["title"]);
			$objPHPExcel->getProperties()->setSubject($_POST["title"]);
			$objPHPExcel->getProperties()->setDescription("Esportazione dei dati del database");
			
			// Add some data
			$objPHPExcel->setActiveSheetIndex(0);
			$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
			foreach($table_head_ as $c => $head){
				$objPHPExcel->getActiveSheet()->freezePane($alphabet[$c] . "2");
				
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFont()->setSize(10);
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getBottom()->getColor()->setARGB("FF666666");
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
				$objPHPExcel->getActiveSheet()->getColumnDimension($alphabet[$c])->setWidth(15);
				$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(18);
				$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFF0F0F0");
					$objPHPExcel->getActiveSheet()->SetCellValue($alphabet[$c] . "1", $head);
				
				if($c > 0){
					foreach($res_ as $r => $content){
						$r+=1;
						foreach($content as $s => $content_){
							$content__ = stripslashes($content[$s]);
							$content__ = html_entity_decode($content__, ENT_QUOTES, "UTF-8");
							$content___ = utf8_decode(utf8_decode(mb_convert_encoding($content__, "UTF-8", mb_detect_encoding($content__))));
							$objPHPExcel->getActiveSheet()->SetCellValue($alphabet[$s] . $r, $content___);
						}
					}
				}
			}
			// Sheet options
			$objPHPExcel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
			$objPHPExcel->getActiveSheet()->setTitle("Esportazione del database AIRS");
			// Save Excel 2007 file
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			$objWriter->save("php://output");
			
			break;
	}
}
?>