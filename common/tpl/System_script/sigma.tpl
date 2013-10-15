<?php
/**
* Generates System script inclusion graph
* 
* PHP versions 4 and 5
*
* LICENSE: This source file is subject to version 3.0 of the PHP license
* that is available through the world-wide-web at the following URI:
* http://www.php.net/license/3_0.txt.  If you did not receive a copy of
* the PHP License and are unable to obtain it through the web, please
* send a note to license@php.net so we can mail you a copy immediately.
*
* @category	SystemScript
* @package	AIRS_System_script
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/
?>
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/graphs/sigma/sigma.js"></script>
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/graphs/sigma/plugins/sigma.parseGexf.js"></script>
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/graphs/sigma/plugins/sigma.forceatlas2.js"></script>
<link href='{ABSOLUTE_PATH}common/js/graphs/sigma/sigma.css' rel='stylesheet' type='text/css'>
<script type="text/javascript">
function show_fullscreen(){
	if($("#mind_map").hasClass("fullscreen")){
		$("#mind_map").removeClass("fullscreen").html("");
		$("body").css("position", "inherit");
		$(".sigma-parent").css("border-radius", "4px");
		$("#airs_logo").removeClass("fullscreen");
		$("#mind_map-footnotes").fadeOut(300, function(){ $(this).remove(); });
		$("#mind_map_search").hide();
	} else {
		$("#mind_map").addClass("fullscreen").html("");
		$("#mind_map_search input").val("").focus();
		$(".sigma-parent").css("border-radius", "0");
		$("body").css("position", "fixed");
		$("#airs_logo").addClass("fullscreen");
		$("#mind_map-parent").prepend('<div id="mind_map-footnotes">Visualizzazione grafica della "mente" di AIRS: collegamenti tra script.</div>');
		$("#mind_map_search").fadeIn(600, function(){ $("#mind_map_search input").focus(); });
		
		var timer = null;
		$("#mind_map_search input").keyup(function(e) {
			clearTimeout(timer);
			timer = setTimeout(function(){
				sigma.instances[1].iterNodes(function(node){
					node.active = false;
					node.forceLabel = false;
					if(node.attr["true_size"]){
						node.displaySize -= 500;
						node.size -= 500;
						node.degree -= 500;
					}
				}).draw();
				
				var inp = String.fromCharCode(e.keyCode);
				if (/[a-zA-Z0-9-_ .]/.test(inp)) {
					$.get("{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/search_script.php", {term: $("#mind_map_search input").val()}, function(result){
						if(result.results != null && result.results.length > 0){
							$.each(result.results, function(r, res){
								sigma.instances[1].iterNodes(function(node){
									if(node.label == res.label){
										node.active = true;
										node.forceLabel = true;
										node.attr["true_size"] = node.size;
										node.displaySize += 500;
										node.size += 500;
										node.degree += 500;
									}
								}).draw();
							});
						} else {
							sigma.instances[1].iterNodes(function(node){
								node.active = false;
								node.forceLabel = false;
								if(node.attr["true_size"]){
									node.displaySize -= 500;
									node.size -= 500;
									node.degree -= 500;
								}
							}).draw();
						}
					}, "json");
				}
			}, 500);
		});
	}
	$("mind_map").html("");
	if($("#started").val() == "true"){
		sigma.instances[0].refresh();
	} else {
		init();
	}
}
function init() {
	sigma.publicPrototype.hoverNode = function() {
		sigInst.bind('overnodes', function(node) {
			$(".sigma-parent").css("cursor", "pointer");
		}).bind('outnodes', function(node){
			$(".sigma-parent").css("cursor", "auto");
			node.size -= 5000;
		}).draw();
	};
	sigma.publicPrototype.preRenderSize = function() {
		this.iterNodes(function(node){
			node.size = node.outDegree + 50;
		}).draw();
	};
	sigma.publicPrototype.highlightNode = function(){
		var popUp;
		
		function attributesToString(attr) {
			return '' + attr.map(function(o){
				return '' + o.attr + ' : ' + o.val + '';
			}).join('') + '';
		};
		var tooltip = {
			showNodeInfo: function(event) {
				popUp && popUp.remove();
				var node, popUpLabel = "", popUpLink = "";
				sigInst.iterNodes(function(n){
					node = n;
				},[event.content[0]]);
				for(var i = 0; i < node['attr']['attributes'].length; i++){
					if(node['attr']['attributes'][i]["val"] == "undetected link" || node['attr']['attributes'][i]["val"] == ""){
						popUpLink = "non &egrave; stato possibile acquisire il link";
					} else {
						var popUpLinks = node['attr']['attributes'][i]["val"].split(",");
						if(popUpLinks.length > 1){
							popUpLink = "<ul>";
							for(var l = 0; l < popUpLinks.length; l++){
								popUpLink += '<li><span style="font-family: monospace;">' + popUpLinks[l] + '</span></li>';
							}
							popUpLink += "</ul>";
						} else {
							popUpLink = '<span style="font-family: monospace;">' + popUpLinks + '</span>';
						}
						if(node['attr']['attributes'][i]["attr"] == "Tipo di relazione"){
							popUpLink = '<span style="color: ' + node["color"] + ';">' + popUpLink + '</span>';
						}
					}
					if(node['attr']['attributes'][i]["attr"] == "sep"){
						popUpLink = "";
						popUpLabel += '</ul><br /><ul style="padding: 0;">';
					} else {
						popUpLabel += "<li>" + node['attr']['attributes'][i]["attr"] + ': ' + popUpLink + "</li>";
					}
				}
				popUp = $('<div class="node-info-popup"></div>').append(
					'<ul style="padding: 0;">' + popUpLabel + "</ul>"
				).attr('id','node-info' + sigInst.getID()
				).css({
					'display': 'inline-block',
					'border-radius': 3,
					'padding': 5,
					'background': 'rgba(255, 255, 255, 0.1)',
					'color': '#fff',
					'box-shadow': '0 0 6px #fff',
					'position': 'absolute',
					'left': node.displayX-10,
					'top': node.displayY+15
				});
				$('ul', popUp).css('margin', '0 10px 0 20px');
				$('#mind_map').append(popUp);
				
				node.displaySize += 500;
				node.size += 500;
				node.degree += 500;
			},
			hideNodeInfo: function(event) {
				popUp && popUp.remove();
				popUp = false;
			}
		};
		sigInst.bind('overnodes', tooltip.showNodeInfo).bind('outnodes', tooltip.hideNodeInfo).draw();
	};
	var sigInst = sigma.init($('#mind_map')[0]).drawingProperties({
		defaultLabelColor: 'rgba(0,0,0,0.5)',
		defaultLabelBGColor: 'rgba(255, 255, 255, 0.25)',
		defaultHoverLabelBGColor: 'rgba(255, 255, 255, 0.5)',
		defaultLabelSize: 16,
		defaultLabelColor: '#fff',
		defaultLabelHoverColor: '#333',
		font: 'Ubuntu',
		labelThreshold: 6,
		labelHoverShadowColor: '#fff',
		defaultEdgeType: 'curve',
		borderSize: 100,
		nodeBorderColor: "default",
		defaultNodeBorderColor: "rgba(255, 255, 255, 0)",
		defaultBorderView: "always"
	}).graphProperties({
		minNodeSize: 0.5,
		maxNodeSize: 5,
		minEdgeSize: 1,
		maxEdgeSize: 1
	}).mouseProperties({
		maxRatio: 32
	});
	sigInst.parseGexf('{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/gexf.php');
	
	
	var greyColor = '#232323';
	sigInst.bind('overnodes', function(event){
		var nodes = event.content;
		var neighbors = {};
		sigInst.iterEdges(function(e){
			if(nodes.indexOf(e.source)<0 && nodes.indexOf(e.target)<0){
				if(!e.attr['grey']){
					e.attr['true_color'] = e.color;
					e.color = greyColor;
					e.attr['grey'] = 1;
				}
			} else {
				e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
				e.attr['grey'] = 0;
				
				neighbors[e.source] = 1;
				neighbors[e.target] = 1;
			}
		}).iterNodes(function(n){
			if(!neighbors[n.id]){
				if(!n.attr['grey']){
					n.attr['true_color'] = n.color;
					n.color = greyColor;
					n.attr['grey'] = 1;
				}
			} else {
				n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
				n.attr['grey'] = 0;
			}
		}).draw(2,2,2);
	}).bind('outnodes',function(){
		sigInst.iterEdges(function(e){
			e.color = e.attr['grey'] ? e.attr['true_color'] : e.color;
			e.attr['grey'] = 0;
		}).iterNodes(function(n){
			n.color = n.attr['grey'] ? n.attr['true_color'] : n.color;
			n.attr['grey'] = 0;
		}).draw(2,2,2);
	}).bind('downnodes',function(event){
		var node;
		sigInst.iterNodes(function(n){
			node = n;
		},[event.content[0]]);
		zoombox.open("{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/read_script.php?filepath=" + node['attr']['attributes'][5]["val"]);
		
		return false;
	});
	sigInst.draw();
	/*sigInst.startForceAtlas2();*/
	sigInst.preRenderSize();
	sigInst.highlightNode();
	sigInst.hoverNode();
	
	$("#started").val("true");
}
$(document).ready(function(){
	if(window.location.hash) {
		var hash = window.location.hash.substring(1),
		hashed = hash.split("/"),
		hash_name = hashed[hashed.length-1];
		
		if(hash_name == "Mindmap"){
			show_fullscreen()
		}
	} else {
		init();
	}
	$(".sigma-parent").mousemove(function(){
		$(".sigma-parent").mousedown(function(){
			$(".sigma-parent").css("cursor", "move");
		});
		$(".sigma-parent").mouseup(function(){
			$(".sigma-parent").css("cursor", "auto");
		});
	});
});
$(document).keyup(function(e) {
	if (e.keyCode == 27 && $("#mind_map").hasClass("fullscreen")) { show_fullscreen() }
});
</script>
<a id="goto_script" rel="zoombox" style="display: none;"></a>
<input type="hidden" value="" autocomplete="off" />
Doppio click per attivare o disattivare la fisualizzazione a schermo intero
<div id="mind_map-parent">
	<div id="mind_map_search">
		<input type="text" placeholder="Cerca uno script" value="" />
	</div>
	<div id="mind_map" class="sigma-parent" ondblclick="show_fullscreen()"></div>
</div>
<br />
<b>Visualizzazione grafica della "mente" di AIRS</b>: collegamenti tra script.<br />
Facendo doppio click sul riquadro &egrave; possibile consultarla a schermo intero.