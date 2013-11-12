<?php
/**
* Returns a tree of all System scripts
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
header("Content-type: text/plain");

if(!isset($_GET["path"]) || trim($_GET["path"]) == ""){
	$GLOBALS["path"] = $_SERVER["DOCUMENT_ROOT"];
	$level = 2;
} else {
	$GLOBALS["path"] = $_SERVER["DOCUMENT_ROOT"] . $_GET["path"] . "/";
	$level = 1;
}
function explodeTree($array, $delimiter = '_', $baseval = false) {
	if(!is_array($array)) return false;
	$splitRE   = '/' . preg_quote($delimiter, '/') . '/';
	$returnArr = array();
	foreach ($array as $key => $val) {
		// Get parent parts and the current leaf
		$parts	= preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
		$leafPart = array_pop($parts);

		// Build parent structure 
		// Might be slow for really deep and large structures
		$parentArr = &$returnArr;
		foreach ($parts as $part) {
			if (!isset($parentArr[$part])) {
				$parentArr[$part] = array();
			} elseif (!is_array($parentArr[$part])) {
				if ($baseval) {
					$parentArr[$part] = array('__base_val' => $parentArr[$part]);
				} else {
					$parentArr[$part] = array();
				}
			}
			$parentArr = &$parentArr[$part];
		}

		// Add the final part to the structure
		if (empty($parentArr[$leafPart])) {
			$parentArr[$leafPart] = $val;
		} elseif ($baseval && is_array($parentArr[$leafPart])) {
			$parentArr[$leafPart]['__base_val'] = $val;
		}
	}
	return $returnArr;
}
function plotTree($arr, $indent = 0, $mother_run = true){
	if($mother_run){
		$a = '<ul class="filetree">';
	}
	foreach($arr as $k=>$v){
		if($k == "__base_val") continue;
		$show_val = (is_array($v) ? $v["__base_val"] : $v);
		$info = pathinfo($show_val);
		$md5 = md5($show_val);
		$a .= (strlen($show_val) == 0) ? '' : ((!is_file($GLOBALS["path"] . "/" . $show_val)) ? '<li class="expandable"><span class="folder"><a href="javascript: void(0);" onclick="load_folder(\'' . $show_val . '\', \'' . $md5 . '\');">' . $k . '</a></span><ul id="' . $md5 . '" class="filetree"></ul></li>' : '<li><span class="file"><a href="javascript: void(0);" onclick="show_script(\'' . $show_val . '\', \'' . $k . '\');">' . $k . "</a></span></li>");
		if(is_array($v)){
			$md5 = md5($k);
			// this is what makes it recursive, rerun for childs
			$a .= '<li><span class="folder"><a href="javascript: void(0);" onclick="load_folder(\'' . $k . '\', \'' . $md5 . '\');">' . $k . '</a></span><ul class="filetree" id="' . $md5 . '">' . plotTree($v, ($indent+1), false) . "</ul></li>";
		}
	}
	if($mother_run){
		$a .= "</ul>";
	}
	return $a;
}
function plotJsonTree($arr, $indent = 0, $mother_run = true){
	$f = -1;
	foreach($arr as $k=>$v){
		$f++;
		if($k == "__base_val") continue;
		$show_val = (is_array($v) ? $v["__base_val"] : $v);
		$info = pathinfo($show_val);
		$md5 = md5($_GET["path"] . "/" . $show_val);
		$a[$f]["text"] = (strlen($show_val) == 0) ? '' : (($info["extension"] == "") ? '<a href="javascript: void(0);" onclick="load_folder(\'' . $_GET["path"] . "/" . $show_val . '\', \'' . $md5 . '\');">' . $k . '</a>' : '<a href="javascript: void(0);" onclick="show_script(\'' . $_GET["path"] . "/" . $show_val . '\', \'' . $show_val . '\');">' . $k . "</a>");
		if($info["extension"] == ""){
			$a[$f]["expanded"] = false;
			$a[$f]["hasChildren"] = false;
			$a[$f]["id"] = $md5;
			$a[$f]["classes"] = "folder";
		} else {
			$a[$f]["hasChildren"] = false;
			$a[$f]["classes"] = "file";
		}
	}
	return $a;
}
function in_array_beginning_with($path, $array) {
	foreach ($array as $begin) {
		if (strncmp($path, $begin, strlen($begin)) == 0) {
			return true;
		}
	}
	return false;
}
$exclude = array(substr_replace($GLOBALS["path"] , "", -1),
			    "backup", 
			    "bigbluebutton", 
			    "bigbluebutton-default", 
			    "doc", 
			    "dtd", 
			    "kannel",
			    "nginx-default",
			    "PEAR",
			    "tests",
			    ".gnupg",
			    ".tmp");
$tree = shell_exec("tree " . $GLOBALS["path"] . " -fiL " . $level);
$treelist = array_filter(explode("\n", $tree));
$stats = array_pop($treelist);
foreach($treelist as $k => $v) {
	$v = str_replace($GLOBALS["path"], "", $v);
	if(!in_array_beginning_with($v, $exclude)) {
		$info = pathinfo($v);
		if($info["extension"] !== "conf"){
			$dir[$v] = $v;
		}
	}
} 
if(!isset($_GET["path"]) || trim($_GET["path"]) == ""){
	//print_r($dir);
	//print $stats . "\n\n";
	//print_r(explodeTree($dir, "/"));
	print plotTree(explodeTree($dir, "/"));
} else {
	$res = plotJsonTree(explodeTree($dir, "/"));
	
	//print_r($res["children"]);
	print json_encode($res);
}
?>