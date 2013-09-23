<?php
require_once("../../../include/.mysql_connect.inc.php");

$use_sts = true;
if($use_sts && !isset($_SERVER['HTTPS'])){
	$http = "http://";
} else {
	$http = "https://";
}
if($_SERVER["SERVER_PORT"] !== "80" && $_SERVER["SERVER_PORT"] !== "443"){
	$port = ":" . $_SERVER["SERVER_PORT"];
} else {
	$port = "";
}
$absolute_path = $http . $_SERVER["SERVER_NAME"] . $port . "/";
$pdo = db_connect("");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<base href="<?php print $absolute_path; ?>" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Anteprima della pagina</title>
<link rel="stylesheet" type="text/css" href="<?php print $absolute_path; ?>common/css/main.css" />
<link rel="stylesheet" type="text/css" href="<?php print $absolute_path; ?>common/js/markitup/templates/preview.css" />
<style>
a[target^="_blank"] {
	background-image: none !important;
}
</style>
</head>
<body>
<div id="content_body">
	<?php
	if (isset($_GET["type"]) && trim($_GET["type"]) == "html"){
		require_once("../../../include/funcs/_create_link.php");
		require_once("../../../include/funcs/_parse_content.php");
		print strip_script_tags(preg_replace(array("/li\>\n\s<li/is", "/(\s\n)/", "/\n+/", "/\n/"), array("li><li", "\n", "\n  ", "<br />"), $_POST["data"]));
	} else {
		$GLOBALS["page_m"] = $_GET["page"];
		
		if (isset($_POST["data"])){
			$data = $_POST["data"];
		} else {
			if (isset($_GET["type"]) && trim($_GET["type"]) == "chronology"){
				if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
					$content_chronology = $pdo->query("select * from `airs_chronology` where id = '" . addslashes($_GET["id"]) . "'");
					if ($content_chronology->rowCount() > 0){
						while ($dato_content_chronology = $content_chronology->fetch()){
							$data = $dato_content_chronology["body"];
						}
					}
				}
			}
		}
		require_once("Text/Wiki.php");
		require_once("../../../include/conf/Wiki/rendering.php");
		$output = $wiki->transform(stripslashes(utf8_decode($data)), "Xhtml");
		$output = str_replace("<a", "<a target=\"_blank\"", $output);
		print stripslashes(utf8_encode($output));
	}
	?>
</div>
</body>
</html>