<?php
header("Content-type: text/plain; charset=utf-8");
if(strlen($_SERVER["DOCUMENT_ROOT"]) == 0){
	$_SERVER["DOCUMENT_ROOT"] = "/var/www-dev/";
}
require_once($_SERVER["DOCUMENT_ROOT"] . "common/include/classes/class.system_automation.php");

$params = array();
if(isset($_GET["id"]) && trim($_GET["id"]) !== "") {
	$params["id"] = $_GET["id"];
}
if(isset($_GET["user"]) && trim($_GET["user"]) !== "") {
	$params["user"] = $_GET["user"];
}
if(isset($_GET["type"]) && trim($_GET["type"]) !== "") {
	$params["type"] = $_GET["type"];
}
if(isset($_GET["force_run"]) && trim($_GET["force_run"]) !== "") {
	$params["force_run"] = $_GET["force_run"];
}
$automation = new System_automation($params);
print $automation->start();
?>