<?php
/**
* List all uploaded files
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
* @package	AIRS_manage_files
* @author		Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../.mysql_connect.inc.php");
require_once("../_converti_data.php");

$pdo = db_connect("");

$page = isset($_POST["page"]) ? $_POST["page"] : 1;
$query = isset($_POST["query"]) ? $_POST["query"] : false;
$qtype = isset($_POST["qtype"]) ? $_POST["qtype"] : false;

$rp = isset($_POST['rp']) ? $_POST['rp'] : 5;
$start = (($page-1) * $rp);
$limit = "limit $start, $rp";

$files_list = $pdo->query("select * from `airs_rdf_files` limit $start, $rp");
$files_list_count = $pdo->query("select * from `airs_rdf_files`");
if($files_list->rowCount() > 0){
	$res["page"] = $page;
	$res["total"] = $files_list_count->rowCount();
	$tb_count = 0;
	while($dato_files = $files_list->fetch()){
		$tb_count++;
		$res["total_ids"][] = $dato_files["id"];
		$res["rows"][$tb_count]["id"] = $tb_count;
		$res["rows"][$tb_count]["cell"]["id"] = $dato_files["id"];
		$res["rows"][$tb_count]["cell"]["file"] = '<a href="./File:' . $dato_files["file"] . '" title="Vai al file">' . $dato_files["title"] . '</a>';
		$tags = explode(",", $dato_files["tag"]);
		$tags_list = "";
		foreach ($tags as $tag){
			$tags_list .= '<span class="tag">' . ucfirst($tag) . '</span>&nbsp;&nbsp;';
		}
		$res["rows"][$tb_count]["cell"]["tags"] = $tags_list;
		$res["rows"][$tb_count]["cell"]["date"] = converti_data(date("D, d M Y \a\l\l\e H:i:s", strtotime($dato_users["date"])), "it", "month_first", "short");
		
		
		if(strlen($dato_files["uploaded_by"]) > 0){
			$created_by = "<a href=\"./Utente/" . ucfirst(strtolower($dato_files["uploaded_by"])) . "\" title=\"Visualizza la scheda di questo utente\">" . ucfirst(strtolower($dato_files["uploaded_by"])) . "</a>";
		} else {
			$created_by = " - ";
		}
		$res["rows"][$tb_count]["cell"]["uploaded_by"] = $created_by;
	}
}
//print_r($res);
print json_encode($res);
?>