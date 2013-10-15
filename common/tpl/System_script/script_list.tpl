<?php
/**
* List all System scripts tree
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

require_once("common/tpl/_component_toolbar.tpl");
$content_body = <<<Tree
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/jquery-treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/jquery-treeview/jquery.treeview.edit.js"></script>
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/jquery-treeview/jquery.treeview.async.js"></script>
<link href='{ABSOLUTE_PATH}common/js/jquery-treeview/jquery.treeview.css' rel='stylesheet' type='text/css'>
<style>
#code {
	width: 100%;
	heght: 600px;
}
</style>
<script type="text/javascript">
function load_folder(dir, id) {
	if(!$("#" + id).hasClass("treeview")){
		$("#" + id).treeview({
			url: "{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/tree.php?path=" + dir
		});
	} else {
		if($("#" + id).css("display") != "none") {
			if($("#" + id).parent("li").hasClass("collapsable")){
				$("#" + id).parent("li").switchClass("collapsable", "expandable");
			} else {
				$("#" + id).parent("li").removeClass("lastCollapsable").addClass("lastExpandable");
			}
			$("#" + id).prev().prev().removeClass("collapsable-hitarea").addClass("expandable-hitarea");
			$("#" + id).css("display", "none");
		} else {
			if($("#" + id).parent("li").hasClass("expandable")){
				$("#" + id).parent("li").switchClass("expandable", "collapsable");
			} else {
				$("#" + id).parent("li").removeClass("lastExpandable").addClass("lastCollapsable");
			}
			$("#" + id).prev().prev().removeClass("expandable-hitarea").addClass("collapsable-hitarea");
			$("#" + id).css("display", "block");
		}
	}
}
function show_script(dir, file) {
	location.hash = dir;
	loader("Recupero lo script", "show");
	$.get("{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/read_script.php?", {filepath: dir}, function(data){
		if(data == "no results"){
			window.location = window.location.pathname;
			back_to_tree('$content_title', '$content_subtitle');
			loader("", "hide");
		} else {
			$("#tree").slideUp(600, function() {
				$("#code").slideDown(600, function(){
					var current_title = $(".title").text();
					var current_subtitle = $("#content_wrapper_title h2").text();
					$(".title").html('<a title="Torna all\'albero dei files" href="javascript: void(0);" onclick="back_to_tree(\'' + current_title + '\', \'' + current_subtitle + '\')">' + current_title + '</a>: ' + file);
					$("#content_wrapper_title h2").text('Visualizzazione del file');
						if(dir != file){
							var bread_dir = "",
							bread_track = "",
							separator = "",
							dir_bread = dir.split("/");
							
							$("#components_top_menu ul").html("");
							for(var i = 0; i < dir_bread.length; i++){
								bread_track += dir_bread[i] + "/";
								if(i > 0){
									separator = '<li><span style="color: #aaa;">/</span>';
								} else {
									separator = '<li><span style="color: #aaa; padding-left: 5px;">Posizione: </span>';
								}
								if(i < (dir_bread.length - 1)){
									$("#components_top_menu ul").append(separator + '</li><li><a title="Torna all\'albero dei files" href="javascript: void(0);" onclick="back_to_tree(\'' + current_title + '\', \'' + current_subtitle + '\');">' + dir_bread[i] + '</a></li>');
								} else {
									$("#components_top_menu ul").append(separator + '</li><li><a href="javascript: void(0);">' + dir_bread[i] + '</a></li>');
								}
							}
							$("#components_top_menu").slideDown(600);
						}
					var last_crumb = $("#breadcrumb li:last-child").text();
					$("#breadcrumb li:last-child").html('<a title="Torna all\'albero dei files" href="javascript: void(0);" onclick="back_to_tree(\'' + current_title + '\', \'' + current_subtitle + '\')">' + last_crumb + '</a>');
					$("#breadcrumb ul").append('<li class="last">Visualizzazione del file</li>');
					$("#code").html('<iframe style="border: #ccc 1px solid; width: 100%; height: 600px; box-shadow: 0 0 6px #ccc inset;" src="{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/read_script.php?filepath=' + dir + '"></iframe>');
				});
			});
		}
	});
}
function back_to_tree(title, subtitle){
	if(window.location.hash) {
		window.location = window.location.pathname;
	}
	$("#code").slideUp(600, function(){
		$("#code").html("");
		$(".title").html(title);
		$("#breadcrumb li.last").remove();
		$("#components_top_menu").slideUp(600, function(){ $("#components_top_menu ul").html(""); });
		var last_crumb = $("#breadcrumb li:last-child").text();
		$("#breadcrumb li:last-child").html(last_crumb);
		$("#content_wrapper_title h2").text(subtitle);
		$("#tree").fadeIn(600);
	});
}
$(document).ready(function(){
	if(window.location.hash) {
		var hash = window.location.hash.substring(1);
		var hashed = hash.split("/");
		hash_file = hashed[hashed.length-1];
		show_script(hash, hash_file);
	} else {
		loader("Recupero l'albero dei files", "show");
		$.get("{ABSOLUTE_PATH}common/include/funcs/_ajax/System_script/tree.php", function(data){
			$("#tree").html(data).treeview({
				speed: "fast", 
				collapsed: false
			});
			loader("", "hide");
		});
	}
});
</script>
<div id="code"></div>
<div id="tree"></div>
Tree;
require_once("common/include/conf/replacing_object_data.php");
?>