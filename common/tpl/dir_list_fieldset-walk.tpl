<div>
	<!-- Jquery treeview -->
	<link rel="stylesheet" href="common/js/jquery_treeview/jquery.treeview.css" type="text/css" media="screen" />
	<script type="text/javascript" src="common/js/jquery_treeview/jquery.treeview.js"></script>
	<script type="text/javascript">
	<!-- 
	function load_directories() { $("body").css("cursor", "progress"); $("#fieldset_content").html(""); $("#list_dir_btn_id").remove(); $("#treeview_fieldset").css({ padding: "5px 10px" }).find("legend").css("margin-top", "-30px"); $(".loader").css({minHeight: "100px", display: "block"}); $.get("common/tpl/_dir_list.tpl.php", function(data){ $("#fieldset_content").css("display", "none").html(data); $(".loader").fadeOut(300, function(){ $("#tree").treeview({ animated: "fast", collapsed: true, unique: true, control:"#sidetreecontrol", persist: "location" }); $("#sidetreecontrol").css("display", "block"); $("#fieldset_content").css("display", "block"); $("body").css("cursor", "") }); }); }
	function load_file(uri){ var url = uri.replace("../../", ""); $("#content_wrapper_main_content").css("display", "none"); $.get(url, function(data){ $("#content_wrapper_dynamic_content").html("<pre>" + data + "</pre>"); sh_highlightDocument(); }, "text"); }
	-->
	</script>
	<h1>DIRECTORIES</h1>
	<div id="treeview_fieldset">
		<p>
			<ul>
				<li>Percorso assoluto: <tt><?php print $_SERVER["DOCUMENT_ROOT"]; ?></tt></li>
				<li>Directory corrente: <tt><?php print $_SERVER["SCRIPT_NAME"]; ?></tt></li>
				<li>Query string: <tt><?php print $_SERVER["QUERY_STRING"]; ?></tt></li>
				<li id="list_dir_btn_id" style="padding-top: 10px;"><a href="javascript: void(0);" class="list_dir_btn" onclick="load_directories()" title="Carica l'elenco delle cartelle (richiesta asincrona)">Lista directory</a></li>
			</ul>
			<ul id="sidetreecontrol" style="display: none;"> 
				<li><a title="Chiude tutti gli elementi dell'albero" href="#"><img src="common/js/jquery_treeview/images/minus.gif" /> Chiudi tutto</a></li>
				<li><a title="Apre tutto l'albero" href="#"><img src="common/js/jquery_treeview/images/plus.gif" /> Espandi tutto</a></li>
				<li><a title="Aggiorna l'elenco delle cartelle" href="javascript: void(0);" onclick="load_directories()">Aggiorna</a></li>
			</ul>
		</p>
	</div>
	<div class="loader" style="display: none;"></div>
	<div id="fieldset_content" style="display: none;"></div>
</div>