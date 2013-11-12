<?php
/**
* Render an inclusion linking xml file
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
* @package	AIRS_System_scripts
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/
header("Content-type: text/plain; charset=utf-8");
require_once("../../../.mysql_connect.inc.php");

function get_absolute_path($path) {
	$path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
	$parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
	$absolutes = array();
	foreach ($parts as $part) {
		if ('.' == $part) continue;
		if ('..' == $part) {
			array_pop($absolutes);
		} else {
			$absolutes[] = $part;
		}
	}
	return implode(DIRECTORY_SEPARATOR, $absolutes);
}
function getColor($num) {
	$hash = md5('color' . $num); // modify 'color' to get a different palette
	return array(
		hexdec(substr($hash, 0, 2)), // r
		hexdec(substr($hash, 2, 2)), // g
		hexdec(substr($hash, 4, 2)) //b
	);
}
function get_item_id($path) {
	$pdLSM = db_connect("slm");
	$item_data = $pdLSM->query("select `id` from `current_scripts` where `path` = '" . addslashes($path) . "'");
	if($item_data->rowCount() > 0){
		while($dato_item = $item_data->fetch()){
			return $dato_item["id"];
		}
	} else {
		return null;
	}
}

function correct_url($url, $path){
	$info = pathinfo($path);
	$current_path = $info["dirname"];
	
	foreach($url as $uk => $the_url){
		if(strpos($the_url, "/")){
			$url_data = explode("/", $the_url);
		}
		$path_data = explode("/", $path);
		if(strpos($the_url, "/") && $url_data[0] == "common"){
			$id = get_item_id(str_replace("{ABSOLUTE_PATH}", "", preg_replace('/\?.*/', '', $the_url))) ? get_item_id(str_replace("{ABSOLUTE_PATH}", "", preg_replace('/\?.*/', '', $the_url))) : 0;
			$res[$id] = str_replace("{ABSOLUTE_PATH}", "", preg_replace('/\?.*/', '', $the_url));
		} else {
			$urlinfo = pathinfo($the_url);
			if(strlen($urlinfo["extension"]) > 0){
				if($url_data[0] == ".."){
					$id = get_item_id(get_absolute_path($current_path ."/" . preg_replace('/\?.*/', '', $the_url))) ? get_item_id(get_absolute_path($current_path ."/" . preg_replace('/\?.*/', '', $the_url))) : 0;
					$res[$id] = get_absolute_path($current_path ."/" . preg_replace('/\?.*/', '', $the_url));
				} else {
					$id = get_item_id($current_path . "/" . preg_replace('/\?.*/', '', $the_url)) ? get_item_id($current_path . "/" . preg_replace('/\?.*/', '', $the_url)) : 0;
					$res[$id] = $current_path . "/" . preg_replace('/\?.*/', '', $the_url);
				}
			} else {
				$res[] = "";
			}
		}
	}
	ksort($res);
	return $res;
}
function detect_link($type, $contents, $id, $path) {
	$arr["id"] = $id;
	$arr["path"] = $path;
	switch($type){
		case "require":
		case "require_once":
		case "include":
		case "include_once":
			$regex = '#' . $type . '\W(\\\\\'|\\\\\")(.*?)(\\\\\'|\\\\\")#s';
			break;
		case "get":
		case "getJSON":
		case "post":
			$regex = '#\.' . $type . '\((\\\\\'|\\\\\")(.*?)(\\\\\'|\\\\\")#s';
			break;
		case "ajax":
			$regex = '#\.' . $type . '.*?url:\s+(\\\'|\\\")(.*?)(\\\'|\\\")#s';
			break;
	}
	if(preg_match_all($regex, $contents, $ajax)){
		$targets = array_unique(correct_url($ajax[2], $path));
		
		$arr["count"] = count($targets);
		$arr["targets"] = $targets;
	} else {
		$arr = null;
	}
	return $arr;
}
$max_score = 0;
$f = 0;
$pdLSM = db_connect("slm");
$system_search = $pdLSM->query("select *, match(`contents`) against('+*require* +*require_once* +*include* +*include_once* +*$.get* +*$.post* +*$.ajax*') as `attinenza` from `current_scripts` where match(`contents`) against('+*require* +*require_once* +*include* +*include_once* +*$.get* +*$.post* +*$.ajax*')");
if($system_search->rowCount() > 0){
	while($dato_system_search = $system_search->fetch()){
		$f++;
		if($dato_system_search['attinenza'] > $max_score){ $max_score = $dato_system_search['attinenza']; }
		$score = @number_format(($dato_system_search['attinenza']/$max_score)*100,0);
		
		$arr["require"][$f] = detect_link("require", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["require_once"][$f] = detect_link("require_once", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["include"][$f] = detect_link("include", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["include_once"][$f] = detect_link("include_once", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["get"][$f] = detect_link("get", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["getJSON"][$f] = detect_link("getJSON", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["post"][$f] = detect_link("post", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
		$arr["ajax"][$f] = detect_link("ajax", $dato_system_search["contents"], $dato_system_search["id"], $dato_system_search["path"]);
	}
}

$require = array_values(array_filter($arr["require"]));
$require_once = array_values(array_filter($arr["require_once"]));
$include = array_values(array_filter($arr["include"]));
$include_once = array_values(array_filter($arr["include_once"]));

foreach($require as $nodes){
	$edges_tot_count += (int)$nodes["count"];
		$e = 0;
	foreach($nodes["targets"] as $ek => $edge){
		$load_id[$nodes["id"]] = $nodes["path"];
		$color[$nodes["id"]]["r"] = 159;
		$color[$nodes["id"]]["g"] = 94;
		$color[$nodes["id"]]["b"] = 59;
		$node_size[$nodes["id"]] = ($nodes["count"] > 100) ? $nodes["count"]/2 : (($nodes["count"] < 1) ? 1 : $nodes["count"]*1.2);
		$nodes_position_type[$nodes["id"]]["circular"] = "true";
		$nodes_position[$nodes["id"]]["x"] = (int)$nodes["id"]*3 + mt_rand(-100, 100);
		$nodes_position[$nodes["id"]]["y"] = (int)$nodes["id"];
		$edge_type[$nodes["id"]] = "require()";
		
		$e++;
		if($nodes["id"] !== "0" && $ek !== 0){
			$load_id[$ek] = $edge;
			$color[$ek]["r"] = 143;
			$color[$ek]["g"] = 67;
			$color[$ek]["b"] = 26;
			$nodes_position_type[$ek]["circular"] = "true";
			$nodes_position[$ek]["x"] = (int)$nodes["id"]*3 + mt_rand(-100, 100);
			$nodes_position[$ek]["y"] = (int)$nodes["id"];
			$node_size[$ek] = ($nodes["count"] > 100) ? $nodes["count"]/5 : (($nodes["count"] < 1) ? 1 : $nodes["count"]*1.2);
			$edgeto[$nodes["id"]][] = $edge;
			$edge_type[$ek] = "require()";
			
			$edges .= '<edge id="' . $e . '" source="' . $nodes["id"] . '" target="' . $ek . '" weight="8.0"/>';
		}
	}
}
foreach($require_once as $nodes){
	$edges_tot_count += (int)$nodes["count"];
		$e = 0;
	foreach($nodes["targets"] as $ek => $edge){
		$load_id[$nodes["id"]] = $nodes["path"];
		$color[$nodes["id"]]["r"] = 111;
		$color[$nodes["id"]]["g"] = 9;
		$color[$nodes["id"]]["b"] = 7;
		$nodes_position_type[$nodes["id"]]["circular"] = "true";
		$nodes_position[$nodes["id"]]["x"] = mt_rand(mt_rand(-1000, -500), mt_rand(500, 1000));
		$nodes_position[$nodes["id"]]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
		$edge_type[$nodes["id"]] = "require_once()";
		
		$e++;
		if($nodes["id"] !== "0" && $ek !== 0){
			$load_id[$ek] = $edge;
			$color[$ek]["r"] = 207;
			$color[$ek]["g"] = 67;
			$color[$ek]["b"] = 65;
			$nodes_position_type[$ek]["circular"] = "true";
			$nodes_position[$ek]["x"] = mt_rand(mt_rand(-1000, -500), mt_rand(500, 1000));
			$nodes_position[$ek]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
			$edgeto[$nodes["id"]][] = $edge;
			$edge_type[$ek] = "require_once()";
			
			$edges .= '<edge id="' . $e . '" source="' . $nodes["id"] . '" target="' . $ek . '" weight="8.0"/>';
		}
	}
}
foreach($include as $nodes){
	$edges_tot_count += (int)$nodes["count"];
		$e = 0;
	foreach($nodes["targets"] as $ek => $edge){
		$load_id[$nodes["id"]] = $nodes["path"];
		$color[$nodes["id"]]["r"] = 143;
		$color[$nodes["id"]]["g"] = 64;
		$color[$nodes["id"]]["b"] = 0;
		$nodes_position_type[$nodes["id"]]["circular"] = "true";
		$nodes_position[$nodes["id"]]["x"] = mt_rand(mt_rand(-1000, -990), mt_rand(990, 1000));
		$nodes_position[$nodes["id"]]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
		$edge_type[$nodes["id"]] = "include()";
		
		$e++;
		if($nodes["id"] !== "0" && $ek !== 0){
			$load_id[$ek] = $edge;
			$color[$ek]["r"] = 191;
			$color[$ek]["g"] = 121;
			$color[$ek]["b"] = 83;
			$nodes_position_type[$ek]["circular"] = "true";
			$nodes_position[$ek]["x"] = mt_rand(mt_rand(-1000, -990), mt_rand(990, 1000));
			$nodes_position[$ek]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
			$edgeto[$nodes["id"]][] = $edge;
			$edge_type[$ek] = "include()";
			
			$edges .= '<edge id="' . $e . '" source="' . $nodes["id"] . '" target="' . $ek . '" weight="8.0"/>';
		}
	}
}
foreach($include_once as $nodes){
	$edgeto[$nodes["id"]] = array();
	$edges_tot_count += (int)$nodes["count"];
		$e = 0;
	foreach($nodes["targets"] as $ek => $edge){
		$load_id[$nodes["id"]] = $nodes["path"];
		$color[$nodes["id"]]["r"] = 189;
		$color[$nodes["id"]]["g"] = 229;
		$color[$nodes["id"]]["b"] = 207;
		$nodes_position_type[$nodes["id"]]["circular"] = "true";
		$nodes_position[$nodes["id"]]["x"] = mt_rand(mt_rand(-1000, -990), mt_rand(990, 1000));
		$nodes_position[$nodes["id"]]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
		$edge_type[$nodes["id"]] = "include_once()";
		
		$e++;
		if($nodes["id"] !== "0" && $ek !== 0){
			$load_id[$ek] = $edge;
			$color[$ek]["r"] = 118;
			$color[$ek]["g"] = 87;
			$color[$ek]["b"] = 127;
			$nodes_position_type[$ek]["circular"] = "true";
			$nodes_position[$ek]["x"] = mt_rand(mt_rand(-1000, -990), mt_rand(990, 1000));
			$nodes_position[$ek]["y"] = mt_rand(mt_rand(-500, -400), mt_rand(400, 500));
			$edgeto[$nodes["id"]][] = $edge;
			$edge_type[$ek] = "include_once()";
			
			$edges .= '<edge id="' . $e . '" source="' . $nodes["id"] . '" target="' . $ek . '" weight="8.0"/>';
		}
	}
}
function generate_points_on_circle($x, $y, $radius){
	if($x < $y){
		$xmin = $x;
		$xmax = $y;
	} else {
		$xmin = $y;
		$xmax = $x;
	}
	$angle = deg2rad(mt_rand($xmin, $xmax));
	//$pointRadius = mt_rand(0, $radius);
	$pointRadius = sqrt(mt_rand(0, $radius*$radius));
	$n = array(
		'x' => sin($angle) * $pointRadius,
		'y' => cos($angle) * $pointRadius
	);
	return array($n["x"], $n["y"]);
}
foreach($load_id as $load => $path){
	$all_scripts = $pdLSM->query("select * from `current_scripts` where `id` = '" . $load . "'");
	while($dato_script = $all_scripts->fetch()){
		if($nodes_position_type[$dato_script["id"]]["circular"] == "true") {
			list($posx, $posy) = generate_points_on_circle($nodes_position[$dato_script["id"]]["x"], $nodes_position[$dato_script["id"]]["y"], 100);
		} else {
			list($posx, $posy) = array($nodes_position[$dato_script["id"]]["x"], $nodes_position[$dato_script["id"]]["y"]);
		}
		
		$info = pathinfo($dato_script["path"]);
		$node .= '<node id="' . $dato_script["id"] . '" label="' . $info["basename"] . '">';
		$node .= '<attvalues>';
			$node .= '<attvalue for="MIME" value="' . $dato_script["mime_type"] . '"/>';
			$node .= '<attvalue for="Permessi del file" value="' . $dato_script["permission"] . '"/>';
			$node .= '<attvalue for="Data di ultima modifica" value="' . $dato_script["modified_date"] . '"/>';
			$node .= '<attvalue for="Stato" value="' . $dato_script["status"] . '"/>';
			$node .= '<attvalue for="sep" value="sep"/>';
			$node .= '<attvalue for="Percorso" value="' . $dato_script["path"] . '"/>';
			//print_r($edgeto[$dato_script["id"]]);
			if(is_array($edgeto[$dato_script["id"]])){
				$etos = implode(",", $edgeto[$dato_script["id"]]);
			} else {
				$etos = "undetected link";
			}
			$node .= '<attvalue for="Collegato con" value="' . $etos . '"/>';
			$node .= '<attvalue for="Tipo di relazione" value="' . $edge_type[$dato_script["id"]] . '"/>';
		$node .= '</attvalues>';
		$node .= '<viz:size value="1"/>';
		$node .= '<viz:color b="' . $color[$dato_script["id"]]["b"] . '" g="' . $color[$dato_script["id"]]["g"] . '" r="' . $color[$dato_script["id"]]["r"] . '"/>';
		$node .= '<viz:position x="' . $posx . '" y="' . $posy . '" z="0.0"/>';
		$node .= '</node>';
	}
	$nodes_count += $all_scripts->rowCount();
}
$gefx = <<<GEFX
<?xml version="1.0" encoding="UTF-8"?>
<gexf xmlns="http://www.gexf.net/1.2draft" version="1.2" xmlns:viz="http://www.gexf.net/1.2draft/viz" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.gexf.net/1.2draft http://www.gexf.net/1.2draft/gexf.xsd">
	<meta lastmodifieddate="2012-05-25">
		<creator>AIRS - Automatic Intelligent Research System</creator>
		<Title>System "mind"</Title>
		<description>A graphic visualization of scripts calls</description>
	</meta>
	<graph defaultedgetype="undirected" timeformat="double" mode="dynamic">
		<attributes class="node" mode="static">
			<attribute id="MIME" title="Tipo di MIME" type="string"></attribute>
			<attribute id="Permessi" title="Permessi del file" type="string"></attribute>
			<attribute id="Data di ultima modifica" title="Data di ultima modifica" type="string"></attribute>
			<attribute id="Stato" title="Stato" type="string"></attribute>
			<attribute id="sep" title="Separator" type="string"></attribute>
			<attribute id="Percorso" title="Percorso del file" type="string"></attribute>
			<attribute id="Collegato con" title="Collegato con" type="string"></attribute>
			<attribute id="Tipo di relazione" title="Tipo" type="string"></attribute>
		</attributes>
		<nodes count="$nodes_count">
			$node
		</nodes>
		<edges count="$edges_tot_count">
			$edges
		</edges>
	</graph>
</gexf>
GEFX;

if($_GET["debug"] == "true"){
	print_r($require_once);
} else {
	print $gefx;
}
?>