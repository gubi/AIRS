<!DOCTYPE html>
<html>
	<head>
		<script type="text/javascript" src="http://<?php print $_SERVER["HTTP_HOST"]; ?>/common/include/error_pages/polymaps.min.js"></script>
		<style type="text/css">
		html, body {
			height: 100%;
		}
		body {
			margin: 0;
			background: #E5E0D9;
		}
		svg {
			display: block;
			overflow: hidden;
			width: 100%;
			height: 100%;
		}
		.zoom {
			display: none;
		}
		#map {
			background: #000;
			width: 100%;
			height: 100%;
			position: absolute;
			z-index: 1;
		}
		#message {
			font-family: Arial;
			position: absolute;
			z-index: 2;
			width: 25em;
			right: 0;
			text-align: left;
			color:#000;
			text-shadow: 0 0 0.5em #ccc;
		}
		#message h1 {
			font-size: 5em;
			margin: 1.75em 0 0.15em 0;
		}
		#message h2 {
			margin: 0 0 0.25em 0;
		}
		#message h3 {
			margin: 0;
			color: #454545;
		}
		.compass .back {
			fill: #256574;
		}
		.compass .fore, .compass .chevron {
			stroke: #1AA398;
		}
		#copy, #copy a {
			color: #1AA398;
		}
		</style>
	</head>
	<body>
		<?php
		if (isset($_GET["error"]) && trim($_GET["error"]) !== ""){
			switch ($_GET["error"]){
				case "404":
					$error_txt = "Questo &egrave; un errore 404 e la pagina non esiste!";
					break;
				default:
					$error_txt = "Si &egrave; verificato un errore!";
					break;
			}
			?>
			<div id="message">
				<h1>Whooh...</h1>
				<h2>Ti sei perso?</h2>
				<h3><?php print $error_txt; ?></h3>
			</div>
			<?php
		}
		?>
		<div id="overviewMap" class="map"></div>
		<div id="map">
			<script type="text/javascript">
			var style = "<?php print $_GET["style"]; ?>";
			var po_url = "";
			var po = org.polymaps;
			var map = po.map().center({lat: 41.8882, lon: 12.5164}).zoom(12).container(document.getElementById("map").appendChild(po.svg("svg"))).add(po.interact()).add(po.hash());
			po_url = po.url("http://{S}tile.cloudmade.com/4a3e88c358364821af72dc503316eaa6/998/256/{Z}/{X}/{Y}.png");
			map.add(po.image().url(po_url.hosts(["a.", "b.", "c.", ""])));
			map.add(po.compass().pan("none"));
			</script>
		</div>
	</body>
</html>
