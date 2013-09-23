/*
 * jQuery File Upload Plugin JS Example 5.0.2
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://creativecommons.org/licenses/MIT/
 */

/*jslint nomen: true */
/*global $ */

$(function () {
	'use strict';
	// Initialize the jQuery File Upload widget:
	$("#fileupload_loading").fadeOut(450, function(){
		$('#fileupload').fileupload({
			maxNumberOfFiles: 1,
			beforeSend: function(e){
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
		}).bind('fileuploaddestroy', function(e, data){
			// Ricarica la pagina alla rimozione del file (visualizza il modulo per la modifica dei metadati)
			window.location.href = window.location.pathname;
		}).fadeIn(720);
		// Load existing files:
		$.getJSON($('#fileupload form').prop('action'), function (files) {
			var fu = $('#fileupload').data('fileupload');
			fu._adjustMaxNumberOfFiles(-files.length);
			fu._renderDownload(files).appendTo($('#fileupload .files')).fadeIn(function () {
				// Fix for IE7 and lower:
				$(this).show();
			});
		});
		// Open download dialogs via iframes,
		// to prevent aborting current uploads:
		$('#fileupload .files a:not([target^=_blank])').live('click', function (e) {
			e.preventDefault();
			$('<iframe style="display:none;"></iframe>').prop('src', this.href).appendTo('body');
		});
	});
});