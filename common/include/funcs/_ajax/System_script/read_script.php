<?php
/**
* Read a script code and returns a page with template
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
if(isset($_GET["filepath"]) && trim($_GET["filepath"]) !== ""){
	$absolute_path = "https://" . $_SERVER["SERVER_ADDR"];
	$absolute_uri = $absolute_path . "/" . $_GET["filepath"];
	$path = $_GET["filepath"];
	$filepath = $_SERVER["DOCUMENT_ROOT"] . $_GET["filepath"];
	$info = pathinfo($filepath);
	
	$ext = $info["extension"];
	
	$img_ext = array("bmp", "gif", "jpg", "jpeg", "ico", "png", "svg", "tif", "tiff");
	if(in_array(strtolower($ext), $img_ext)) {
		$contents = '<div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #2d2d2d url(' . $absolute_uri . ') center center no-repeat;"></div><script type="text/javascript">parent.loader("", "hide");</script>';
	} else {
		$file_contents = utf8_encode(shell_exec("cat " . $filepath));
		if(strlen($file_contents) > 0 && $ext !== "conf"){
			$contents = <<<Contents
<html>
	<head>
		<script src="$absolute_path/common/js/codemirror-3.02/lib/codemirror.js"></script>
		<link rel="stylesheet" href="$absolute_path/common/js/codemirror-3.02/lib/codemirror.css">
		<script src="$absolute_path/common/js/codemirror-3.02/addon/edit/matchbrackets.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/diff/diff.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/htmlembedded/htmlembedded.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/htmlmixed/htmlmixed.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/http/http.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/xml/xml.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/javascript/javascript.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/css/css.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/markdown/markdown.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/mysql/mysql.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/sql/sql.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/perl/perl.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/properties/properties.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/python/python.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/shell/shell.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/clike/clike.js"></script>
		<script src="$absolute_path/common/js/codemirror-3.02/mode/php/php.js"></script>
		<style>
		body {
			padding: 0;
			margin: 0;
		}
		.CodeMirror {
			height: auto;
			top: 31px;
		}
		#script_header {
			font-family: Ubuntu, Arial, sans-serif;
			font-size: 12px;
			text-indent: 10px;
			line-height: 30px;
			background-color: #f7f7f7;
			border-bottom: #ccc 1px solid;
			box-shadow: 0 0 6px #aaa;
			position: fixed;
			top: 0;
			width: 100%;
			z-index: 4;
		}
		</style>
	</head>
	<body>
		<div id="script_header">Visualizzazione del file: <tt>$path</tt></div>
		<div style="width: 100%; height: 100%; position: fixed; top: 0;">
			<textarea id="code" name="code">$file_contents</textarea>
		</div>
		<script type="text/javascript">
		var editor = CodeMirror.fromTextArea(document.getElementById("code"), {
			lineNumbers: true,
			matchBrackets: true,
			mode: "application/x-httpd-php",
			indentUnit: 4,
			indentWithTabs: true,
			enterMode: "keep",
			tabMode: "shift",
			readOnly: true
		});
		parent.loader("", "hide");
		</script>
	</body>
</html>
Contents;
		} else {
			$contents = <<<Error_contents
<html>
	<head>
		<link rel="stylesheet" href="$absolute_path/common/css/main.css" />
		<style>
		body {
			box-shadow: 0 0 108px #ccc inset;
			padding: 0;
			margin: 0;
			width: 100%;
			height: 100%;
		}
		#centered {
			width: 800px;
			height: 500px;
			left: 50%;
			top: 50%;
			margin-left: -405px;
			margin-top: -250px;
			position: absolute;
		}
		h1 {
			font-size: 2em;
		}
		h2 {
			color: #666;
		}
		</style>
		<script type="text/javascript">
		parent.loader("", "hide");
		</script>
	</head>
	<body>
		<div id="centered">
			<table style="width: 100%; text-align: center;" cellpadding="20" cellspacing="20">
				<tr>
					<td>
						<img src="$absolute_path/common/media/img/remix_cancel_256_ccc.png" />
					</td>
				</tr>
				<tr>
					<td>
						<h1>Ooops... qualcosa &egrave; andato storto!</h1>
						<h2>Stavi cercando <i>$path</i> ma &egrave; stato rimosso o il percorso &egrave; sbagliato</h2>
					</td>
				</tr>
			</table>
		</div>
	</body>
</html>
Error_contents;
		}
	}
	print $contents;
}
?>