<?php
header('Content-Type: text/html; charset=utf-8'); 

require_once("../_blowfish.php");
require_once("../../classes/class.rsa.php");
$rsa = new rsa();
$fb = fopen("common/include/conf/rsa_2048_pub.pem", "r");
$Skey = fread($fb, 8192);
fclose($fb);
$Stoken = $rsa->get_token($Skey);
$rsa_encrypted = $rsa->simple_private_encrypt($Stoken);
$absolute_path = "http://" . $_SERVER["HTTP_HOST"] . "/";

$key = $rsa_encrypted;
$crypted_user_key = $_COOKIE["iack"];
$decrypted_user = PMA_blowfish_decrypt($_COOKIE["iac"], $_COOKIE["iack"]);
$type = $_GET["type"];

$content_body = <<<Mind_map
<script type="text/javascript" src="../../../js/jquery-1.6.min.js"></script>
<script type="text/javascript" src="../../../js/jquery_ui_effects/ui/jquery-ui.min.js"></script>
<link type="text/css" href="../../../js/Jit/Examples/css/base.css" rel="stylesheet" />
<link type="text/css" href="../../../js/Jit/Examples/css/Spacetree.css" rel="stylesheet" />
<script language="javascript" type="text/javascript" src="../../../js/Jit/jit-yc.js"></script>
<script language="javascript" type="text/javascript">
var absolute_path = "$absolute_path";
var labelType, useGradients, nativeTextSupport, animate;
(function() {
	var ua = navigator.userAgent, 
	iStuff = ua.match(/iPhone/i) || ua.match(/iPad/i),
	typeOfCanvas = typeof HTMLCanvasElement,
	nativeCanvasSupport = (typeOfCanvas == 'object' || typeOfCanvas == 'function'),
	textSupport = nativeCanvasSupport 
	&& (typeof document.createElement('canvas').getContext('2d').fillText == 'function');
	//I'm setting this based on the fact that ExCanvas provides text support for IE
	//and that as of today iPhone/iPad current text support is lame
	labelType = (!nativeCanvasSupport || (textSupport && !iStuff))? 'Native' : 'HTML';
	nativeTextSupport = labelType == 'Native';
	useGradients = nativeCanvasSupport;
	animate = !(iStuff || !nativeCanvasSupport);
})();
function init(){
	var getTree = (function() {
		var i = 0;
		return function(json, nodeId, level) {
			var subtree = eval('(' + json.replace(/id:\"(.*?)\"/g, function(all, match) {
				return "id:\"" + match + "_" + i + "\""  
			}) + ')');;
			\$jit.json.prune(subtree, level);
			i++;
			return {
				'id': nodeId,
				'children': subtree.children
			};
		};
	})();;
	$.get("../_ajax/EditoRSS/feed_list.php", {user: "$decrypted_user", type: "$type"}, function(data){
		var st = new \$jit.ST({
			orientation: "left", 
			levelsToShow: 1,
			subtreeOffset: 2,
			siblingOffset: 1,
			withLabels: true,
			align: "center", 
			multitree: false,
			indent: 10, 
			injectInto: 'infovis',
			duration: 450,
			transition: \$jit.Trans.Quart.easeInOut, 
			levelDistance: 50,
			
			Navigation: {
				enable: true,
				panning: true
			},
			Node: {
				height: 30,
				width: 80,
				type: 'ellipse',
				lineWidth: 2,
				align: "left",
				color: '#aaa',
				overridable: true
			},
			Edge: {
				type: 'bezier',
				lineWidth: 2,
				overridable: true,
				color: '#eed'
			},
			Tips: {
				enable: true,
				onShow: function(tip, node) {
					var count = -1;
					node.eachAdjacency(function() { count++; });
					tip.innerHTML = "<div class=\"tip-title\"><b>" + node.data.name + "</b></div>";
					if (node.data.level < 3){
						if (node.data.level > 0 && count > 0){
							if (count == 1){
								var connections_txt = "connessione";
							} else {
								var connections_txt = "connessioni";
							}
							tip.innerHTML += "<br /><div class=\"tip-text\">" + count + " " + connections_txt + "</div>";
						}
					} else {
						tip.innerHTML += "<br /><div class=\"tip-text\">" + node.data.description + "</div>";
						tip.innerHTML += "<br /><div class=\"tip-text\">Tag: " + node.data.tag + "</div>";
					}
				}
			},
			Events: {
				enable: true,
				type: 'Native',
				enableForEdges: true
			},
			request: function(nodeId, level, onComplete) {
				var json = $.get("../_ajax/EditoRSS/feed_list.php", {user: "$decrypted_user", type: "$type", node: nodeId}, function(data) {
					var json = data;
					var ans = getTree(json, nodeId, level);
					onComplete.onComplete(nodeId, ans);  
				}, "text");
			},
			onCreateLabel: function(label, node){
				label.id = node.id;
				label.innerHTML = node.name;
				label.onclick = function(){
					st.onClick(node.id);
					if (node.data.level >= 3){
							console.log(node.data.level);
						$("#selected_item").html("Selezionato: <b>" + node.name + "</b>");
						$("#lateral_panel").fadeIn(600);
						$("#edit").click(function(){
							if (node.data.level == 3){
								location.href = absolute_path + "EditoRSS/Feeds/" + node.data.id, "_blank";
							} else {
								location.href = absolute_path + "EditoRSS/News/" + node.data.id, "_blank";
							}
						});
						$("#delete").click(function(){
							parent.apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/cancel_128_ccc.png\" /></td><td><h1>Si è sicuri di voler rimuovere il feed?</h1>Il feed <b>" + node.name + "</b> verrà rimosso dall'archivio ma le news afferenti rimarranno indicizzate.</td></tr></table>", {"verify": true});
						});
					} else {
						$("#selected_item").html("");
						$("#lateral_panel").fadeOut(600);
					}
					//st.setRoot(node.id, 'animate');
				};
				var style = label.style;
				style.width = 80 + 'px';
				style.height = 27 + 'px';
				style.cursor = 'pointer';
				style.color = '#333';
				style.fontSize = '11px';
				style.fontFamily = 'Arial, Helvetica';
				style.textAlign= 'center';
				style.paddingTop = '9px';
				if (node.selected) {
					style.fontWeight = 'bold';
				}
				if (node.id.length > 10){
					label.style.width = node.id.length*6 + 30 + 'px';
				}
			},
			onBeforePlotNode: function(node){
				if (node.id.length > 10){
					if (node.data.level == 3){
						node.data.\$width = node.id.length*6 + 50;
					} else {
						node.data.\$width = node.id.length*6 + 30;
					}
				}
				if (node.selected) {
					node.data.\$color = "#ff7";
				} else {
					delete node.data.\$color;
					if(!node.anySubnode("exist")) {
						var count = 0;
						node.eachSubnode(function(n) {
							count++;
						});
						node.data.\$color = ['#ccc', '#baa', '#caa', '#daa', '#eaa', '#faa', '#abb', '#bbb', '#cbb', '#dbb', '#ebb', '#fbb', '#acc', '#bcc', '#ccc', '#dcc', '#ecc', '#fcc', '#add', '#bdd', '#cdd', '#ddd', '#edd', '#fdd', '#aee', '#bee', '#cee', '#dee', '#eee', '#fee'][count];
					}
				}
			},
			onBeforePlotLine: function(adj){
				if (adj.nodeFrom.selected && adj.nodeTo.selected) {
					adj.data.\$color = "#999";
					adj.data.\$lineWidth = 2;
				} else {
					delete adj.data.\$color;
					delete adj.data.\$lineWidth;
				}
			}
		});
		st.loadJSON(data);
		st.compute();
		st.geom.translate(new \$jit.Complex(-350, 0), "current");
		st.onClick(st.root, {
			Move: {
				offsetX: 150
			}
		});
	}, "json");
}
$(document).ready(function() {
	init();
});
</script>
<div id="lateral_panel"><table><tr><td id="edit">MODIFICA</td></tr><tr><td id="delete">RIMUOVI</td></tr></table></div>
<div id="selected_item"></div>
<div id="infovis"></div>
Mind_map;

$origin = "ext";
require_once("../../conf/replacing_object_data.php");

print $content_body;
?>