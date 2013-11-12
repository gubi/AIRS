<?php
/**
* Add feeds to EditoRSS database
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
header("content-type: text/plain");
require_once("../../../.mysql_connect.inc.php");
require_once("../../_converti_data.php");

if (isset($_POST) || isset($_GET)){
	foreach($_POST as $key => $value){
		$GLOBALS[$key] = $value;
		if (is_array($value)){
			$GLOBALS[$key] = array_unique($value);
			$GLOBALS[$key] = implode(",", $GLOBALS[$key]);
		}
	}
	foreach($_GET as $key => $value){
		$GLOBALS[$key] = $value;
		if (is_array($value)){
			$GLOBALS[$key] = array_unique($value);
			$GLOBALS[$key] = implode(",", $GLOBALS[$key]);
		}
	}
	require_once("../../_converti_data.php");
	require_once("../../calculate_tags.php");
	$pdo = db_connect("editorss");
	
	// Vede se il feed è già stato salvato
	$check_feeds_group = $pdo->query("select * from `editorss_feeds` where `uri` = '" . addslashes($GLOBALS["rss_uri"]) . "'");
	if ($check_feeds_group->rowCount() > 0){
		while ($dato_check_feeds_group = $check_feeds_group->fetch()) {
			$the_id = $dato_check_feeds_group["id"];
			$date = converti_data(date("d M Y", strtotime($dato_check_feeds_group["last_insert_date"])), "it", "month_first", "short");
			$user = strtolower($dato_check_feeds_group["user"]);
		}
		// In tal caso lo aggiorna
			// Se la descrizione non c'è mantiene quella precedente
			if (strlen(trim($GLOBALS["rss_description"])) == 0){
				$with_description = "";
			} else {
				$with_description = ", `description` = ('" . utf8_decode(addslashes($GLOBALS["rss_description"])) . "')";
			}
			$edit_feed_group =  $pdo->prepare("update `editorss_feeds` set `group` = ('" . utf8_decode(addslashes($GLOBALS["rss_group"])) . "'), `title` = ('" . utf8_decode(addslashes($GLOBALS["rss_title"])) . "'), `tags` = ('" . addslashes($GLOBALS["rss_tag"]) . "')" . $with_description . " where uri = '" . addslashes($GLOBALS["rss_uri"]) . "'");
			if (!$edit_feed_group->execute()) {
				$act = "error:Si &egrave; verificato un errore durante il salvataggio: _<br />" . $pdo->errorCode();
			} else {
				// Se l'utente che ne chiede il salvataggio è diverso da quello già salvato
				// allora lo duplica per lui
				if($user !== strtolower($GLOBALS["decrypted_user"])){
					$check_feeds_group = $pdo->prepare("insert into `editorss_feeds` (`group`, `title`, `description`, `uri`, `valid_resources`, `tags`, `user`, `origin`) values(?, ?, ?, ?, ?, ?, ?, ?)");
					$check_feeds_group->bindParam(1, addslashes(str_replace(array("a'", "che'", "e'", "i'", "o'", "u'", "A'", "CHE'", "E'", "I'", "O'", "U'"), array("à", "ché", "è", "ì", "ò", "ù", "À", "CHÉ", "È", "Ì", "Ò", "Ù"), stripslashes($GLOBALS["rss_group"]))));
					$check_feeds_group->bindParam(2, addslashes($GLOBALS["rss_title"]));
					$check_feeds_group->bindParam(3, addslashes($GLOBALS["rss_description"]));
					$check_feeds_group->bindParam(4, addslashes($GLOBALS["rss_uri"]));
					$check_feeds_group->bindParam(5, addslashes($GLOBALS["valid_resources"]));
					$check_feeds_group->bindParam(6, addslashes(str_replace(array("a'", "e'", "che'", "i'", "o'", "u'", "A'", "E'", "CHE'", "I'", "O'", "U'"), array("à", "è", "ché", "ì", "ò", "ù", "À", "È", "CHÉ", "Ì", "Ò", "Ù"), $GLOBALS["rss_tag"])));
					$check_feeds_group->bindParam(7, addslashes($GLOBALS["decrypted_user"]));
					$check_feeds_group->bindParam(8, addslashes($GLOBALS["origin"]));
					if (!$check_feeds_group->execute()) {
						$act = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
					}
				}
				$act = "edited:" . $the_id;
			}
	} else {
		// Oppure ne inserisce uno nuovo
		$check_feeds_group = $pdo->prepare("insert into `editorss_feeds` (`group`, `title`, `description`, `uri`, `valid_resources`, `tags`, `user`, `origin`) values(?, ?, ?, ?, ?, ?, ?, ?)");
		$check_feeds_group->bindParam(1, addslashes(str_replace(array("a'", "che'", "e'", "i'", "o'", "u'", "A'", "CHE'", "E'", "I'", "O'", "U'"), array("à", "ché", "è", "ì", "ò", "ù", "À", "CHÉ", "È", "Ì", "Ò", "Ù"), stripslashes($GLOBALS["rss_group"]))));
		$check_feeds_group->bindParam(2, addslashes($GLOBALS["rss_title"]));
		$check_feeds_group->bindParam(3, addslashes($GLOBALS["rss_description"]));
		$check_feeds_group->bindParam(4, addslashes($GLOBALS["rss_uri"]));
		$check_feeds_group->bindParam(5, addslashes($GLOBALS["valid_resources"]));
		$check_feeds_group->bindParam(6, addslashes(str_replace(array("a'", "e'", "che'", "i'", "o'", "u'", "A'", "E'", "CHE'", "I'", "O'", "U'"), array("à", "è", "ché", "ì", "ò", "ù", "À", "È", "CHÉ", "Ì", "Ò", "Ù"), $GLOBALS["rss_tag"])));
		$check_feeds_group->bindParam(7, addslashes($GLOBALS["decrypted_user"]));
		$check_feeds_group->bindParam(8, addslashes($GLOBALS["origin"]));
		if (!$check_feeds_group->execute()) {
			$act = "Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
		} else {
			// Ricava l'ultimo id
			$get_last_id = $pdo->query("select max(id) as `maxid` from `editorss_feeds`");
			if ($get_last_id->rowCount() > 0){
				while ($dato_get_last_id = $get_last_id->fetch()) {
					$the_id = $dato_get_last_id["maxid"];
				}
			}
			$act = "added:" . $the_id;
		}
	}
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// AUTOMAZIONE
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	$pdo2 = db_connect("");
	if (!isset($GLOBALS["automation_date"])){
		$GLOBALS["automation_date"] = converti_data(date("D, d M Y"));
	}
	if (!isset($GLOBALS["automation_time"])){
		$GLOBALS["automation_time"] = date("H:i");
	}
	$start_date = date("Y-m-d", strtotime(converti_data($GLOBALS["automation_date"], "en")));
	$start_time = date("H:i:s", strtotime($GLOBALS["automation_time"]));
	$is_uri = true;
	
	// Vede se l'automazione è già stata salvata
	$check_action = $pdo2->query("select * from `airs_automation` where `action` = '" . addslashes($GLOBALS["action"]) . "'");
	if ($check_action->rowCount() == 0){
		if ($GLOBALS["automation"] == "on"){
			$add_automation = $pdo2->prepare("insert into `airs_automation` (`action`, `frequency`, `is_uri_execution`, `start_date`, `start_time`, `user`) values(?, ?, ?, ?, ?, ?)");
			$add_automation->bindParam(1, addslashes($GLOBALS["action"]));
			$add_automation->bindParam(2, addslashes($GLOBALS["automation_cadence"]));
			$add_automation->bindParam(3, $is_uri);
			$add_automation->bindParam(4, addslashes($start_date));
			$add_automation->bindParam(5, addslashes($start_time));
			$add_automation->bindParam(6, addslashes($GLOBALS["decrypted_user"]));
			
			if (!$add_automation->execute()) {
				$act = "Si è verificato un errore durante il salvataggio dell'automazione:<br />" . $pdo->errorCode();
			}
		}
	} else {
		if ($GLOBALS["automation"] == "on"){
			$edit_automation =  $pdo2->prepare("update `airs_automation` set `action` = ('" . addslashes($GLOBALS["action"]) . "'), `frequency` = ('" . addslashes($GLOBALS["automation_cadence"]) . "'), `is_uri_execution` = ('" . $is_uri . "'), `start_date` = ('" . $start_date . "'), `start_time` = ('" . $start_time . "'), `user` = ('" . addslashes($GLOBALS["decrypted_user"]) . "') where `action` = '" . addslashes($GLOBALS["action"]) . "'");
			if (!$edit_automation->execute()) {
				$act = "Si è verificato un errore durante il salvataggio:\n" . $pdo->errorCode();
			}
		} else {
			$edit_automation =  $pdo2->prepare("delete from `airs_automation` where `action` = '" . addslashes($GLOBALS["action"]) . "'");
			if (!$edit_automation->execute()) {
				$act = "Si è verificato un errore durante la cancellazione:\n" . $pdo->errorCode();
			}
		}
	}
	print $act;
}
?>