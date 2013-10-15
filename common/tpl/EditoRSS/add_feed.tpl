<?php
/**
* Generates form for add feed
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
* @package	AIRS_EditoRSS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	testing
*/

$pdo = db_connect("editorss");

$ec = 0;
$editor_type = $pdo->query("select * from `editorss_origins_type`");
if ($editor_type->rowCount() > 0){
	while ($dato_editor_type = $editor_type->fetch()){
		$ec++;
		$editors_table .= "<td id=\"" . $dato_editor_type["elem_id"] . "\"><img src=\"" . $dato_editor_type["img"] . "\" /><span>" . $dato_editor_type["name"] . "</span></td>";
		if ($ec < $editor_type->rowCount()){
			$editors_table .= "<td class=\"separator\"></td>";
		}
	}
}
$content_body = <<<Add_choice
<script type="text/javascript">
$.ajaxSetup({
	error: function(xhr, status, error) {
		if(status.length > 0 && error.length > 0){
			alert("An AJAX error occured: " + status + "\nError: " + error);
			loader("", "hide");
			apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/upload_128_ccc.png\" /></td><td><h1>Finestra di upload</h1><br />Si sta per caricare un file all'interno del Sistema ed è necessaria una conferma da parte dell'utente.<br />Apro la finestra di scelta del file ora?</td></tr></table>", {"confirm": true}, function(r) {
				if (r){
					$(".delete > button").click();
					document.getElementById('input_file_upload').click();
				}
			});
		}
	}
});

