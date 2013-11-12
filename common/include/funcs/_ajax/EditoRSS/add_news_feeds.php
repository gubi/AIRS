<?php
/**
* Add news from feed to EditoRSS database
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
require_once("../../calculate_tags.php");
require_once("../../../.mysql_connect.inc.php");

$pdo = db_connect("editorss");

$add == false;
$added == false;
for ($i = 1; $i <= $_POST["counter"]; $i++){
	if (trim($_POST["title_" . $i]) == ""){
		print "error:È necessario inserire il titolo della news " . $i . ":" . $i . ":title_" . $i;
		$add = false;
		$added = false;
		break;
	} else if (trim($_POST["description_" . $i]) == ""){
		print "error:È necessario inserire il corpo della news " . $i . ":" . $i . ":desc_" . $i;
		$add = false;
		$added = false;
		break;
	} else if (trim($_POST["link_" . $i]) == ""){
		print "error:È necessario inserire il link della news " . $i . ":" . $i . ":link_" . $i;
		$add = false;
		$added = false;
		break;
	} else if (trim($_POST["date_" . $i]) == ""){
		print "error:È necessario inserire la data della news " . $i . ":" . $i . ":date_" . $i;
		$add = false;
		$added = false;
		break;
	} else {
		$add = true;
		$added = true;
	}
}
if ($add == false || $added == false){
	exit();
} else {
	for ($i = 1; $i <= $_POST["counter"]; $i++){
		// Pulizia del link
		$_POST["link_" . $i] = str_replace(array(
									"http://news.google.com/news/url?sa=t&fd=R&usg=AFQjCNHHBxp5B0wQL9t41nVDQFC_Qe83HQ&url=" // Rimuove i redirects di google
									), 
									array(""),
									$_POST["link_" . $i]);
		// Vede se la news è stata già salvata
		$check_news = $pdo->query("select * from `editorss_feeds_news` where `link` = '" . addslashes($_POST["link_" . $i]) . "'");
		if ($check_news->rowCount() > 0){
			while ($dato_check_news = $check_news->fetch()) {
				$the_id = $dato_check_news["id"];
			}
			// In tal caso lo aggiorna
				// Se la descrizione non c'è mantiene quella precedente
				if (strlen(trim($_POST["description_" . $i])) == 0){
					$with_description = "";
				} else {
					$with_description = "`description` = ('" . addslashes($_POST["description_" . $i]) . "'), ";
				}
			
			$tags = trim($_POST["tags_" . $i]);
			$edit_news =  $pdo->prepare("update `editorss_feeds_news` set `title` = ('" . utf8_decode(addslashes($_POST["title_" . $i])) . "'), " . $with_description . " `date` = ('" . utf8_decode(addslashes($_POST["date_" . $i])) . "'), `link` = ('" . utf8_decode(addslashes($_POST["link_" . $i])) . "'), `parent_id` = ('" . utf8_decode(addslashes($_POST["parent_id_" . $i])) . "'), `tags` = ('" . addslashes($tags) . "'), `user` = ('" . addslashes($GLOBALS["decrypted_user"]) . "') where `id` = '" . addslashes($the_id) . "'");
			if (!$edit_news->execute()) {
				$act = "error:Si &egrave; verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
			} else {
				$act = "edited:" . $the_id;
			}
		} else {
			$add_feed = $pdo->prepare("insert into `editorss_feeds_news` (`title`, `description`, `date`, `link`, `parent_id`, `tags`, `user`) values(?, ?, ?, ?, ?, ?, ?)");
			$add_feed->bindParam(1, addslashes(trim($_POST["title_" . $i])));
			$add_feed->bindParam(2, strip_tags(utf8_decode(addslashes(trim($_POST["desc_" . $i])))));
			$add_feed->bindParam(3, addslashes(trim($_POST["date_" . $i])));
			$add_feed->bindParam(4, addslashes(trim($_POST["link_" . $i])));
			$add_feed->bindParam(5, addslashes($_POST["parent_id"]));
			if (strlen(trim($_POST["tags_" . $i])) == 0){
				$tags = implode(",", calculate_tags(strip_tags(utf8_encode(utf8_decode(str_replace(array("\n+", "\r+", "\r\n"), " ", trim($_POST["title_" . $i]) . " " . trim($_POST["desc_" . $i])))))));
			} else {
				$tags = trim($_POST["tags_" . $i]);
			}
			$add_feed->bindParam(6, addslashes($tags));
			$add_feed->bindParam(7, addslashes($_POST["decrypted_user"]));
			if ($add_feed->execute()){
				$added = true;
			} else {
				//print $pdo->errorCode();
				print "error:Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
				break;
				$added = false;
			}
		}
	}
}
if ($added == true){
	print "added";
}
?>