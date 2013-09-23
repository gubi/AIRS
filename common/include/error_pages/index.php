<!DOCTYPE html>
<html>
	<head>
		<style type="text/css">
		html, body { margin: 0; height: 100%; }
		body { margin: 0; background: #fff; }
		object { border: 0px none; width: 100%; height: 100%; }
		</style>
	</head>
	<body>
		<object width="100" height="100" type="text/html" data="http://" . $_SERVER["HTTP_HOST"] . "/common/include/error_pages/error.php?error=<?php print $_GET["error"]; ?>"></object>
	</body>
</html>
