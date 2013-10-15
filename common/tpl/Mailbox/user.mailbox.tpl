<?php
/**
* Generates user main Mailbox page
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
* @package	AIRS_Mailbox
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

// Pagina dell'elenco

$img_special = "<img src=\"common/media/img/star_outline_16.png\" style=\"vertical-align: bottom;\" />";
$img_attachment = "<img src=\"common/media/img/attachment_16.png\" style=\"vertical-align: bottom;\" />";
$img_read = "<img src=\"common/media/img/viewer_text_16.png\" style=\"vertical-align: bottom;\" />";

$content_body .= <<<Research_list
<script src="{ABSOLUTE_PATH}common/js/jquery.cookie.js"></script>
<link href="{ABSOLUTE_PATH}common/js/flexigrid/css/flexigrid.css" rel="stylesheet" media="screen" />
<script src="{ABSOLUTE_PATH}common/js/flexigrid/js/flexigrid.js"></script>

<script src="{ABSOLUTE_PATH}common/js/jquery-contextmenu/src/jquery.contextMenu.js" type="text/javascript"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery-contextmenu/src/jquery.contextMenu.css" rel="stylesheet" media="screen" />
<script src="{ABSOLUTE_PATH}common/js/include/Mailbox/mailbox.js" type="text/javascript"></script>

<link href="{ABSOLUTE_PATH}common/js/jquery_treeview/jquery.treeview.css" rel="stylesheet" media="screen" />
<script src="{ABSOLUTE_PATH}common/js/jquery_treeview/jquery.treeview.js"></script>
<script src="{ABSOLUTE_PATH}common/js/jquery_treeview/jquery.treeview.async.js"></script>
<script language="javascript" type="text/javascript">
jQuery.download = function(url, data, method){
	if( url && data ){ 
		data = typeof data == 'string' ? data : jQuery.param(data);
		var inputs = '';
		jQuery.each(data.split('&'), function(){ 
			var pair = this.split('=');
			inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; 
		});
		jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>').appendTo('body').submit().remove();
	};
};
function GetColumnSize(percent){ 
	screen_res = (700/4)*0.95;
	col = parseInt((percent*(screen_res/100)));
	if (percent != 100){
		return col-18;
	} else {
		return col;
	}
}
function show(dir, id){
	if(id != undefined && id != null){
		$("#current_dir_id").val(id);
	}
	$("#current_dir").val(dir);
	$("#content_wrapper_title").find("h2").html("&rsaquo; " + $("#" + id).text());
	$("#message_body").html("");
	$("#message_list").flexOptions({url: "common/include/funcs/_ajax/Mailbox/check.php?user=$decrypted_user&check_all=true&check_dir=" + dir});
	$("#message_list").flexReload(); 
}
function read_unread(id, unread){
	if(unread = undefined || unread == null){
		unread = false;
	}
	if($("#read_" + id).hasClass("read") || unread == true){
		$.get("common/include/funcs/_ajax/Mailbox/flag.php", {user: "$decrypted_user", check_dir: $("#current_dir").val(), msg_id: id, type: "unread"}, function(data){
			datas = data.split(":");
			if(datas[0] == "ok"){
				$("#read_" + id).removeClass("read").html("<div>&bull;</div>");
				$("#subject" + id).removeClass("read");
				$("#date" + id).removeClass("read");
				if(datas[1] > 0){
					$("#mailbox_folder").find("#" + $("#current_dir_id").val()).addClass("unread").find(".count").text("(" + datas[1] + ")");
				} else {
					$("#mailbox_folder").find("#" + $("#current_dir_id").val()).removeClass("unread").find(".count").text("");
				}
			}
		});
	} else {
		$.get("common/include/funcs/_ajax/Mailbox/flag.php", {user: "$decrypted_user", check_dir: $("#current_dir").val(), msg_id: id, type: "read"}, function(data){
			datas = data.split(":");
			if(datas[0] == "ok"){
				$("#read_" + id).addClass("read").html("<div>&#9702;</div>");
				$("#subject" + id).addClass("read");
				$("#date" + id).addClass("read");
				if(datas[1] > 0){
					$("#mailbox_folder").find("#" + $("#current_dir_id").val()).addClass("unread").find(".count").text("(" + datas[1] + ")");
				} else {
					$("#mailbox_folder").find("#" + $("#current_dir_id").val()).removeClass("unread").find(".count").text("");
				}
			}
		});
	}
	return false;
}
function get_attachment(id, file){
	$.download('common/include/funcs/_ajax/Mailbox/download_attachment.php', 'user=$decrypted_user&filename=' + file + '&check_dir=' + $("#current_dir").val() + '&msg_id=' + id);
}
function read(celDiv, id) {
	$(celDiv).click(function() {
		$.get("common/include/funcs/_ajax/Mailbox/check.php", {user: "$decrypted_user", check_dir: $("#current_dir").val(), msg_id: id}, function(data){
			$("#mail_from").html(data.rows[0].from);
			$("#mail_subject").html(data.rows[0].subject);
			$("#mail_to").html(data.rows[0].to);
			$("#mail_data").html(data.rows[0].full_date);
			
			if(data.rows[0].attachments_count > 0) {
				var num_attachments = data.rows[0].attachments_count;
				if(num_attachments == 1){
					var allegato_txt = "allegato:";
				} else {
					var allegato_txt = "allegati:";
				}
				$("#attachments").html("<ul></ul>").prepend(num_attachments + " " + allegato_txt);
				$.each(data.rows[0].attachments, function(item, val){
					if(val.is_attachment) {
						$("#attachments ul").append('<li><a href="javascript:void(0);" onclick="get_attachment(\'' + id + '\', \'' + val.name + '\');">' + val.name + '</a></li>').parent().fadeIn(100);
					}
				});
			} else {
				$("#attachments").fadeOut(100, function(){ $("#attachments").html("<ul></ul>"); });
			}
			$("#message_body").html(data.rows[0].body);
			$("#selected_row").val(data.rows[0].id);
		}, "json");
		setTimeout(function() {
			$("#read_" + id).html("<div>&#9702;</div>");
			$("#subject" + id).addClass("read");
			$("#date" + id).addClass("read");
		}, 3000);
	});
}
function write(type){
	return false;
}
function rename(current_dir, new_dir){
	loader("Rinomino la directory...", "show");
	$.get("common/include/funcs/_ajax/Mailbox/rename.php", {user: "$decrypted_user", dir: current_dir, new: new_dir}, function(data){
		if(data == "ok"){
			$("#mailbox_folder ul").empty().treeview({
				url: "common/include/funcs/_ajax/Mailbox/check_folder.php?user=$decrypted_user"
			});
			loader("", "hide");
		} else {
			apprise(data);
		}
	});
}
function new_dir(current_dir, new_dir){
	loader("Creo la directory...", "show");
	$.get("common/include/funcs/_ajax/Mailbox/create_dir.php", {user: "$decrypted_user", dir: current_dir, new: new_dir}, function(data){
		if(data == "ok"){
			$("#mailbox_folder ul").empty().treeview({
				url: "common/include/funcs/_ajax/Mailbox/check_folder.php?user=$decrypted_user"
			});
			loader("", "hide");
		} else {
			apprise(data);
		}
	});
}
function clear(current_dir) {
	apprise("Si &egrave; sicuri di voler eliminare la directory e tutti i messaggi contenuti?", {"confirm": true}, function(q){
		if(q) {
			loader("Elimino la directory...", "show");
			$.get("common/include/funcs/_ajax/Mailbox/remove_dir.php", {user: "$decrypted_user", dir: current_dir}, function(data){
				if(data == "ok"){
					$("#mailbox_folder ul").empty().treeview({
						url: "common/include/funcs/_ajax/Mailbox/check_folder.php?user=$decrypted_user"
					});
					loader("", "hide");
				} else {
					apprise(data);
				}
			});
		}
	});
}
function empty(current_dir) {
	loader("Svuoto la directory...", "show");
	$.get("common/include/funcs/_ajax/Mailbox/empty.php", {user: "$decrypted_user", dir: current_dir, type: "empty"}, function(data){
		if(data == "ok"){
			show(current_dir);
			loader("", "hide");
		} else {
			apprise(data);
		}
	});
}
function empty_trash(current_dir) {
	loader("Svuoto il cestino...", "show");
	$.get("common/include/funcs/_ajax/Mailbox/empty.php", {user: "$decrypted_user", dir: current_dir, type: "empty_trash"}, function(data){
		if(data == "ok"){
			show(current_dir);
			loader("", "hide");
		} else {
			apprise(data);
		}
	});
}
function set_as_read(current_dir){
	$.get("common/include/funcs/_ajax/Mailbox/set_as_read.php", {user: "$decrypted_user", dir: current_dir}, function(data){
		if(data == "ok"){
			show(current_dir);
			$("#mailbox_folder").find("#" + $("#current_dir_id").val()).removeClass("unread").find(".count").text("");
		} else {
			apprise(data);
		}
	});
}
function moveTo(folder){
	$.get("common/include/funcs/_ajax/Mailbox/move_to.php", {user: "$decrypted_user", dir: $("#current_dir").val(), id: $("#selected_row").val(), move_to_folder: "INBOX." + folder}, function(data){
		if(data == "ok"){
			show($("#current_dir").val());
		} else {
			apprise(data);
		}
	});
}
$(document).ready(function() {
	$('.context-menu-one').on('click', function(e){
		
	});
	$("fieldset.legend").click(function(){
		if($(this).hasClass("collapsed")){
			$(this).switchClass("collapsed", "", 300).find("p.indication").slideUp(250);
			$(this).find("div#legend_content").slideDown(300);
		} else {
			$(this).switchClass("", "collapsed", 300).find("p.indication").slideDown(250);
			$(this).find("div#legend_content").slideUp(300);
		}
	});
	$("#selected_row").val("");
	$("#message_list").flexigrid({
		colModel : [
			{display: '$img_special', name: 'special', width: 27, sortable: false, align: 'center'},
			{display: '$img_attachment', name: 'attachment_icon', width: 27, sortable: false, valign: 'middle', align: 'center', process: read},
			{display: 'Oggetto', name: 'subject', width: GetColumnSize(500), sortable: false, align: 'left', process: read},
			{display: '$img_read', name: 'read', width: 27, sortable: false, align: 'center'},
			{display: 'Mittente', name: 'from', width: 150, sortable: false, align: 'left', process: read},
			{display: 'Data', name: 'date', width: 130, sortable: false, align: 'left', process: read}
		],
		searchitems : [
			{display: 'Oggetto', name: 'subject', isdefault: false},
			{display: 'Corpo del messaggio', name: 'body', isdefault: false},
			{display: 'Mittente', name: 'from', isdefault: false},
			{display: 'Data (AAA-MM-GG)', name: 'date'}
		],
		url: "common/include/funcs/_ajax/Mailbox/check.php?user=$decrypted_user&check_dir=" + $("#current_dir").val() + "&check_all=true",
		hidable: false,
		showTableToggleBtn: true,
		method: 'GET',
		dataType: "json",
		sortorder: "asc",
		usepager: true,
		useRp: true,
		rp: 15,
		width: "auto",
		height: 290.25,
		singleSelect: true,
		onSuccess: function(data){
			$("#row" + data.total).addClass("trSelected");
			var i = 0;
			$.each(data.rows, function(key, value) { 
				i++;
				if(i == 1){
					setTimeout(function() {
						$("#read_" + data.total).html("<div>&#9702;</div>");
						$("#subject" + data.total).addClass("read");
						$("#date" + data.total).addClass("read");
					}, 5000);
					$("#mail_from").html(value.from);
					$("#mail_subject").html(value.subject);
					$("#mail_to").html(value.to);
					$("#mail_data").html(value.full_date);
					
					if(value.attachments_count > 0) {
						var num_attachments = value.attachments_count;
						if(num_attachments == 1){
							var allegato_txt = "allegato:";
						} else {
							var allegato_txt = "allegati:";
						}
						$("#attachments").html("<ul></ul>").prepend(num_attachments + " " + allegato_txt);
						$.each(value.attachments, function(item, val){
							if(val.is_attachment) {
								$("#attachments ul").append('<li><a href="javascript:void(0);" onclick="get_attachment(\'' + val.id + '\', \'' + val.name + '\');">' + val.name + '</a></li>').parent().fadeIn(100);
							}
						});
					} else {
						$("#attachments").fadeOut(100, function(){ $("#attachments").html("<ul></ul>"); });
					}
					$("#message_body").html(value.body);
					$("#selected_row").val(value.id);
					if(data.unseen_count > 0){
						$("#mailbox_folder").find("#" + $("#current_dir_id").val()).addClass("unread").find(".count").text("(" + data.unseen_count + ")");
					}
				}
			});
		},
		onError: function(XMLHttpRequest, textStatus, errorThrown){
			$("#message_list").html("<center><i>Nessun messaggio presente in questa directory</i></center>");
		}
	});
	$("#mailbox_folder ul").treeview({
		url: "common/include/funcs/_ajax/Mailbox/check_folder.php?user=$decrypted_user",
		dataLoaded: function(data){
			$("#content_wrapper_title").find("h2").html("&rsaquo; " + data[0].name);
		}
	});
});
</script>
<fieldset class="legend">
	<legend class="info">Informazioni utili</legend>
	<p class="indication">Fare click per visualizzare il contenuto condensato</p>
	<div id="legend_content">
		<p class="time">La tabella si autoaggiorna ogni 5 minuti</p>
		<!--<p class="filter">&Egrave; possibile filtrare i risultati nel menu in fondo alla tabella</p>-->
		<p class="activated">Selezionando i risultati si attiveranno le funzioni su di essi</p>
	</div>
</fieldset>
<input type="hidden" id="current_dir" value="INBOX" />
<input type="hidden" id="current_dir_id" value="inbox" />
<input type="hidden" id="selected_row" value="" />
<table id="mailbox" cellpadding="0" cellspacing="0">
	<tr>
		<td rowspan="3" valign="top">
			<div class="flexigrid">
				<div class="tDiv">
					<div class="tDiv2">
						<div class="fbutton"><div><span class="pWriteMail" style="padding-left: 20px;" title="Nuovo messaggio" onclick="window.location.href='./Mailbox/Nuovo';"></span></div></div>
						<div class="btnseparator"></div>
						<div class="fbutton"><div><span class="pBookmark" style="padding-left: 20px;" title="Rubrica" onclick="window.location.href='./Mailbox/Rubrica';"></span></div></div>
						<div class="btnseparator"></div>
					</div>
				</div>
			</div>
			<div id="mailbox_folder"><ul class="filetree"></ul></div>
		</td>
		<td>
			<div style="width: 100%; overflow-x: hidden;">
				<table id="message_list" class="flexigrid flexigrid_long_autorefresh"></table>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="message_body_controls" class="flexigrid">
				<div class="tDiv">
					<div class="tDiv2">
						<div style="float: right;">
							<div class="btnseparator_small"></div>
							<div class="fbutton"><div><span class="pReply context-menu-one" style="padding-left: 20px;" title="Rispondi">Rispondi</span></div></div>
							<div class="btnseparator_small"></div>
							<div class="fbutton"><div><span class="pForward" style="padding-left: 20px;" title="Inoltra" onclick="write('forward');">Inoltra</span></div></div>
							<div class="btnseparator_small"></div>
							<div class="fbutton"><div><span class="pSpam" style="padding-left: 20px;" title="Indesiderata" onclick="moveTo('Junk');">Indesiderata</span></div></div>
							<div class="btnseparator_small"></div>
							<div class="fbutton"><div><span class="pDelete" style="padding-left: 20px;" title="Elimina" onclick="moveTo('Trash');">Elimina</span></div></div>
							
							<div id="mail_data"></div>
						</div>
						<table id="message_data" cellspacing="5" cellpadding="0">
							<tr>
								<th>Da:</th>
								<td id="mail_from">&nbsp;</td>
							</tr>
							<tr>
								<th>Oggetto:</th>
								<td id="mail_subject">&nbsp;</td>
							</tr>
							<tr>
								<th>A:</th>
								<td id="mail_to">&nbsp;</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="attachments"><ul></ul></div>
			<div id="message_body"></div>
		</td>
	</tr>
</table>
Research_list;

require_once("common/include/conf/replacing_object_data.php");
?>