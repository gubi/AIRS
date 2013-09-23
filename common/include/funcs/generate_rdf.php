<?php
/**
* Generate file RDF
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
header("Content-Type: application/rdf+xml");
require_once("../.mysql_connect.inc.php");

if(isset($_GET["id"]) && trim($_GET["id"]) !== ""){
	$pdo = db_connect("");
	$rdf_data = $pdo->query("select * from `airs_rdf_files` where `id` = '" . addslashes($_GET["id"]) . "'");
	while($dato_rdf = $rdf_data->fetch()){
		require_once("XML/Serializer.php");
		
		$options = array(
			"indent" => "    ",
			"linebreak" => "\n",
			"typeHints" => false,
			"addDecl" => true,
			"encoding" => "UTF-8",
			"rootName" => "rdf:RDF",
			"rootAttributes" => array(
								"xmlns:rdf" => "http://www.w3.org/1999/02/22-rdf-syntax-ns#",
								"xmlns:rdfs" => "http://www.w3.org/2000/01/rdf-schema#",
								"xmlns:dc" => "http://purl.org/dc/elements/1.1/",
								"xmlns:dcterms" => "http://purl.org/dc/terms/",
								"xmlns:dcam" => "http://purl.org/dc/dcam/",
								"xmlns:foaf" => "http://xmlns.com/foaf/0.1/"
							),
			"defaultTagName" => "rdf:value",
			"attributesArray" => "_attributes"
		);
		$serializer = new XML_Serializer($options);
			$authors_uri = array_map("trim", explode(" - ", $dato_rdf["author_uri"]));
			foreach(explode("&", $dato_rdf["author"]) as $ak => $author){
				$authors["foaf:Person"][] = array(
					trim($author),
					"foaf:uri" => array("_attributes" => array("rdf:resource" => $authors_uri[$ak])),
					"foaf:reference" => array(
						"_attributes" => array("rdf:resource" => $dato_rdf["author_entity_uri"]),
						$dato_rdf["author_entity"]
					)
				);
			}
			$info = pathinfo($dato_rdf["file_path"]);
		
		$rdf = array(
			"rdf:Description" => array(
				"_attributes" => array("about" => "https://airs.inran.it/File:" . $dato_rdf["file"]),
				"dc:Title" => $dato_rdf["title"],
				"dc:Author" => array_filter($authors),
				"dc:Description" => preg_replace("#\s+#", " ", str_replace(array("\n", "\r", "\r\n"), " ", $dato_rdf["description"])),
				"dc:Tags" => $dato_rdf["tag"],
				"dc:Source" => array(
					"_attributes" => array("rdf:resource" => $dato_rdf["origins_uri"]),
					$dato_rdf["origins"]
				),
				"dc:License" => array(
					"_attributes" => array("rdf:resource" => $dato_rdf["license_uri"]),
					$dato_rdf["license"]
				),
				"dc:Date" => $dato_rdf["date"],
				"dc:Type" => $info["extension"],
				"dc:Format" => mime_content_type($_SERVER["DOCUMENT_ROOT"] . $dato_rdf["file_path"])
				
			)
		);
		$result = $serializer->serialize($rdf);
		if($result === true) {
			// fix for IE catching or PHP bug issue
			/*header("Pragma: public");
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			
			header("Content-Type: application/octet-stream");
			header("Content-Type: application/rdf+xml");
			header("Content-Disposition: attachment; filename=\"" . $dato_rdf["file"] . ".rdf\";");
			header("Cache-Control: public");
			header("Content-Description: File Transfer");
			header("Content-Transfer-Encoding: Binary");
			*/
			print html_entity_decode($serializer->getSerializedData());
		}
	}
}
?>