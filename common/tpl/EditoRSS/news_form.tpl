<?php
/**
* Generates form for edit news page
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

require_once("common/include/funcs/_converti_data.php");
$news = $pdo->query("select * from editorss_feeds_news where `id` = '" . addslashes($GLOBALS["page_q"]) . "'");
$the_id = $_GET["q"];

if ($news->rowCount() > 0){
	while ($dato_news = $news->fetch()){
		$news_title = stripslashes($dato_news["title"]);
		$news_description = stripslashes($dato_news["description"]);
		$news_date = converti_data(date("D, d M Y", strtotime($dato_news["date"])), "it", "month_first");
		$news_link = $dato_news["link"];
		$news_tag = $dato_news["tags"];
			$li_tag = explode(",", $news_tag);
			foreach ($li_tag as $tag){
				$news_li_tag .= "<li>" . $tag . "</li>";
			}
		
		$content_title = $news_title;
		$content_subtitle = "Modifica della notizia indicizzata";
	}
}
$content_body = <<<News_form
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/skins/simple/style.css" />
<link rel="stylesheet" type="text/css" href="{ABSOLUTE_PATH}common/js/markitup/sets/wiki/style.css" />
<script type="text/javascript" src="{ABSOLUTE_PATH}common/js/markitup/jquery.markitup.js"></script>
<link rel="stylesheet" href="{ABSOLUTE_PATH}common/js/jquery-ui-1.8.14.custom/css/custom-theme/jquery-ui-1.8.14.custom.css" id="theme">
<script src="{ABSOLUTE_PATH}common/js/jquery_tag-it/tag-it.js" type="text/javascript" charset="utf-8"></script>
<link href="{ABSOLUTE_PATH}common/js/jquery_tag-it/jquery.tagit.css" rel="stylesheet" type="text/css">
<link href="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.css" rel="stylesheet" type="text/css">
<script src="{ABSOLUTE_PATH}common/js/jquery_timepicker/jquery-ui-timepicker-addon.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
function show_warn(warn, subj){
	if (warn.length > 0){
		apprise(warn, {'textOk': 'Ok'}, function(r){
			if (r){
				$("#" + subj).attr(warn);
				$("#" + subj).focus();
				$("#" + subj).addClass("error");
				
				$("#" + subj).live('keydown blur', function(){
					$("#" + subj).removeClass("error");
				});
			}
		});
	}
}
function save_news(){
	var rss_uri = $("#link_1").val();
	if(rss_uri.length > 7){
		loader("Salvataggio della news", "show");
		$.post("common/include/funcs/_ajax/EditoRSS/add_news_feeds.php", $("#news_form").serialize(),
		function(data){
			var response = data.split(":");
			if (response[0] == "error"){
				apprise(response[1]);
				loader("", "hide");
			} else {
				if (response[0] == "added" || response[0] == "edited"){
					apprise("<table cellpadding=\"10\" cellspacing=\"0\" style=\"width: 100%;\"><tr><td><h1>Dati salvati con successo.</h1></td><td><img src=\"common/media/img/accept_128_ccc.png\" /></td></tr></table>", {'animate': true}, function(r){
						if (r){
							loader("Redirezionamento", "show");
							window.location = ("{ABSOLUTE_PATH}EditoRSS/News/$the_id");
						}
					});
				} else {
					apprise(data);
					loader("", "hide");
				}
			};
		});
	}
	return false;
}
$('.noWarn').click(function() { $('body').removeAttr('onbeforeunload'); });

$(document).ready(function() {
	myWikiSettings = {
		nameSpace: "wiki",
		previewParserPath: "~/templates/preview.php?page={PAGE}&user={DECRYPTED_USER}",
		previewAutoRefresh: true,
		markupSet:  [
			{name:'Grassetto', key:'B', openWith:"'''", closeWith:"'''", className:'bold'}, 
			{name:'Corsivo', key:'I', openWith:"''", closeWith:"''", className:'italic'}, 
			{name:'Sottolineato', key:'U', openWith:'__', closeWith:'__', className:'underline'}, 
			{name:'Barrato', key:'S', openWith:'@@--- ', closeWith:' @@', className:'strokethrough'}, 
			{separator:'---------------' },
			{name:'Titolo 1', key:'1', openWith:'= ', closeWith:' =', className:'h1'},
			{name:'Titolo 2', key:'2', openWith:'== ', closeWith:' ==', className:'h2'},
			{name:'Titolo 3', key:'3', openWith:'=== ', closeWith:' ===', className:'h3'},
			{name:'Titolo 4', key:'4', openWith:'==== ', closeWith:' ====', className:'h4'},
			{name:'Titolo 5', key:'5', openWith:'===== ', closeWith:' =====', className:'h5'},
			{separator:'---------------' }, 
			{name:'Elenco puntato', openWith:'(!(* |!|*)!)', className:'ul'}, 
			{name:'Elenco numerato', openWith:'(!(# |!|#)!)', className:'ol'}, 
			{separator:'---------------' },
			{name:'Crea tabella', className:'tablegenerator', placeholder:"Inserisci del testo qui",
				replaceWith:function(h) {
					var cols = prompt("Inserire il numero di colonne"),
						rows = prompt("Inserire il numero di righe"),
						html = "||";
					if (h.altKey) {
						for (var c = 0; c < cols; c++) {
							html += "! [![TH"+(c+1)+" text:]!]";
						}	
					}
					for (var r = 0; r < rows; r++) {
						if (r > 0){
							html += "||\\n||";
						}
						for (var c = 0; c < cols; c++) {
							if (c > 0){
								html += "||";
							}
							html += (h.placeholder||"");
						}
					}
					html += "||";
					return html;
				},
			className:'tablegenerator'},
			{name:'Immagine', key:'P', replaceWith:'[![Url:!:http://]!] [![name]!]', className:'image'},
			{name:'Collegamento libero', openWith:'(([![Collegamento:!:]!]|', closeWith:'))', placeHolder:'Testo del collegamento', className:'internal_link'},
			{name:'Collegamento InterWiki', openWith:'[[![Canale wiki:!:Wikipedia]!]:[![Lingua (sigla ISO_3166-1):!:it]!]:', closeWith:']', placeHolder:'Testo del collegamento', className:'interwiki_link'},
			{name:'Collegamento esterno', openWith:'[[![URI (Uniform Resource Locator):!:http://]!] ', closeWith:']', placeHolder:'Testo del collegamento', className:'external_link'},
			{separator:'---------------' },
			{name:'Citazione', openWith:'(!(> |!|>)!)', className:'quote'},
			{name:'Codice', openWith:'(!(<code type="[![Linguaggio:!:php]!]">\\n|!|<pre>)!)', closeWith:'(!(\\n</code>|!|</pre>)!)', className:'code'}, 
			{separator:'---------------' },
			{name:'Anteprima', call:'preview', className:'preview'}
		]
	};
	$('#description').markItUp(myWikiSettings);
	$.datepicker.regional['it'] = {
		closeText: 'Chiudi',
		prevText: '« Precedente',
		nextText: 'Successivo »',
		currentText: 'Oggi',
		monthNames: ['Gennaio','Febbraio','Marzo','Aprile','Maggio','Giugno','Luglio','Agosto','Settembre','Ottobre','Novembre','Dicembre'],
		monthNamesShort: ['Gen','Feb','Mar','Apr','Mag','Giu','Lug','Ago','Set','Ott','Nov','Dic'],
		dayNames: ['Domenica','Luned&#236','Marted&#236','Mercoled&#236','Gioved&#236','Venerd&#236','Sabato'],
		dayNamesShort: ['Dom','Lun','Mar','Mer','Gio','Ven','Sab'],
		dayNamesMin: ['Do','Lu','Ma','Me','Gio','Ve','Sa'],
		dateFormat: 'D, dd M yy',
		firstDay: 1,
		autoSize: true,
		isRTL: false
	};
	$.datepicker.setDefaults($.datepicker.regional['it']);
	$(".datepicker").datepicker();
	$(".timepicker").timepicker({
		timeFormat: "hh:mm",
		stepHour: 1,
		stepMinute: 1,
		hourGrid: 5,
		minuteGrid: 10
	});
	
	$("#tag").tagit({
		fieldName: "tags",
		singleField: true,
		singleFieldNode: $('#inputTag'),
		removeConfirmation: true,
		allowSpaces: true,
		tagSource: function(search, showChoices) {
			var that = this;
			$.ajax({
				url: "common/include/funcs/_ajax/get_existing_tags.php",
				data: search,
				dataType: "json",
				success: function(choices) {
					showChoices(that._subtractArray(choices, that.assignedTags()));
				}
			});
		}
	}).click(function(){
		$(this).css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	}).find("input").blur(function(){
		$("#tag").css({
			"border": "#ccc 1px solid",
			"box-shadow": "none"
		});
	}).focus(function(){
		$("#tag").css({
			"border": "#999 1px solid",
			"box-shadow": "0 0 9px #ccc"
		});
	});
	$("#link_1").blur(function(){
		check_uri("test");
	});
});
</script>
<input type="hidden" id="results_index" value="" />
<div id="add_feed_form">
	<form action="" method="post" id="news_form" onsubmit="return save_news(); return false;">
		<input type="hidden" name="decrypted_user" value="$decrypted_user" />
		<input type="hidden" id="action" name="action" value="" />
		<input type="hidden" id="counter" name="counter" value="1" />
		<table cellpadding="0" cellspacing="0" style="width: 100%;" id="content_editor">
			<tr>
				<td>
					<fieldset>
						<legend class="edit">News <acronym title="Really Simple Syndication">RSS</acronym></legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="url" id="link_1" name="link_1" placeholder="Indirizzo della news" style="width: 99%;" value="$news_link" />
								</td>
							</tr>
							<tr><td>&nbsp;</td></tr>
							<tr>
								<td>
									<input type="text" id="title_1" name="title_1" placeholder="Titolo della news" style="width: 99%;" value="$news_title" />
								</td>
							</tr>
							<tr>
								<td>
									<textarea name="description_1" id="description" placeholder="Descrizione della news">$news_description</textarea>
								</td>
							</tr>
							<tr>
								<td>
									Data di creazione: <input type="date" class="datepicker" id="date_1" name="date_1" value="$news_date" />
								</td>
							</tr>
						</table>
					</fieldset>
					<br />
					<fieldset>
						Aggiungi delle parole chiave per poter rintracciare questa news più facilmente.
						<legend class="label">Tag</legend>
						<table cellspacing="5" cellpadding="5" style="width: 100%;">
							<tr>
								<td>
									<input type="hidden" id="inputTag" name="tags_1" value="$rss_tag"  />
									<ul id="tag">$news_li_tag</ul>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>
				<td>
					<span style="float: left;"><a href="$news_link" target="_blank" title="Vedi la news in una nuova finestra">Vedi la pagina della news</a></span>
					<input type="submit" name="save_feed_btn" id="save_feed_btn" value="Salva" />
				</td>
			</tr>
		</table>
	</form>
</div>
<div id="contenuto"></div>
News_form;

require_once("common/include/conf/replacing_object_data.php");
?>