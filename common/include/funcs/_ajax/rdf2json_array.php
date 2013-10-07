<?php
header("Content-type: text/plain; charset=utf-8");
require_once("../_taglia_stringa.php");

if(isset($_GET["resources"]) && trim($_GET["resources"]) !== ""){
	require_once($_SERVER["DOCUMENT_ROOT"] . "/common/include/lib/semantic_web/semsol-arc2/ARC2.php");
	require_once($_SERVER["DOCUMENT_ROOT"] . "/common/include/funcs/_check_valid_uri.php");
	//require_once("EasyRDF/autoload.php");
	
	if(strpos($_GET["resources"], ",")){
		$resources = explode(",", $_GET["resources"]);
	} else {
		$resources = array($_GET["resources"]);
	}
	foreach($resources as $resource){
		$uri = urldecode(trim($resource));
		
		$parser = ARC2::getRDFXMLParser();
		$parser->parse($uri);
		$triples = $parser->getTriples();
		
		foreach($triples as $k => $v){
			//print "Subject: " . $v["s"] . " Predicate:" . $v["p"] . " Object:" . $v["o"] . "\n";
			$p = pathinfo($v["p"]);
			$pos[$v["s"]][preg_replace("/.*\#(.*?)/i", "", $p["filename"])][] = $v["o"];
		}
		foreach($pos as $_k => $_v){
			foreach($_v as $__k => $__v){
				if(count($__v) > 1){
					$_pos[$_k][$__k] = $__v;
				} else {
					$_pos[$_k][$__k] = $__v[0];
				}
			}
		}
		foreach($_pos as $pk => $pv){
			foreach($pv as $ppk => $ppv){
				if(is_array($ppv)){
					foreach($ppv as $pppk => $pppv){
						if(is_array($_pos[$pppv])) {
							$pos2[$uri][$ppk][$pppk] = array_map("trim", $_pos[$pppv]);
						} else {
							$pos2[$uri][$ppk][$pppk] = trim($pppv);
						}
					}
				} else {
					if(!is_array($_pos[$ppv])) {
						$pos2[$uri][$ppk] = trim($ppv);
					} else {
						foreach($_pos[$ppv] as $_ppk => $_ppv){
							if(!is_array($_ppv)) {
								if(is_array($_pos[$_ppv])) {
									$pos2[$uri][$ppk][$_ppk] = array_map("trim", $_pos[$_ppv]);
								} else {
									$pos2[$uri][$ppk][$_ppk] = trim($_ppv);
									$pos2[$uri][$ppk][$_ppk . "_text"] = (strpos(trim($_ppv), "http") === false ? null : taglia_stringa(trim($_ppv), 50, "...", "/"));
								}
							}
						}
					}
				}
			}
		}
	}
	if($_GET["debug"] == "true") {
		print_r($pos2);
	}
	print json_encode($pos2);
}
?>