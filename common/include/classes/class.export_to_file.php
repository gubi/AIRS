<?php
/**
* Convert all data to file
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

class Export {
	public $params;
	
	public function __construct($params) {
		$this->params = $params;
		$this->pdo = $this->db_connect("");
		$this->pdo_db = $this->db_connect(addslashes($this->params["db"]));
	}
	private function db_connect($db) {
		require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/.mysql_connect.inc.php");
		return db_connect($db);
	}
	private function get_error($rsssage) {
		$this->error = true;
		$this->head();
		print "error:<h1>Ouch!</h1>\n<b>" . ucfirst($rsssage) . "!</b><br />\nPer favore, inviaci un <a href=\"javascript:void(0);\" id=\"feedback_btn\" title=\"Riporta un feedback\">feedback</a> per risolvere il problema.";
		exit();
	}
	private function set_format($format) {
		$this->format = $format;
	}
	private function head() {
		if(!$this->error) {
			switch($this->format){
				case "mail":
					header("Content-type: text/plain");
					
					break;
				case "pdf":
					header("Content-type: application/force-download");
					header("Content-type: application/pdf");
					header("Content-Disposition: attachment; filename=" . $this->params["filename"] . "." . $this->format);
					
					break;
				case "txt":
					header("Content-type: application/force-download");
					header("Content-Disposition: attachment; filename=" . $this->params["filename"] . "." . $this->format);
					
					break;
				case "csv":
					header("Content-type: application/force-download; charset=utf-8");
					header("Content-Disposition: attachment; filename=" . $this->params["filename"] . "." . $this->format);
					
					break;
				case "rdf":
					//header("Content-type: application/rdf+xml; charset=utf-8");
					//header("Content-Disposition: attachment; filename=" . $this->params["filename"] . "." . $this->format);
					header("Content-type: text/plain; charset=utf-8");
					
					break;
				case "xls":
					header("Content-type: application/vnd.ms-excel; charset=utf-8");
					header("Content-Disposition: attachment; filename=" . $this->params["filename"] . "." . $this->format . "x");
					
					break;
			}
		} else {
			header("Content-type: text/plain; charset=utf-8");
		}
	}
	private function set_header(){
		if(strlen(trim($this->params["db"])) == 0){
			$this->get_error("non è stato impostato un database di riferimento");
		}
		if(strlen(trim($this->params["table"])) == 0){
			$this->get_error("non è stata impostata una tabella di riferimento");
		}
		
		$user_data = $this->pdo->query("select * from `airs_users` where `username` = '" . addslashes($this->params["user"]) . "'");
		if($user_data->rowCount() > 0){
			while ($dato_user_data = $user_data->fetch()){
				$this->user_name = ucwords(strtolower($dato_user_data["name"] . " " . $dato_user_data["lastname"]));
				$this->user_email = strtolower($dato_user_data["email"]);
			}
		} else {
			$this->get_error(((strlen(trim($_POST["username"])) > 0) ? "impossibile connettersi al database utenti" : "a quale utente afferisce l'esportazione?")); 
		}
		$check_export_schema = $this->pdo_db->prepare("select * from `__export_data_schema` where `reference` = '" . addslashes($this->params["table"]) . "' order by `order` asc");
		if($check_export_schema->execute()){
			if($check_export_schema->rowCount() > 0){
				while($dato_schema = $check_export_schema->fetch()){
					$this->table_head[] = "\"" . strtoupper($dato_schema["title"]) . "\"";
					$this->table_head_[] = strtoupper($dato_schema["title"]);
					$this->table_head_size[] = (int)$dato_schema["size"];
					$this->table_head_txt[] = "*" . strtoupper($dato_schema["title"]) . "*";
					$this->table_head_no_quote[] = "<b>" . strtoupper($dato_schema["title"]) . "</b>";
					
					if ($dato_schema["has_more_results"] == 0){
						$this->fields[] = $dato_schema["column"];
						$this->tables[] = $dato_schema["table"];
						$this->export_mode[]["simple"] = $dato_schema["column"];
					} else {
						$this->fields[] = $dato_schema["column"];
						$this->complex_fields[] = $dato_schema["column"];
						$this->tables[] = $dato_schema["table"];
						
						//$dato_schema["column"] = str_replace("`", "", $dato_schema["column"]);
						$this->export_mode[]["complex"] = $dato_schema["column"];
					}
				}
			} else {
				$table_data = $this->pdo_db->prepare("show full columns from `" . addslashes($this->params["table"]) . "`");
				if($table_data->execute()) {
					if ($table_data->rowCount() > 0){
						while($dato_table = $table_data->fetch()){
							if(strlen(trim($dato_table["Comment"])) > 0){
								$this->table_head[] = "\"" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "\"";
								$this->table_head_[] = strtoupper(strstr($dato_table["Comment"], "::", true));
								$this->table_head_size[] = strstr($dato_table["Comment"], "::");
									$this->table_head_txt[] = "*" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "*";
									$this->table_head_no_quote[] = "<b>" . strtoupper(strstr($dato_table["Comment"], "::", true)) . "</b>";
								$this->fields[] = $dato_table["Field"];
								$this->tables[] = $this->params["table"];
							}
						}
					} else {
						$this->get_error("nessuna colonna da mostrare"); 
					}
				} else {
					$this->get_error(((strlen(trim($this->params["table"])) > 0) ? "la tabella di riferimento è sbagliata: non posso acquisire i dati dal database" : "la tabella di riferimento non esiste: non posso acquisire i dati dal database")); 
				}
			}
			switch($this->format){
				case "mail":
					$this->results_txt .= "| " . implode(" | ", $this->table_head_txt) . " |\r\n";
					
					break;
				case "pdf":
					require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/lib/FPDF/get_pdf_of_page.php");
					$this->results_no_quote = "<TABLE>\r\n<TR>\r\n<TD>" . implode("</TD>\r\n<TD>", $this->table_head_no_quote) . "</TD>\r\n</TR>\r\n";
					
					break;
				case "txt":
					$this->header = "| " . implode(" | ", $this->table_head) . " |\r\n";
					
					break;
				case "csv":
					
					break;
				case "rdf":
					
					break;
				case "xls":
					
					break;
			}
		} else {
			$this->get_error("non è stato trovato nessuno schema di esportazione! È necessario crearne uno nella tabella `__export_data_schema`.\n");
		}
	}
	private function set_content() {
		//print_r($this->params);
		require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/funcs/_converti_data.php");
		
		$tables_arr = array_unique($this->tables);
		$tables_ = "`" . implode("` join `", $tables_arr) . "`";
		
		$t_query = "select *, " . addslashes($tables_) . ".id as 'the_id' from " . addslashes($tables_) . " order by " . addslashes($this->params["table"]) . ".data desc, " . addslashes($this->params["table"]) . ".ora desc";
		$test = $this->pdo_db->prepare($t_query);
		if(!$test->execute()){
			$t_query = "select *, " . addslashes($tables_) . ".id as 'the_id' from " . addslashes($tables_) . " order by " . addslashes($this->params["table"]) . ".last_insert_date desc";
		}
		$test = $this->pdo_db->prepare($t_query);
		if(!$test->execute()){
			$t_query = "select * from " . addslashes($tables_);
		}
		$test = $this->pdo_db->prepare($t_query);
		if(!$test->execute()){
			$this->get_error("impossibile generare il contenuto!\nLa query: '" . $t_query . "' è sbagliata");
		}
		if(is_array($this->params["ids"]) && count($this->params["ids"]) > 1){
			$the_ids = $this->params["ids"];
		} else {
			if($this->params["ids"][0] == "") {
				if($test->rowCount() > 0) {
					$test_ = $this->pdo_db->query($t_query);
					while ($dato_test_ = $test_->fetch()){
						$the_ids[] = $dato_test_["the_id"];
					}
				} else {
					$this->get_error("nessun risultato per questa query");
				}
			} else {
				$the_ids[] = $this->params["ids"][0];
			}
		}
		if($test->rowCount() > 0) {
			$row_count = -1;
			while ($dato_test = $test->fetch()){
				if(in_array($dato_test["the_id"], $the_ids)){
					$row_count++;
					foreach($this->fields as $field){
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
						$fv = -1;
						if(is_array($this->export_mode)){
							foreach($this->export_mode as $exp_order => $export_data){
								$fv++;
								foreach($export_data as $exp_type => $export_col){
									if($exp_type == "simple"){
										$value = mb_convert_encoding(quoted_printable_decode(htmlentities($dato_test[$export_col])), "UTF-8", mb_detect_encoding($dato_test[$export_col]));
										$this->res_[$row_count][$exp_order] = $value;
										$this->res[$row_count][$fv] = "\"" . $value . "\"";
										$this->res_no_quote[$row_count][] = "<TD>" . $value . "</TD>";
									} else {
										$ert = $export_col;
										eval('$this->res_[$row_count][$exp_order] = ' . $ert . ';');
										eval('$this->res[$row_count][$fv] = \'"\' . ' . $ert . '. \'"\';');
										eval('$this->res_no_quote[$row_count][] = \'"<TD>"\' . ' . $ert . ' . \'"</TD>"\';');
									}
								}
							}
						} else {
							$fv++;
							$this->res_[$row_count][] = $value;
							$this->res[$row_count][$fv] = "\"" . $value . "\"";
							$this->res_no_quote[$row_count][] = "<TD>" . $value . "</TD>";
						}
					}
					$this->result[] = implode(";", $this->res[$row_count]);
					$this->result_txt[] = "| " . implode(" | ", $this->res[$row_count]) . " |";
					$this->result_no_quote[] = implode("\r\n", $this->res_no_quote[$row_count]);
				}
			}
			
			$results .= implode("\r\n", $this->result);
			$results_txt .= implode("\r\n", $this->result_txt);
			$results_no_quote .= "<TR>\r\n" . implode("\r\n</TR>\r\n<TR>\r\n", $this->result_no_quote) . "\r\n</TABLE>";
			switch($this->format){
				case "mail":
					require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/funcs/mail.send.php");
					
					$body_txt = "Ciao, è stato richiesto l'invio del seguente report dal portale AIRS.\nCome da oggetto, il tipo di report è: \"" . $this->params["research_type"] . "\"\n\nIn allegato la tabella con i dati registrati";
					$body_html = 'Ciao, &egrave; stato richiesto l\'invio del seguente report dal portale AIRS.<br />Come da oggetto, il tipo di report &egrave;: "' . $this->params["research_type"] . '"<br /><br />In allegato la tabella con i dati registrati';
						$exp2 = new Export($this->params);
						ob_start();
						$exp2->to_file("txt");
						$file = ob_get_clean();
						
						$file_data = array(
									$file,
									"text/plain",
									$this->params["filename"] . ".txt"
								);
					send_mail($this->user_name . "<" . $this->user_email . ">", "Report: " . $this->params["research_type"], $body, $body_html, $file_data);
					
					break;
				case "pdf":
					pdf($this->params["title"], utf8_decode(nl2br($results_no_quote)), array($this->params["filename"]));
					
					break;
				case "txt":
					require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/classes/funcs/php_array_to_text_tables.php");
					
					foreach($this->res as $r_k => $r_v) {
						for ($i = 0; $i < (count($this->res[0]) - 1); $i++){
							$dd[$r_k][trim($this->table_head[$i], '"')] = html_entity_decode(mb_convert_encoding(stripslashes(trim($r_v[$i], '"')), "UTF-8", "HTML-ENTITIES"));
						}
					}
					$renderer = new ArrayToTextTable($dd);
					$renderer->showHeaders(true);
					$renderer->render();
					print "\n\n* Nota: questa tabella ha unicamente uno scopo rappresentativo dei dati esportati.\n  Se desideri i dati completi esportali in un altro formato di file.";
					
					break;
				case "csv":
					print $this->header;
					print html_entity_decode(mb_convert_encoding(stripslashes($results), "UTF-8", "HTML-ENTITIES"));
					
					break;
				case "rdf":
					$this->head();
					require_once("EasyRDF/autoload.php");
					
					EasyRdf_Namespace::set('rs', 'http://purl.org/INRAN/AIRS/');
					$graph = new EasyRdf_Graph();
					$rs = $graph->resource('http://airs.inran.it/EditoRSS', 'rs:Research');
					$rs->set('rs:type', $this->params["research_type"]);
					$rs->set('rs:filename', $this->params["filename"]);
					
					$rParams = $graph->newBnode('rs:Params');
					$rParams->set('rs:table', $this->params["table"]);
					$rs->set('rs:currentProject', $rParams);
					
					$rSelectedIDs = $graph->newBnode('rs:SelectedID');
					foreach($this->res as $r_k => $r_v) {
						for ($i = 0; $i < (count($this->res[0]) - 1); $i++){
							if(trim($this->table_head[$i], '"') == "ID"){
								$rSelectedIDs->add('rs:ID', trim($r_v[$i], '"'));
							}
							$dd[$r_k][trim($this->table_head[$i], '"')] = html_entity_decode(mb_convert_encoding(stripslashes(trim($r_v[$i], '"')), "UTF-8", "HTML-ENTITIES"));
						}
					}
					$rs->set('rs:Results', $rSelectedIDs);
					print_r($dd);
					/*
					foreach($this->res as $r_k => $r_v) {
						for ($i = 0; $i < (count($this->res[0]) - 1); $i++){
							$dd[$r_k][trim($this->table_head[$i], '"')] = html_entity_decode(mb_convert_encoding(stripslashes(trim($r_v[$i], '"')), "UTF-8", "HTML-ENTITIES"));
						}
					}
					*/
					$format = preg_replace("/[^\w\-]+/", '', strtolower("rdf"));
					$data = $graph->serialise("rdf");
					if (!is_scalar($data)) {
						$data = var_export($data, true);
					}
					print $data;
					
					break;
				case "xls":
					$this->head();
					require_once("PHPExcel/PHPExcel.php");
					require_once("PHPExcel/PHPExcel/Writer/Excel2007.php");
					$objPHPExcel = new PHPExcel();
					
					// Set properties
					$objPHPExcel->getProperties()->setCreator($this->user_name);
					$objPHPExcel->getProperties()->setLastModifiedBy($this->user_name);
					$objPHPExcel->getProperties()->setTitle($this->params["title"]);
					$objPHPExcel->getProperties()->setSubject($this->params["title"]);
					$objPHPExcel->getProperties()->setDescription("Esportazione dei dati del database");
					
					// Add some data
					$objPHPExcel->setActiveSheetIndex(0);
					$alphabet = array("A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z");
					//header("Content-type: text/plain");
					foreach($this->table_head_ as $c => $head) {
						$objPHPExcel->getActiveSheet()->freezePane($alphabet[$c] . "2");
						
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFont()->setBold(true);
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFont()->setSize(10);
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getTop()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getBottom()->getColor()->setARGB("FF666666");
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getLeft()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getBorders()->getRight()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)->getColor()->setARGB("FF666666");
						$objPHPExcel->getActiveSheet()->getColumnDimension($alphabet[$c])->setWidth((int)$this->table_head_size[$c]);
						$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(18);
						$objPHPExcel->getActiveSheet()->getStyle($alphabet[$c] . "1")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB("FFF0F0F0");
							$objPHPExcel->getActiveSheet()->SetCellValue($alphabet[$c] . "1", $head);
						
						foreach($this->res_ as $r => $content){
							foreach($content as $s => $content_){
								$content__ = stripslashes($content[$s]);
								$content__ = html_entity_decode($content__, ENT_QUOTES, "UTF-8");
								$content___ = html_entity_decode(mb_convert_encoding($content__, "UTF-8", mb_detect_encoding($content__)));
								
								$excel[$r][$alphabet[$s]][($r+2)] = $content___;
							}
						}
					}
					foreach ($excel as $item => $tailback){
						foreach($tailback as $col => $tailback_v) {
							foreach ($tailback_v as $row => $value) {
								$objPHPExcel->getActiveSheet()->getStyle($col . $row)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
								$objPHPExcel->getActiveSheet()->SetCellValue($col . $row, $value);
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
		} else {
			$this->get_error("c'è un errore con la query dei contenuti: quella attuale non  riporta risultati");
		}
	}
	
	private function generate() {
		$this->set_header();
		$this->set_content();
	}
	
	public function to_file($format) {
		$this->set_format($format);
		$this->generate();
		//return $format;
	}
}
//header("Content-type: text/plain; charset=utf-8");

foreach($_GET as $pk => $pv){
	$_POST[$pk] = $pv;
}
$params = array(
	"research_type" => $_POST["rtype"],
	"db" => $_POST["db"],
	"table" => $_POST["table"],
	"filename" => ((strlen(trim($_POST["filename"])) > 0) ? $_POST["filename"] : "AIRS~export_" . ((strlen(trim($_POST["table"])) !== 0) ? $_POST["table"] . "_-_" : "") . date("Y-m-d")),
	"ids" => array_unique(strpos($_POST["ids"], ",") ? array_values(array_filter(array_map("trim", explode(",", $_POST["ids"])))) : array($_POST["ids"])),
	"user" => $_POST["user"]
);
if($_GET["debug"] == "true"){
	print_r($params);
}
$exp = new Export($params);
print_r($exp->to_file("pdf"));
?>