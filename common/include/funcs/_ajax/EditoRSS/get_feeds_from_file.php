<?php
/**
* Retrieve feeds from file
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
require_once("../../_create_link.php");
require_once("../../_check_valid_uri.php");
$pdo = db_connect("editorss");

$file_info = pathinfo($_GET["file"]);
$file_ext = $file_info["extension"];
$filepath = "../../../../../.tmp/files/" . $_GET["file"];
$path = "../../../../../.tmp/files/";

switch($file_ext) {
	case "csv":
		require_once("File/CSV.php");
		
		$salvable = 0;
		$edited = 0;
		$config = File_CSV::discoverFormat($filepath);
		while ($res = File_CSV::readQuoted($filepath, $config)) {
			$file[] = $res;
		}
		$c = 0;
		for ($i = 1; $i <= count($file); $i++){
			$c++;
			foreach($file as $k => $v){
				if ($c > 0){
					if (is_array($file[$c])){
						foreach($file[$c] as $kk => $vv){
							if (strlen(trim($file[0][$kk])) > 0){
								switch(strtolower(str_replace(" ", "_", $file[0][$kk]))){
									case "tipo":
									case "tematica":
										$vv = utf8_decode(ucfirst(strtolower($vv)));
										break;
									case "url":
										$vv = $vv;
										break;
									default:
										$vv = utf8_decode($vv);
								}
								$parsed_csv[strtolower(str_replace(" ", "_", $file[0][$kk]))] = $vv;
							}
						}
					}
				}
			}
			$parsed[] = $parsed_csv;
		}
		$file = array("file" => $parsed);
			
		break;
	case "xml":
	case "rss":
		// Controlla che l'xml inviato sia un feed
		$_GET["type"] = "test";
		$_GET["uri"] = "http://airs.inran.it/.tmp/files/" . $_GET["file"];
		ob_start();
		require_once("check_save_show_rss.php");
		$is_valid = ob_get_clean();
		// Se è un feed
		if($is_valid !== "null" && $is_valid !== ""){
			$parsed["file"][0]["nome_feed_rss"] = $file_info["filename"];
			$parsed["file"][0]["url"] = stripslashes($_GET["uri"]);
			$file = $parsed;
		} else {
			// Diversamente si tratta di un file xml contenente feeds
			// In questo caso va parsato
			//ini_set('display_errors', 1);
			require_once("XML/Unserializer.php");
			$fp = fopen($filepath, "r");
			$xml_content = fread($fp, filesize($filepath));
			fclose($fp);
			
			$string = preg_replace('/\s{2,}/', '', $xml_content);
			$string = preg_replace('/\n/', '', $string);
			
			$options = array(
				'complexType' => 'array',
				'parseAttributes' => true,
				'whitespace' => '_WHITESPACE_NORMALIZE'
			);
			$unserializer = &new XML_Unserializer($options);
			$us = $unserializer->unserialize($xml_content, false);
			if (PEAR::isError($us)) {
				print "Error: " . $status->getMessage();
			} else {
				$kf = -1;
				$data = $unserializer->getUnserializedData();
				foreach($data["body"]["outline"] as $k2 => $v2){
					$kf++;
					foreach($data["body"]["outline"][$k2] as $k3 => $v3){
						if(!array_key_exists("outline", $v2)){
							switch($k3){
								case "text":
									$parsed_csv[$kf]["nome_feed_rss"] = utf8_decode($v3);
									break;
								case "xmlUrl":
									$parsed_csv[$kf]["url"] = $v3;
									break;
							}
						} else {
							if(is_array($data["body"]["outline"][$k2][$k3])){
								$kf2 = $kf;
								foreach($data["body"]["outline"][$k2][$k3] as $k4 => $v4){
									$kf2++;
									foreach($data["body"]["outline"][$k2][$k3][$k4] as $k5 => $v5){
										switch($k5){
											case "text":
												$parsed_csv[$kf2]["nome_feed_rss"] = utf8_decode($v5);
												break;
											case "xmlUrl":
												$parsed_csv[$kf2]["url"] = $v5;
												break;
										}
									}
								}
							}
						}
					}
				}
				foreach($parsed_csv as $arr){
					$parsed[] = $arr;
				}
			}
			$file = array("file" => $parsed);
			//$file = array("xml" => $parsed_xml);
		}
		break;
		
	case "pdf":
		shell_exec("pdftotext -eol unix -layout " . $path . $file_info["basename"] . " " . $path . $file_info["filename"] . ".txt");
		if(is_file($path . $file_info["filename"] . ".txt")){
			$fh = fopen($path . $file_info["filename"] . ".txt", 'r');
			$theData .= utf8_decode(fread($fh, filesize($path . $file_info["filename"] . ".txt")));
			fclose($fh);
			$urlregex = "^(https?|ftp)\:\/\/([a-z0-9+!*(),;?&=\$_.-]+(\:[a-z0-9+!*(),;?&=\$_.-]+)?@)?[a-z0-9+\$_-]+(\.[a-z0-9+\$_-]+)*(\:[0-9]{2,5})?(\/([a-z0-9+\$_-]\.?)+)*\/?(\?[a-z+&\$_.-][a-z0-9;:@/&%=+\$_.-]*)?(#[a-z_.-][a-z0-9+\$_.-]*)?\$";
			$lines = explode("\n", $theData);
			foreach($lines as $line){
				$col[] = preg_split("/[\s]+/", $line);
			}
			$ccc = 0;
			foreach($col as $ck => $colums){
				foreach($colums as $ckk => $cols){
					if (eregi($urlregex, $cols)) {
						$ccc++;
						$parsed_pdf[]["url"] = $cols;
					}
				}
			}
			$file = array("file" => $parsed_pdf);
		}
		break;
}
//print_r($file);
print json_encode($file);
?>