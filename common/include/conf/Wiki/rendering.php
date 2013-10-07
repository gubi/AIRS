<?php
/**
* Core config for System Wiki renderings
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
if (get_magic_quotes_gpc()) {
	print "ATTENZIONE: per eseguire meglio lo script &egrave; necessario impostare magic_quotes_gpc = Off";
}

$wiki = &new Text_Wiki;
// Pagine interne
function check_internal_pages(){
	require_once($_SERVER["DOCUMENT_ROOT"] . "/common/include/.mysql_connect.inc.php");
	$pdo = db_connect("");
	
	// Pagine del menu
	$wikilink_internal_pages = $pdo->query("select `page`, `name` as `page` from `airs_wikilink_internal_pages`, `airs_menu`");
	if ($wikilink_internal_pages->rowCount() > 0){
		while ($dato_wikilink_internal_pages = $wikilink_internal_pages->fetch()){
			$pages[] = str_replace(" ", "_", str_replace("{PAGE}", urldecode($_GET["page"]), $dato_wikilink_internal_pages["page"]));
		}
	}
	// Pagine con i contenuti
	$content_pages = $pdo->query("select `name`, `subname`, `sub_subname` from `airs_content`");
	if ($content_pages->rowCount() > 0){
		while ($dato_content_pages = $content_pages->fetch()){
			$pages[] = $dato_content_pages["name"];
			$pages[] = $dato_content_pages["name"] . "/" . $dato_content_pages["subname"];
			$pages[] = $dato_content_pages["name"] . "/" . $dato_content_pages["subname"] . "/" . $dato_content_pages["sub_subname"];
		}
	}
	// Pagine per gli utenti
	$user_pages = $pdo->query("select `username` from `airs_users`");
	if ($user_pages->rowCount() > 0){
		while ($dato_user_pages = $user_pages->fetch()){
			$pages[] = array("Utente/" . ucfirst($dato_user_pages["username"]), "", ucfirst($dato_user_pages["username"]));
			$pages[] = ucfirst($dato_user_pages["username"]);
		}
	}
	$result = array_unique($pages);
	return $result;
}
$wiki->setRenderConf('xhtml', 'wikilink', 'pages', check_internal_pages());
$wiki->setRenderConf('xhtml', 'freelink', 'pages', check_internal_pages());

// Esistenza delle pagine interne
function pageExists($page){
	if (in_array($page, check_internal_pages())){
		return true;
	} else {
		return false;
	}
}
$wiki->setRenderConf('xhtml', 'wikilink', 'exists_callback', 'pageExists');
$wiki->setRenderConf('xhtml', 'freelink', 'exists_callback', 'pageExists');

// interwiki
$sites = array(
	'wikipedia'    => 'http://it.wikipedia.org/wiki/%s',
	'wikipedia:it'    => 'http://it.wikipedia.org/wiki/%s',
	'wikipedia:en'    => 'http://en.wikipedia.org/wiki/%s',
	'wikipedia:es'    => 'http://es.wikipedia.org/wiki/%s',
	'wikipedia:fr'    => 'http://fr.wikipedia.org/wiki/%s',
	'wikipedia:de'    => 'http://de.wikipedia.org/wiki/%s',
	'wikiquote'    => 'http://it.wikiquote.org/wiki/%s',
	'wikiquote:it'    => 'http://it.wikiquote.org/wiki/%s',
	'wikiquote:en'    => 'http://en.wikiquote.org/wiki/%s',
	'wikiquote:es'    => 'http://es.wikiquote.org/wiki/%s',
	'wikiquote:fr'    => 'http://fr.wikiquote.org/wiki/%s',
	'wikiquote:de'    => 'http://de.wikiquote.org/wiki/%s'
);
// configure the interwiki rule
$wiki->setRenderConf('xhtml', 'interwiki', 'sites', $sites);
?>