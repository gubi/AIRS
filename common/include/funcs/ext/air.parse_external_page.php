<?php
//header("Content-type: text/plain");

require_once("../_get_html_item-scrap.php");

$out = str_replace("\n", "", scrap($_GET["uri"]));
preg_match_all("/\<html.*\>(.*)\<\/html\>/", $out, $matched);
//print_r($matched);
$search = "<head>";

$replace = <<<Replace
		<head>
		<script type="text/javascript" src="../../../js/jquery-1.6.min.js"></script>
		<script type="text/javascript" src="../../../js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
		<script type="text/javascript" src="../../../js/jquery.animate-shadow-min.js"></script>
		<script type="text/javascript" src="air.parse_external_page.js"></script>
Replace;
$preg_rep = preg_replace(array(
						'/onclick="(.*?)"/is',
						'/<script\b[^>]*>(.*?)<\/script>/is',
						'/<title\b[^>]*>(.*?)<\/title>/is'
					), "", $matched[0][0]);
$str_rep = str_replace("<body>", "<body><div style=\"overflow: auto; position: fixed; width: 100%; height: 100%; top: 0px; left: 0px; background: #f0f0f0; opacity: 0.65; z-index: 99;\"></div>", $preg_rep);

print str_replace($search, $replace, $str_rep);
?>