<?php
/**
* Generates chronology template
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

$rows_per_page = 10;

$content_subtitle = "Cronologia della pagina";
$content_body = <<<Chronology
<script type=text/javascript>
function show_details(id){
	var content = $('.details_' + id + '_content').html();
	var previous_id = parseFloat(id);
	if (content.length == 0){
		$.get('common/include/funcs/_ajax/cronology_detail.php', {the_id: previous_id}, function(data){
			$('.details_' + id + '_content').html(data).parent().slideDown(300);
		}, 'html');
	} else {
		$('.details_' + id + '_content').parent().slideUp(300, function(){ $('.details_' + id + '_content').html(''); });
	}
}
function show_formatted(id){
	var formatted_btn = $('.formatted_btn_' + id);
	var content = $('.details_' + id + '_content');
	var previous_id = parseFloat(id) - 1;
	if (formatted_btn.hasClass('selected')){
		formatted_btn.removeClass('selected');
		$('#td_' + id).attr('title', 'Sommario dei cambiamenti');
		$('#content_body_' + id).html($('#stored_content_' + id).val());
	} else {
		$('#stored_content_' + id).val($('#content_body_' + id).html());
		formatted_btn.addClass('selected');
		$('#td_' + id).attr('title', 'Sommario dei cambiamenti (testo formattato)');
		$('#content_body_' + id).html('<iframe border="0" title="Sommario dei cambiamenti (testo formattato)" src="common/include/funcs/_ajax/format_wiki_content.php?type=chronology&id=' + id + '"></iframe>');
	}
}
function show_monospaced(id){
	var monospaced_btn = $('.monospaced_btn_' + id);
	var content = $('.details_' + id + '_content');
	if (monospaced_btn.hasClass('selected')){
		monospaced_btn.removeClass('selected');
		content.removeClass('monospaced');
		$('#td_' + id).attr('title', 'Sommario dei cambiamenti');
	} else {
		monospaced_btn.addClass('selected');
		content.addClass('monospaced');
		$('#td_' + id).attr('title', 'Sommario dei cambiamenti (testo monospaziato)');
	}
}
function send_via_mail(id){
	apprise('<table cellpadding=5 cellspacing=5 style=width: 100%;><tr><td><img src=common/media/img/questionmark_128_ccc.png /></td><td valign=top><b>Invio dei dati della versione via e-mail</b><br />Vuoi che ti vengano inviati via e-mail?</td></tr></table>', {'verify': true, 'textYes': 'Si'}, function(r){
	if (r){
		$.get('common/include/funcs/_ajax/send_content_via_mail.php', {where: 'chronology', what: 'body', the_id: id}, function(data){
			if(data == 'ok'){
				apprise('<b>Testo inviato!</b><br />I dati relativi a questa versione sono stati inviati al tuo indirizzo di posta');
			}
		});
	}});
}
function restore(id){
	apprise('<table cellpadding=5 cellspacing=5 style=width: 100%;><tr><td><img src=common/media/img/questionmark_128_ccc.png /></td><td valign=top><b>Ripristino di una versione precedente</b><br />Sei sicuro di voler ripristinare questa versione precedente?<br /><br />La versione attuale rimarr&agrave; comunque salvata nella cronologia,<br />quindi ripristinabile in ogni momento.</td></tr></table>', {'verify': true, 'textYes': 'Si'}, function(r){
	if (r){
		$.get('common/include/funcs/_ajax/restore_previous_chronology.php', {the_id: id, page: '{PAGE_M}', subpage: '{PAGE_ID}', sub_subpage: '{PAGE_Q}'}, function(data){
			if(data == 'ok'){
				location.reload();
			} else {
				alert(data);
			}
		});
	}});
}
function get_chronology(page_name, subpage_name, sub_subpage_name, rows_number, page_number){
	$.get('common/include/funcs/_ajax/get_chronology.php', {page: page_name, subpage: subpage_name, sub_subpage: sub_subpage_name, rows_per_page: rows_number, page_no: page_number}, function(data){
		$('#evaluating_version > div').slideUp(600, function(){
			$(this).html(data).slideDown(300);
		});
	});
}
$(function(){
	get_chronology('{PAGE_M}', '{PAGE_ID}', '{PAGE_Q}', '{ROWS_PER_PAGE}', '1');
});
</script>
<b><u>Nota:</u></b> Le comparazioni vengono fatte <u>rispetto alla versione attuale</u>
<br />
<br />
<fieldset id=evaluating_version><legend>Seleziona la versione da valutare</legend><div><p class="load"></p></div></fieldset>
Chronology;
require_once("common/include/conf/replacing_object_data.php");
$content_body = str_replace("{ROWS_PER_PAGE}", $rows_per_page, $content_body);
?>