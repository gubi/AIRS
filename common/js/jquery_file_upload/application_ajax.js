function show_end_message(msg, usr){
	apprise(msg, {"noButton": false}, function(r){
		loader("", "hide");
		loader("", "show");
		$("#loader_message").text("Avvio la prima scansione");
		setTimeout(function() {
			$("#loader_message").html('L\'automazione &egrave; stata avviata<br />Se lo si vuole &egrave; possibile attendere nella <a style="color: #333 !important; text-decoration: underline;" href="./EditoRSS/News">pagina delle news</a>');
		}, 5000);
		$.get("common/include/funcs/_cron/automation.php", {force_run: "true", type: "editorss", user: usr},
		function (data){
			if (data){
				var response = data.split("::");
				if (response[0] == "STARTED "){
					$("#loader_message").text("Scansione avviata");
					loader("", "hide");
					apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/recent_changes_128_ccc.png\" /></td><td><h1>News in fase di scansione</h1><br />Le news relative ai feed appena inseriti sono in fase di scansione.<br />Al suo termine verrà inviata un'e-mail di conferma</td></tr></table>", {"noButton": false}, function(r){
						parent.back_to_choice();
						$(".delete > button").click();
					});
				}
			}
		});
		$(".delete > button").click();
	});
}
$(function () {
	"use strict";
	// Initialize the jQuery File Upload widget:
	$("#fileupload_loading").fadeOut(450, function(){
		if ($("#fileupload_loading").css("display") == "block"){
			$("#fileupload_loading").fadeOut(450);
		}
		$('#fileupload').fileupload({
			autoUpload: true,
			maxNumberOfFiles: 1,
			acceptFileTypes: /(\.|\/)(csv|xml|rss|pdf)$/i,
			beforeSend: function(e, data){
				//check_if_theres_rss("test");
				/*
				// Trovare un modo per poter interrompere il caricamento e farlo riavviare una volta inserita la chiave
				//$(this).stop();
				apprise("<h1>Ibernazione del sistema</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/document_sans_security_128_ccc.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\">La sessione è scaduta e pertanto il sistema si è ibernato.<br />Questo consente un maggiore livello di sicurezza e inoltre permette ad altri utenti di poter operare nel caso tu abbia prenotato la modifica di una pagina.<br /><br />Per poter continuare, è necessario inserire la tua chiave di cifratura nel campo sottostante<br /><a href=\"./Sicurezza/Chiave_di_cifratura\" target=\"_blank\">Maggiori informazioni riguardo alla chiave di cifratura</a><br /></td></tr></table><br /><br />", {"key": true}, function(r){
					$("input, textarea").attr("readonly", "readonly");
					if (r){
						$.get("common/include/funcs/_ajax/re_login.php", {u: USER, k1: $("#ukey").val(), k2: r, page: PAGE_M, subpage: PAGE_ID, sub_subpage: PAGE_Q}, function(data){
							if (data !== "allowed"){
								check_login_expiry(username, v);
							} else {
								$("*").removeAttr('readonly');
								check_login_expiry(username);
							}
						});
					} else {
						
					}
				});
				*/
			}
		}).bind('fileuploadstart', function (e) {
			loader("Caricamento del file...", "show");
		}).bind('fileuploaddone', function (e, data) {
			var c = 0;
			var file_name = data.files[0].name;
			file_name = file_name.replace(/\s+/g, "_");
			
			if ($("#formtype").val() == "xml_file"){
				//var last_bread = $("#breadcrumb").find("li:last-child").html();
				
				loader("Elaborazione del file...", "show");
				$.get("common/include/funcs/_ajax/EditoRSS/get_feeds_from_file.php", {file: file_name, user: USER}, function(json, textStatus){
					if (textStatus = "success"){
						//$("#breadcrumb").find("li:last-child").html("<a href=\"javascript: void(0);\" onclick=\"back_to_choice()\">" + last_bread + "</a>").addClass("back");
						//$("#breadcrumb").find("ul").append("<li class=\"last_last\">Riepilogo scansione file</li>");
						//$("#content_wrapper_title").find("h2").text("Riepilogo scansione del file");
						var finded_feeds = json.file.length;
						var the_id = -1;
						var done_counter = 0;
						var total_counter = 0;
						var total_resources = 0;
						
						$.each(json.file, function(i, object) {
							the_id++;
							//$.each(object, function(property, value) {});
							
							var title = json.file[the_id].nome_feed_rss;
							var url = json.file[the_id].url;
							var group = json.file[the_id].tipo;
							var tag = json.file[the_id].tematica;
							var automation_action = "http://airs.inran.it/common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php?type=save&user=" + USER + "&uri=" + url;
							var finded_feeds_txt = "";
							var finded_feed_txt = "";
							var releved = "";
							var report_txt = "";
							
							loader(json.file.length + " feeds trovati<br />Elaborazione in corso...", "show");
							if (finded_feeds == 1){
								finded_feeds_txt = finded_feeds + " feed";
							} else {
								finded_feeds_txt = finded_feeds + " feeds";
							}
							$("#loader_message").html(finded_feeds_txt + " in totale<br /><span id=\"valid_feeds_finded\"></span>");
							$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {type: "test", user: USER, uri: json.file[the_id].url},
							function(dato) {
								if (dato.response != "invalid uri"){
									done_counter++;
									if (done_counter == 0){
										finded_feed_txt = "nessun feed valido rilevato";
									} else if (done_counter == 1){
										finded_feed_txt = done_counter + " feed valido";
										releved = " rilevato";
										report_txt = "È stato trovato " + finded_feed_txt + " su " + finded_feeds + releved + ".";
									} else {
										finded_feed_txt = done_counter + " feeds validi";
										releved = " rilevati";
										report_txt = "Sono stati trovati " + finded_feed_txt + " su " + finded_feeds + releved + ".";
									}
									$("#valid_feeds_finded").html(finded_feed_txt + releved + "<br /><br /><font style=\"color: #ff0000; font-weight: bold;\">Si prega di non chiudere questa pagina</font>");
									
									total_resources += dato.valid_resources;
									
									if (done_counter == 0){
										show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/cancel_128_ccc.png\" /></td><td><h1>Nessun feed rilevato</h1><br />La scansione del file non ha prodotto risultati accettabili e pertanto non è stato possibile procedere con l'operazione.<br />È possibile che non sia stato rispettato lo schema della tabella nel file csv, in tal caso controlla il file e riprova nuovamente</td></tr></table>", USER);
									} else {
										$.get("common/include/funcs/_ajax/EditoRSS/add_feed.php", {rss_group: group, rss_title: title, rss_description: dato.description, rss_tag: tag, rss_uri: url, valid_resources: dato.valid_resources, origin: "file_feed", automation: "on", action: automation_action, automation_cadence: "daily", decrypted_user: USER},
										function(data){
											var response = data.split(":");
											if (response[0] == "error"){
												apprise(response[1], {"noButton": false}, function(r){
													back_to_choice();
													$(".delete > button").click();
												});
											} else {
												if (response[0] == "added" || response[0] == "edited"){
													c++;
													if(c == done_counter){
														loader("", "hide");
														show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Feeds salvati con successo.</h1><br />" + report_txt + "<br /><br />Le relative news verranno archiviate ogni giorno alla mezzanotte.<br /><b>Verr&agrave; avviata la prima scansione.</b></td></tr></table>", USER);
													}
													/*
													$.get("common/include/funcs/_ajax/EditoRSS/check_save_show_rss.php", {uri: url, type: "save", id: response[1], user: USER},
													function(dato) {
														if (dato == "added" || dato == "edited"){
															done_counter++;
															
															if (done_counter == 1){
																var done_msg = done_counter + " feed";
															} else {
																var done_msg = done_counter + " feeds";
															}
															if (dato == "added"){
																done_msg += " aggiunt";
															} else {
																done_msg += " modificat";
															}
															if (done_counter == 1){
																done_msg += "o";
															} else {
																done_msg += "i";
															}
															$("#loader_message").text(done_msg + "...");
														} else {
															// Inserire il comportamento
														}
													});
													*/
												}
											}
										});
									}
								}
							}, "json");
							if (the_id == finded_feeds){
								//show_end_message("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/accept_128_ccc.png\" /></td><td><h1>Dati salvati con successo.</h1></td></tr></table>", USER);
								//loader("", "hide");
							}
						});
						/*
						$("#add_feed_form").html(data).fadeOut(900, function(){
							$("#parsed_feed_resume").html(data).fadeIn(600);
							check_if_theres_rss("test", null, "-")
						});
						*/
					}
				}, "json");
			}
		}).bind('fileuploadfail', function (e) {
			loader("", "hide");
			document.getElementById('input_file_upload').click();
		}).bind('drop', function (e) {
			var url = $(e.originalEvent.dataTransfer.getData('text/html')).filter('img').attr('src');
			if (url) {
				$.getImageData({
					url: url,
					success: function (img) {
						var canvas = document.createElement('canvas'), file;
						canvas.getContext('2d').drawImage(img, 0, 0);
						if ($.type(canvas.mozGetAsFile) === 'function') {
							file = canvas.mozGetAsFile(PAGE + ".png");
						}
						if (file) {
							$('#fileupload').fileupload('add', {files: [file]});
						}
						console.log(file);
					}
				});
			}
		}).fadeIn(720);
		// Open download dialogs via iframes,
		// to prevent aborting current uploads:
		$('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
			e.preventDefault();
			$('<iframe style="display:none;"></iframe>').prop('src', this.href).appendTo('body');
		});
	});
});
$(document).ready(function(){
	apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><img src=\"common/media/img/upload_128_ccc.png\" /></td><td><h1>Finestra di upload</h1><br />Si sta per caricare un file all'interno del Sistema ed è necessaria una conferma da parte dell'utente.<br />Apro la finestra di scelta del file ora?</td></tr></table>", {"confirm": true}, function(r) {
		if (r){
			document.getElementById('input_file_upload').click();
		}
	});
});