function back_to_choice(){
	var last_bread_a = $("#breadcrumb").find("li.back a").html();
	
	$("#add_feed_form").fadeOut(900, function(){
		$("#breadcrumb").find("li.last").remove();
		$("#breadcrumb").find("li.back").html(last_bread_a);
		$("table.menu").fadeIn(600);
	});
}
function load_form(form_type){
	var loader_msg = "";
	switch (form_type){
		case "single":
			loader_msg = "Creazione del modulo per l'inserimento<br />Attendere...";
			break;
		case "feeds_page":
			loader_msg = "Creazione del modulo per l'inserimento del link<br />Attendere...";
			break;
		case "file":
			loader_msg = "Creazione del modulo per il caricamento del file<br />Attendere...";
			break;
	}
	loader(loader_msg, "show");
	$.get("common/include/funcs/_ajax/EditoRSS/load_form.php", {type: form_type},
	function(data) {
		var last_bread = $("#breadcrumb").find("li:last-child").html();
		
		$("table.menu").fadeOut(900, function(){
			$("#breadcrumb").find("li:last-child").html("<a href=\"javascript: void(0);\" onclick=\"back_to_choice()\">" + last_bread + "</a>").addClass("back");
			switch (form_type){
				case "single":
					$("#breadcrumb").find("ul").append("<li class=\"last\">Singolo feed</li>");
					break;
				case "feeds_page":
					$("#breadcrumb").find("ul").append("<li class=\"last\">Pagina con elenco di feeds</li>");
					$("#rss_uri").focus();
					break;
				case "file":
					$("#breadcrumb").find("ul").append("<li class=\"last\">Da file</li>");
					$("#fileupload_loading").fadeOut(450);
					break;
			}
		});
		$("#add_feed_form").html(data).fadeIn(600);
		loader("", "hide");
	});
}
function show_end_message(msg){
	apprise(msg, {'verify': true}, function(r){
		if (r){
			$("#loader_message").text("Avvio la prima scansione");
			setTimeout(function() {
				$("#loader_message").html('L\'automazione &egrave; stata avviata<br />Se lo si vuole &egrave; possibile attendere nella <a style="color: #333 !important; text-decoration: underline;" href="./EditoRSS/News">pagina delle news</a>');
			}, 5000);
			$.get("common/include/funcs/_cron/automation.php", {force_run: "true", type: "editorss", user: "$decrypted_user"},
			function (data){
				if (data){
					var response = data.split(" :: ");
					if (response[0] == "STARTED"){
						$("#loader_message").text("Scansione avviata");
						loader("", "hide");
						apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/recent_changes_128_ccc.png\" /></td><td><h1>News in fase di scansione</h1><br />Le news relative ai feed appena inseriti sono in fase di scansione, al termine della quale verrà inviata un'e-mail di conferma</td></tr></table>", {"noButton": false}, function(r){
							if(r){
								loader("Redirezionamento", "show");
								window.location = ("{ABSOLUTE_PATH}EditoRSS/Feeds");
							}
						});
					}
				}
			});
		} else {
			loader("", "hide");
		}
	});
	$(".delete > button").click();
	back_to_choice();
}
function check_if_theres_rss(e, f, t){
	if (f == undefined || f == null){
		if (t == undefined || t == null){
			rss_title = $("#rss_title").val();
			rss_uri = $("#rss_uri").val();
		} else {
			rss_title = $(".rss_title").val();
			rss_uri = $(".rss_uri").val();
		}
	} else {
		rss_title = f.rss_title.value;
		rss_uri = f.rss_uri.value;
	}
	if(rss_uri.length > 7){
		if (e == "test"){
			loader("Controllo il feed", "show");
			$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: e, user: "{DECRYPTED_USER}", uri: rss_uri},
			function(dato) {
				if(dato != null){
					if (dato.response == "invalid uri"){
						apprise("L'indirizzo inserito non è un feed valido", function(r){
							if (r){
								$("#rss_uri").focus();
							}
						});
						loader("", "hide");
					} else {
						if ($("#rss_title").val() == ""){ $("#rss_title").val(dato.title); }
						$("#rss_description").html(dato.description).attr("disabled", false);
						if (dato.category != null){
							for(cc = 0; cc < dato.category.length; cc++){
								new_group = dato.category[cc];
								$("#group").tagit("createTag", new_group);
							}
						}
						if (dato.tags != null){
							if (dato.tags.length > 0){
								for(tt = 0; tt < dato.tags.length; tt++){
									new_tag = dato.tags[tt];
									$("#tag").tagit("createTag", new_tag);
								}
							}
						}
						loader("", "hide");
					}
				}
			}, "json");
		} else {
			if (e == "check_page"){
				loader("interpretazione dell'elenco dei links", "show");
				$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: e, user: "{DECRYPTED_USER}", uri: rss_uri},
				function(json, textStatus) {
					if (json != "invalid uri"){
						var finded_feeds = json.uri.length-1;
						var the_id = -1;
						var undone_counter = 0;
						var done_counter = 0;
						var total_counter = 0;
						/*var total_resources = 0;*/
						var c = 0;
						var d = 0;
						
						$.each(json.uri, function(i, object) {
							the_id++;
							
							var url = json.uri[the_id].url;
							var finded_feeds_txt = "";
							var finded_feed_txt = "";
							var releved = "";
							var report_txt = "";
							var title = "";
							var description = "";
							var group = "";
							var tag = "";
							var automation_action = "";
							
							loader(json.uri.length + " feeds trovati<br />Elaborazione in corso...", "show");
							if (finded_feeds == 1){
								finded_feeds_txt = finded_feeds + " link";
							} else {
								finded_feeds_txt = finded_feeds + " links";
							}
							$("#loader_message").html("Trovati " + finded_feeds_txt + "<br /><span id=\"valid_feeds_finded\"></span>");
							$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: "test", user: "{DECRYPTED_USER}", uri: json.uri[the_id].url},
							function(dato) {
								if(dato != null){
									if (dato.response != "invalid uri"){
										if (dato.valid_resources > 0){
											done_counter++;
											
											title = dato.title;
											group = dato.category;
											tag = dato.tags;
											automation_action = "http://airs.inran.it/common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php?type=save&user=" + USER + "&uri=" + url;
											if (done_counter == 0){
												finded_feed_txt = "nessun feed valido rilevato";
											} else if (done_counter == 1){
												finded_feed_txt = done_counter + " feed valido";
												releved = " rilevato";
												report_txt = "È stato trovato " + finded_feed_txt + " su " + finded_feeds + " collegamenti.";
											} else {
												finded_feed_txt = done_counter + " feeds validi";
												releved = " rilevati";
												report_txt = "Sono stati trovati " + finded_feed_txt + " su " + finded_feeds + ".";
											}
											$("#valid_feeds_finded").text(finded_feed_txt + releved);
											
											/*total_resources += dato.valid_resources;*/
											if (done_counter == 0){
												show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/cancel_128_ccc.png\" /></td><td><h1>Nessun feed rilevato</h1><br />La scansione del file non ha prodotto risultati accettabili e pertanto non è stato possibile procedere con l'operazione.<br />È possibile che non sia stato rispettato lo schema della tabella nel file csv, in tal caso controlla il file e riprova nuovamente</td></tr></table>");
											} else {
												if ($("#origin_form").value == ""){
													var origin_form = "page_feed";
												}
												$.get("common/include/funcs/_ajax/EditoRSS/add_feed.php", {rss_group: group, rss_title: title, rss_description: dato.description, rss_tag: tag, rss_uri: url, valid_resources: dato.valid_resources, origin: origin_form, automation: "on", action: automation_action, automation_cadence: "daily", decrypted_user: USER},
												function(data){
													var response = data.split(":");
													if (response[0] == "error"){
														/* Inserire il comportamento*/
													} else {
														if (response[0] == "added" || response[0] == "edited"){
															c++;
															if(c == done_counter){
																d++;
																if (d == 1){
																	show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Feeds salvati con successo.</h1><br />" + report_txt + "<br /><br />Le relative news verranno archiviate ogni giorno alla mezzanotte.<br /><b>Si vuole avviare ora la prima scansione?</b></td></tr></table>");
																}
															}
														} else {
															/* Inserire il comportamento*/
														}
													}
												});
											}
										}
									}
								}
							}, "json");
						});
					}
				}, "json");
			} else {
				loader("interpretazione del feed", "show");
				$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: e, user: "{DECRYPTED_USER}", uri: rss_uri},
				function(dato) {
					if(dato != null){
						if (dato >= 1){
							$("#valid_resources").val(dato);
							$("#action").val("http://airs.inran.it/common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php?type=" + e + "&user={DECRYPTED_USER}&uri=" + rss_uri);
							/* Salva i dati nel primo form*/
							$.post("common/include/funcs/_ajax/EditoRSS/add_feed.php", $("#add_feed_group").serialize(),
							function(data){
								var response = data.split(":");
								if (response[0] == "error"){
									apprise(response[1]);
									loader("", "hide");
								} else {
									if (response[0] == "added" || response[0] == "edited"){
										$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: "save", id: response[1], user: "{DECRYPTED_USER}", uri: rss_uri},
										function(dato) {
											if (dato == "added"){
												apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Dati salvati con successo.</h1></td></tr></table>", {'animate': true}, function(r){
													if (r){
														loader("Redirezionamento", "show");
														window.location = ("{ABSOLUTE_PATH}EditoRSS/Feeds");
													}
												});
											} else {
												apprise(data);
												loader("", "hide");
											}
										});
									} else {
										apprise(data);
										loader("", "hide");
									}
								};
							});
						} else {
							var warn = "";
							switch (dato){
								case "no get":
									warn = "Si è verificato un problema di rete";
									break;
								case "invalid uri":
									warn = "L'indirizzo inserito non è un feed valido.<br /><br />È possibile <a href=\"" + rss_uri + "\" target=\"_blank\">visualizzarlo in un'altra pagina</a>";
									break;
								default:
									alert(data);
									break;
							}
							show_warn(warn, "rss_uri");
							loader("", "hide");
						}
					}
				});
			}
		}
	}
	return false;
}
$(document).ready(function() {
	$("#single").click(function(){ load_form("single"); });
	$("#file").click(function(){ load_form("file"); });
	$("#feeds_page").click(function(){ load_form("feeds_page"); });
});
</script>
<table cellpadding="10" cellspacing="10" class="menu">
	<tr>
		$editors_table
	</tr>
</table>
<div id="add_feed_form" style="display: none;"></div>
<div id="parsed_feed_resume" style="display: none;"></div>
Add_choice;

require_once("common/include/conf/replacing_object_data.php");
?>