<ul id="tree" class="filetree">
	<?php
	function getDirectoryTree($outer_dir){
		$dirs = array_diff(scandir($outer_dir), Array(".", ".."));
		$dir_array = Array();
		foreach($dirs as $d){
			if(is_dir($outer_dir . "/" . $d)){
				$dir_array["dir"][$d] = getDirectoryTree($outer_dir . "/" . $d, $outer_dir);
			} else {
				$dir_array["file"][$d] = $d;
			}
		}
		return $dir_array;
	}
	function iterate_array($array, $last_dir, $outer_dir){
		if ($last_dir !== ""){
			$last_dir .= "/";
		}
		if ($array["dir"]){
			if ($last_dir !== $outer_dir . "/"){
				print "<ul id=\"folder_" . $dir . "\">";
			}
			foreach($array["dir"] as $dir => $type){
				if ($dir == "common"){
					$common_open = " class=\"open\"";
				} else {
					$common_open = "";
				}
				print "<li" . $common_open . "><span class=\"folder\">" . $dir . "</span>";
					iterate_array(getDirectoryTree($last_dir . $dir), $last_dir . $dir, $outer_dir);
				print "</li>";
			}
			if ($last_dir !== $outer_dir . "/"){
				print "</ul>";
			}
		}
		if ($array["file"]){
			if ($last_dir !== $outer_dir . "/"){
				print "<ul id=\"folder_" . $dir . "\">";
			}
			foreach($array["file"] as $file => $type){
				if (substr($file, 0, 1) == "."){
					$class_transparent = "file transparent";
				} else {
					$class_transparent = "file";
				}
				//print "<li><span class=\"" . $class_transparent . "\"><a href=\"javascript: void(0);\" onclick=\"load_file('" . $last_dir . $file . "')\" title=\"Visualizza questo script\">" . $file . "</a></span></li>";
				print "<li><span class=\"" . $class_transparent . "\">" . $file . "</span></li>";
			}
			if ($last_dir !== $outer_dir . "/"){
				print "</ul>";
			}
		}
	}
	$scanned = iterate_array(getDirectoryTree("../.."), "../..", "../..");
	?>
</ul>