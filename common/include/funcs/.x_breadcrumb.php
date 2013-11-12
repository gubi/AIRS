<?php
$link = "http://" . $page_uri;
$referringPage = parse_url($link);
$parsed_query = str_replace("/inran/", "", $referringPage["path"]);
//parse_str($parsed_query, $queryVars); // Solo in caso di .htaccess non funzionante
$queryVars = explode("/", $parsed_query);
if (trim($queryVars[count($queryVars)-1]) == ""){
	array_pop($queryVars);
}
$q_counter = 0;
foreach ($queryVars as $k => $v){
	$q_counter++;
	$page_link .= $v . "/";
	$v_functioned = $v;
	$v = str_replace($GLOBALS["function_part"] . ":", "", $v);
	if ($q_counter < count($queryVars)){
		if (!is_numeric($v)){
			$page_a .= "<li><a href=\"" . substr($page_link, 0, -1) . "\" title=\"Vai alla pagina &quot;" . unget_link($v) . "&quot;\">" . unget_link($v) . "</a></li>";
		}
	} else {
		$page_a .= "<li>" . unget_link(urldecode($v)) . "</li>";
	}
	$GLOBALS["current_pos"] .= $v;
	$GLOBALS["current_pos_functioned"] .= $v_functioned;
	
	if ($q_counter < count($queryVars)){
		$GLOBALS["current_pos"] .= "/";
		$GLOBALS["current_pos_functioned"] .= "/";
	}
}
?>