<?php
/**
* Generates "mail" and "export" toolbar
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
* @package	AIRS
* @author	Alessandro Gubitosi <gubi.ale@gotanotherway.com>
* @link		http://airs.inran.it/
*
* @SLM_is_core	true
* @SLM_status	ok
*/

function toolbar($export_file, $database, $table, $filename = "", $return = ""){
	$user = $GLOBALS["decrypted_user"];
	$title = ucfirst(str_replace("_", " ", $filename));
	$page_uri = $GLOBALS["page_uri"];
	if (strlen($filename) == 0){
		$filename = $GLOBALS["page"] . "__airs-" . date("d-m-Y");
	} else {
		$filename .=  "__airs-" . date("d-m-Y");
	}
	$pdo = db_connect("");
	$user_mail = $pdo->query("select * from `airs_users` where `username` = '" . addslashes($GLOBALS["decrypted_user"]) . "'");
	if($user_mail->rowCount() > 0){
		while($dato_user = $user_mail->fetch()){
			$user_mail_txt = $dato_user["email"];
			$user_mail_txt_full = $dato_user["name"] . " " . $dato_user["lastname"] . " <" . $dato_user["email"] . ">";
		}
	}
	$btns_table = <<<Btns
	<h1 style="text-align: center;">Selezionare il formato desiderato</h1>
	<br />
	<table id="table_buttons" cellspacing="10" cellpadding="5">
		<tr>
			<td>
				<button onclick="exp(\'pdf\', \'\')">
					<img src="common/media/img/document_sans_128_ccc.png" />
					<span>FILE <acronym title="Portable Document Format">PDF</acronym></span>
				</button>
			</td>
			<td>
				<button onclick="exp(\'txt\', \'\')">
					<img src="common/media/img/document_text_128_ccc.png" />
					<span>FILE DI TESTO</span>
				</button>
			</td>
			<!--
			<td>
				<button onclick="exp(\'csv\', \'\');">
					<img src="common/media/img/xml_document_128_ccc.png" />
					<span>FILE <acronym title="Comma-Separated Values">CSV</acronym></span>
				</button>
			</td>
			-->
			<td>
				<button onclick="exp(\'xls\', \'\');">
					<img src="common/media/img/xml_document_128_ccc.png" />
					<span>FILE <acronym title="Office Spreadsheet">XLS(X)</acronym></span>
				</button>
			</td>
			<td>
				<button onclick="exp(\'rdf\', \'\');">
					<img src="common/media/img/rdf_document_128_ccc.png" />
					<span>FILE <acronym title="Resource Description Framework">RDF</acronym></span>
				</button>
			</td>
		</tr>
	</table>
Btns;
	$javascript_functions = <<<Javascript_functions
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
	
	function exp(filetype, send_to){
		if($("#selected_row").length > 0){
			var download_selected_row = "&ids=" + $("#selected_row").val();
			var post_selected_row = $("#selected_row").val();
		} else {
			var download_selected_row = "";
			var post_selected_row = "";
		}
		if (filetype == "mail"){
			$.post("common/include/funcs/_ajax/$export_file", {filename: "$filename", format: filetype, page_uri: "$page_uri", db: "$database", table: "$table", title: "$title", save: "true", dest: send_to, ids: post_selected_row}, function(data){
				if(data == "NO"){
					apprise("<h1>Errore invio mail</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/mail_cancel_128_ccc.png\" /></td><td class=\"appriseInnerContent\">Si è verificato un errore nell'invio della mail<br /></td></tr></table>", {"noButton": false});
				} else {
					apprise("<h1>Invio mail riuscito</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/mail_accept_128_ccc.png\" /></td><td class=\"appriseInnerContent\">E-mail inviata con successo!<br /></td></tr></table>", {"noButton": false});
				}
			});
		} else {
			$.download('common/include/funcs/_ajax/$export_file', 'user=$user&filename=$filename&format=' + filetype + '&page_uri=$page_uri&db=$database&table=$table&title=$title&save=true&to=' + $("#send_to").val() + download_selected_row);
			$("#send_to").val("");
			close_apprise();
		}
	}
Javascript_functions;

	switch($return){
		case "user_mail_txt":
			return $user_mail_txt;
			break;
		case "user_mail_txt_full":
			return $user_mail_txt_full;
			break;
		case "btns_table":
			return $btns_table;
			break;
		case "javascript_functions":
			return $javascript_functions;
			break;
		default:
			$toolbar = <<<Toolbar
			<script type="text/javascript">
			$javascript_functions
			$(document).ready(function(){
				$("#component_toolbar > a").click(function(){
					$("#send_to").val("");
					switch($(this).attr("class")){
						case "export":
							apprise('$btns_table', {"animate": "true", "noButton": "true"});
							break;
						case "mail":
							apprise("<h1>Esporta via e-mail</h1><table cellspacing=\"10\" cellpadding=\"10\" style=\"width: 100%;\"><tr><td style=\"width: 128px\"><img src=\"common/media/img/mail_run_128_ccc.png\" /></td><td valign=\"top\" class=\"appriseInnerContent\">Inserisci l'indirizzo in cui si desidera ricevere la mail<br /></td></tr></table>", {"input": "$user_mail_txt"}, function(r){
								if(r){
									if (r == "$user_mail_txt"){
										$("#send_to").val("$user_mail_txt_full");
										var to = "$user_mail_txt_full";
									} else {
										var to = r;
									}
									/* Invio con allegato ~ da finire di sviluppare */
									/*
									apprise('$btns_table', {"animate": "true", "noButton": "true"}, function(s){
										if(s){
										} else {
											$("#send_to").val("");
										}
									});
									*/
									exp("mail", to);
								} else {
									$("#send_to").val("");
								}
							});
							break;
					}
				});
			});
			</script>
			<div id="component_toolbar">
				<input type="hidden" id="send_to" value="" />
				<a class="export" href="javascript:void(0);" title="Esporta"></a>
				<a class="mail" href="javascript:void(0);" title="Notifica via e-mail"></a>
			</div>
Toolbar;
			break;
	}
	return $toolbar;
}
?>