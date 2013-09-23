<?php
if (file_exists($_SERVER["DOCUMENT_ROOT"] . $_GET["file"])){
	// downloading a file
	$file = $_SERVER["DOCUMENT_ROOT"] . $_GET["file"];
	$filename = $_GET["file"];

	// fix for IE catching or PHP bug issue
	header("Pragma: public");
	header("Expires: 0"); // set expiration time
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	// browser must download file from server instead of cache

	// force download dialog
	header("Content-Type: application/octet-stream");
	header("Content-Type: " . mime_content_type($file));
	header("Content-Disposition: attachment; filename=\"" . trim(basename($filename)) . "\";");
	
	/*
	The Content-transfer-encoding header should be binary, since the file will be read
	directly from the disk and the raw bytes passed to the downloading computer.
	The Content-length header is useful to set for downloads. The browser will be able to
	show a progress meter as a file downloads. The content-lenght can be determines by
	filesize function returns the size of a file.
	*/
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Length: " . filesize($file));

	readfile($file);
	exit();
}
?>