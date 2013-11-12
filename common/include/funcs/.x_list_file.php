<?php
$cartella_upload = "downloads/";

$ext = strrchr($file_name, '.');
$dir = opendir($cartella_upload);
while ($fname = readdir($dir)){
	if ($fname !== "." && $fname !== ".."){
		print $fname . "<br />";
	}
}
closedir($dir);
?>