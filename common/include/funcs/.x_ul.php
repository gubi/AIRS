<?php
header("Content-type: text/plain; charset=utf8;");
require_once("../.mysql_connect.inc.php");
require_once("translate.php");

$pdo = db_connect(".airs");
$query_countries = $pdo->query("select * from `ISO_3166-1` order by `Country` asc");
if ($query_countries->rowCount() >0){
	while($dato_country = $query_countries->fetch()){
		$translated = translate($dato_country["Country"], "en", "it");
		if ($translated !== "Daily Quota Exceeded. Contact Info@translated.net. See You Tomorrow."){
			$translated = "";
			$edit_countries =  $pdo->prepare("update `ISO_3166-1` set `Country_it` = ('" . addslashes(ucwords(strtolower($translated))) . "') where `id` = '" . addslashes($dato_country["id"]) . "'");
			if (!$edit_countries->execute()) {
				print "error:Si è verificato un errore durante il salvataggio:<br />" . $pdo->errorCode();
			} else {
				print $dato_country["Country_it"] . ". . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . ok\n";
			}
		}
	}
}
?>