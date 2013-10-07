<?php
/**
* Creates a preview for Wiki contents editor
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
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
//require_once("FirePHPCore/FirePHP.class.php");
//$firephp = FirePHP::getInstance(true);
require_once("../../.mysql_connect.inc.php");

$absolute_path = "http://" . $_SERVER["HTTP_HOST"] . "/";
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
</head>
<body>
<div id="content_body">
	<?php
	if (isset($_POST["data"])){
		$data = $_POST["data"];
	} else {
		if (isset($_GET["type"]) && trim($_GET["type"]) == "chronology"){
			if (isset($_GET["id"]) && trim($_GET["id"]) !== ""){
				$content_chronology = $pdo->query("select * from airs_chronology where id = '" . addslashes($_GET["id"]) . "'");
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
	print stripslashes($output);
	?>
</div>
</body>
</html>